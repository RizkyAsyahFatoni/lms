<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleAttachment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * Tampilkan daftar seluruh artikel & berkas di panel admin
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search   = trim($request->input('q', ''));
        $category = $request->input('category', 'all');
        $status   = $request->input('status', 'all');

        $query = Article::with(['user', 'attachments'])
            ->withCount('attachments')
            ->search($search);

        // Jika user adalah Admin OPD (bukan super_admin / bkpsdm), batasi hanya melihat artikel OPD mereka
        if ($user->role === 'admin_opd' && $user->opd_name) {
            $query->where('opd_name', $user->opd_name);
        }

        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }

        if ($status !== 'all') {
            if ($status === 'published') {
                $query->where('is_published', true);
            } elseif ($status === 'draft') {
                $query->where('is_published', false);
            }
        }

        $articles = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'       => Article::count(),
            'published'   => Article::where('is_published', true)->count(),
            'draft'       => Article::where('is_published', false)->count(),
            'attachments' => ArticleAttachment::count(),
        ];

        return view('admin.articles.index', compact('articles', 'stats', 'search', 'category', 'status'));
    }

    /**
     * Tampilkan form pembuatan artikel baru
     */
    public function create()
    {
        $user = Auth::user();
        return view('admin.articles.create', compact('user'));
    }

    /**
     * Simpan artikel baru beserta berkas lampiran
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'title'             => 'required|string|max:255',
            'category'          => 'required|string',
            'excerpt'           => 'nullable|string|max:1000',
            'content'           => 'required|string',
            'cover_image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'opd_name'          => 'nullable|string|max:255',
            'author_name'       => 'nullable|string|max:255',
            'is_published'      => 'nullable|boolean',
            
            // Validasi attachment array
            'attachments.*.title'           => 'nullable|string|max:255',
            'attachments.*.attachment_type' => 'nullable|string',
            'attachments.*.file'            => 'nullable|file|mimes:pdf,ppt,pptx,doc,docx,xls,xlsx,zip,rar|max:20480', // Maks 20MB
            'attachments.*.external_url'    => 'nullable|url|max:500',
        ]);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('articles/covers', 'public');
        }

        $isPublished = $request->boolean('is_published', true);

        $article = Article::create([
            'user_id'      => $user->id,
            'title'        => $request->title,
            'category'     => $request->category,
            'opd_name'     => $request->opd_name ?: ($user->opd_name ?? 'BKPSDM Kabupaten Berau'),
            'author_name'  => $request->author_name ?: $user->name,
            'excerpt'      => $request->excerpt,
            'content'      => $request->content,
            'cover_image'  => $coverPath,
            'is_published' => $isPublished,
            'published_at' => $isPublished ? now() : null,
        ]);

        // Simpan Lampiran jika ada
        $attachmentsData = $request->input('attachments', []);
        $filesData       = $request->file('attachments', []);

        foreach ($attachmentsData as $index => $item) {
            $attachTitle = trim($item['title'] ?? '');
            $attachType  = $item['attachment_type'] ?? 'dokumen';
            $externalUrl = trim($item['external_url'] ?? '');
            $uploadedFile = $filesData[$index]['file'] ?? null;

            // Jika ada file upload atau ada link external
            if ($uploadedFile || !empty($externalUrl)) {
                $filePath = null;
                $fileName = null;
                $fileSize = 0;
                $fileExt  = null;

                if ($uploadedFile) {
                    $originalName = $uploadedFile->getClientOriginalName();
                    $fileExt = strtolower($uploadedFile->getClientOriginalExtension());
                    $fileSize = $uploadedFile->getSize();
                    $filePath = $uploadedFile->store('articles/attachments', 'public');
                    $fileName = $originalName;
                    if (empty($attachTitle)) {
                        $attachTitle = pathinfo($originalName, PATHINFO_FILENAME);
                    }
                }

                if (empty($attachTitle)) {
                    $attachTitle = 'Dokumen Lampiran ' . ($index + 1);
                }

                ArticleAttachment::create([
                    'article_id'      => $article->id,
                    'title'           => $attachTitle,
                    'attachment_type' => $attachType,
                    'file_path'       => $filePath,
                    'file_name'       => $fileName,
                    'file_size'       => $fileSize,
                    'file_extension'  => $fileExt,
                    'external_url'    => $externalUrl ?: null,
                ]);
            }
        }

        ActivityLog::record(
            'Membuat Artikel & Pengetahuan',
            'Berhasil menerbitkan artikel: ' . $article->title . ' dengan ' . $article->attachments()->count() . ' lampiran berkas.'
        );

        return redirect()->route('admin.articles.index')->with('success', 'Artikel dan lampiran berhasil disimpan!');
    }

    /**
     * Form edit artikel
     */
    public function edit(Article $article)
    {
        $article->load('attachments');
        return view('admin.articles.edit', compact('article'));
    }

    /**
     * Perbarui artikel dan kelola lampirannya
     */
    public function update(Request $request, Article $article)
    {
        $user = Auth::user();

        $request->validate([
            'title'             => 'required|string|max:255',
            'category'          => 'required|string',
            'excerpt'           => 'nullable|string|max:1000',
            'content'           => 'required|string',
            'cover_image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'opd_name'          => 'nullable|string|max:255',
            'author_name'       => 'nullable|string|max:255',
            'is_published'      => 'nullable|boolean',

            'new_attachments.*.title'           => 'nullable|string|max:255',
            'new_attachments.*.attachment_type' => 'nullable|string',
            'new_attachments.*.file'            => 'nullable|file|mimes:pdf,ppt,pptx,doc,docx,xls,xlsx,zip,rar|max:20480',
            'new_attachments.*.external_url'    => 'nullable|url|max:500',
        ]);

        $coverPath = $article->cover_image;
        if ($request->hasFile('cover_image')) {
            if ($coverPath && Storage::disk('public')->exists($coverPath)) {
                Storage::disk('public')->delete($coverPath);
            }
            $coverPath = $request->file('cover_image')->store('articles/covers', 'public');
        }

        $isPublished = $request->boolean('is_published', false);

        $article->update([
            'title'        => $request->title,
            'category'     => $request->category,
            'opd_name'     => $request->opd_name ?: $article->opd_name,
            'author_name'  => $request->author_name ?: $article->author_name,
            'excerpt'      => $request->excerpt,
            'content'      => $request->content,
            'cover_image'  => $coverPath,
            'is_published' => $isPublished,
            'published_at' => ($isPublished && !$article->published_at) ? now() : $article->published_at,
        ]);

        // Tambah lampiran baru jika ada yang diinput
        $newAttachmentsData = $request->input('new_attachments', []);
        $newFilesData       = $request->file('new_attachments', []);

        foreach ($newAttachmentsData as $index => $item) {
            $attachTitle = trim($item['title'] ?? '');
            $attachType  = $item['attachment_type'] ?? 'dokumen';
            $externalUrl = trim($item['external_url'] ?? '');
            $uploadedFile = $newFilesData[$index]['file'] ?? null;

            if ($uploadedFile || !empty($externalUrl)) {
                $filePath = null;
                $fileName = null;
                $fileSize = 0;
                $fileExt  = null;

                if ($uploadedFile) {
                    $originalName = $uploadedFile->getClientOriginalName();
                    $fileExt = strtolower($uploadedFile->getClientOriginalExtension());
                    $fileSize = $uploadedFile->getSize();
                    $filePath = $uploadedFile->store('articles/attachments', 'public');
                    $fileName = $originalName;
                    if (empty($attachTitle)) {
                        $attachTitle = pathinfo($originalName, PATHINFO_FILENAME);
                    }
                }

                if (empty($attachTitle)) {
                    $attachTitle = 'Dokumen Lampiran Baru';
                }

                ArticleAttachment::create([
                    'article_id'      => $article->id,
                    'title'           => $attachTitle,
                    'attachment_type' => $attachType,
                    'file_path'       => $filePath,
                    'file_name'       => $fileName,
                    'file_size'       => $fileSize,
                    'file_extension'  => $fileExt,
                    'external_url'    => $externalUrl ?: null,
                ]);
            }
        }

        ActivityLog::record(
            'Memperbarui Artikel',
            'Memperbarui konten artikel: ' . $article->title
        );

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil diperbarui!');
    }

    /**
     * Switch status publish / draft
     */
    public function toggleStatus(Article $article)
    {
        $article->is_published = !$article->is_published;
        if ($article->is_published && !$article->published_at) {
            $article->published_at = now();
        }
        $article->save();

        return back()->with('success', 'Status publikasi artikel berhasil diubah.');
    }

    /**
     * Hapus artikel dan seluruh filenya
     */
    public function destroy(Article $article)
    {
        $user = Auth::user();
        $title = $article->title;

        // Hapus cover image
        if ($article->cover_image && Storage::disk('public')->exists($article->cover_image)) {
            Storage::disk('public')->delete($article->cover_image);
        }

        // Hapus berkas lampiran
        foreach ($article->attachments as $attachment) {
            if ($attachment->file_path && Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }
        }

        $article->delete();

        ActivityLog::record(
            'Menghapus Artikel',
            'Menghapus artikel: ' . $title
        );

        return redirect()->route('admin.articles.index')->with('success', 'Artikel beserta seluruh lampirannya berhasil dihapus.');
    }

    /**
     * Perbarui data lampiran yang sudah ada (judul, jenis dokumen, ganti file atau ganti tautan Google Drive)
     */
    public function updateAttachment(Request $request, ArticleAttachment $attachment)
    {
        $request->validate([
            'title'           => 'required|string|max:255',
            'attachment_type' => 'required|string',
            'external_url'    => 'nullable|url|max:1000',
            'file'            => 'nullable|file|mimes:pdf,ppt,pptx,doc,docx,xls,xlsx,zip,rar|max:20480',
        ]);

        $filePath = $attachment->file_path;
        $fileName = $attachment->file_name;
        $fileSize = $attachment->file_size;
        $fileExt  = $attachment->file_extension;
        $sourceType  = $request->input('source_type', 'keep');
        $externalUrl = trim($request->input('external_url', ''));

        // Jika user memilih unggah berkas baru
        if ($sourceType === 'upload' && $request->hasFile('file')) {
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            $uploadedFile = $request->file('file');
            $originalName = $uploadedFile->getClientOriginalName();
            $fileExt = strtolower($uploadedFile->getClientOriginalExtension());
            $fileSize = $uploadedFile->getSize();
            $filePath = $uploadedFile->store('articles/attachments', 'public');
            $fileName = $originalName;
            $externalUrl = null;
        } elseif ($sourceType === 'link' && !empty($externalUrl)) {
            // Jika beralih atau memperbarui tautan luar (Google Drive / Cloud)
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath = null;
            $fileName = null;
            $fileSize = 0;
            $fileExt  = null;
        } elseif ($sourceType === 'keep') {
            // Mempertahankan berkas fisik atau tautan yang sudah ada
            if ($attachment->external_url) {
                $externalUrl = $attachment->external_url;
            }
        }

        $attachment->update([
            'title'           => $request->title,
            'attachment_type' => $request->attachment_type,
            'file_path'       => $filePath,
            'file_name'       => $fileName,
            'file_size'       => $fileSize,
            'file_extension'  => $fileExt,
            'external_url'    => $externalUrl ?: null,
        ]);

        ActivityLog::record(
            'Memperbarui Lampiran Artikel',
            'Memperbarui lampiran: ' . $attachment->title . ' pada artikel: ' . $attachment->article->title
        );

        return back()->with('success', 'Lampiran "' . $attachment->title . '" berhasil diperbarui!');
    }

    /**
     * Hapus satu berkas lampiran
     */
    public function deleteAttachment(ArticleAttachment $attachment)
    {
        if ($attachment->file_path && Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->delete();

        return back()->with('success', 'Berkas lampiran berhasil dihapus.');
    }
}
