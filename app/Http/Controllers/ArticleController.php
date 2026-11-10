<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleAttachment;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ArticleController extends Controller
{
    /**
     * Tampilkan katalog artikel & repositori pengetahuan
     */
    public function index(Request $request)
    {
        $appSetting = AppSetting::getSettings();

        $search         = trim($request->input('q', ''));
        $category       = $request->input('category', 'all');
        $attachmentType = $request->input('attachment_type', 'all');
        $opdFilter      = $request->input('opd', 'all');

        $query = Article::published()
            ->with(['attachments', 'user'])
            ->withCount('attachments')
            ->search($search)
            ->filterCategory($category)
            ->filterAttachmentType($attachmentType);

        if ($opdFilter && $opdFilter !== 'all') {
            $query->where('opd_name', $opdFilter);
        }

        $articles = $query->latest('published_at')->paginate(9)->withQueryString();

        // Statistik Ringkas Repository
        $stats = [
            'total_articles'    => Article::published()->count(),
            'total_regulations' => ArticleAttachment::where('attachment_type', 'regulasi')->count(),
            'total_guides'      => ArticleAttachment::where('attachment_type', 'panduan')->count(),
            'total_slides'      => ArticleAttachment::where('attachment_type', 'presentasi')->count(),
            'total_downloads'   => ArticleAttachment::sum('downloads_count'),
        ];

        // Daftar OPD kontributor untuk dropdown filter
        $availableOpds = Article::published()
            ->whereNotNull('opd_name')
            ->where('opd_name', '!=', '')
            ->distinct()
            ->pluck('opd_name')
            ->sort()
            ->values();

        // Hitungan per kategori untuk sidebar filter
        $categoryCounts = Article::published()
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        return view('articles.index', compact(
            'articles',
            'stats',
            'search',
            'category',
            'attachmentType',
            'opdFilter',
            'availableOpds',
            'categoryCounts',
            'appSetting'
        ));
    }

    /**
     * Tampilkan detail artikel dan seluruh dokumen attachmentnya
     */
    public function show(string $slug)
    {
        $appSetting = AppSetting::getSettings();

        $article = Article::published()
            ->with(['attachments', 'user'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment views count secara aman
        $article->increment('views_count');

        // Artikel Terkait / Rekomendasi
        $relatedArticles = Article::published()
            ->where('id', '!=', $article->id)
            ->where(function ($q) use ($article) {
                $q->where('category', $article->category)
                  ->orWhere('opd_name', $article->opd_name);
            })
            ->withCount('attachments')
            ->latest('published_at')
            ->limit(4)
            ->get();

        // Hitung jumlah artikel per kategori untuk widget sidebar
        $categoryCounts = Article::published()
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        return view('articles.show', compact('article', 'relatedArticles', 'appSetting', 'categoryCounts'));
    }

    /**
     * Unduh attachment dokumen secara aman & catat statistik unduhan
     */
    public function downloadAttachment(ArticleAttachment $attachment)
    {
        // Pastikan artikel dalam status publish atau user adalah admin
        if (!$attachment->article->is_published && (!auth()->check() || !auth()->user()->isAdmin())) {
            abort(403, 'Dokumen ini belum dipublikasikan.');
        }

        // Increment hit counter download
        $attachment->increment('downloads_count');

        // Jika external url (misal Google Drive / Cloud Link)
        if ($attachment->external_url) {
            return redirect()->away($attachment->external_url);
        }

        // Jika file di storage lokal
        if ($attachment->file_path && Storage::disk('public')->exists($attachment->file_path)) {
            $fullPath = Storage::disk('public')->path($attachment->file_path);
            $downloadName = $attachment->file_name ?: basename($attachment->file_path);

            return response()->download($fullPath, $downloadName);
        }

        return back()->with('error', 'Berkas lampiran tidak ditemukan di penyimpanan server.');
    }
}
