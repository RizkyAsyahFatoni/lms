<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Database\Seeders\CategoryTaxonomySeeder;

class CategoryController extends Controller
{
    /**
     * Tampilkan halaman pengelolaan Kategori (WordPress Style: 2 Kolom Form + Tabel Hirarki).
     */
    public function index(Request $request)
    {
        $search = trim($request->input('q', ''));
        $parentFilter = $request->input('parent', 'all');

        // Query categories
        $categoriesQuery = Category::with(['parent', 'subCategories'])
            ->withCount(['courses', 'subCourses']);

        if ($search) {
            $categoriesQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        if ($parentFilter === 'main') {
            $categoriesQuery->whereNull('parent_id');
        } elseif ($parentFilter === 'sub') {
            $categoriesQuery->whereNotNull('parent_id');
        }

        // Urutkan: Main kategori dulu, lalu sub kategori di bawahnya
        $categories = $categoriesQuery->orderByRaw('CASE WHEN parent_id IS NULL THEN id ELSE parent_id END')
            ->orderByRaw('CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END')
            ->orderBy('name', 'asc')
            ->paginate(25)
            ->withQueryString();

        // Daftar kategori utama untuk dropdown parent pada form
        $mainCategories = Category::whereNull('parent_id')->orderBy('name', 'asc')->get();

        // Hitung statistik taksonomi
        $totalMain = Category::whereNull('parent_id')->count();
        $totalSub = Category::whereNotNull('parent_id')->count();
        $totalCategories = Category::count();

        return view('admin.categories.index', compact(
            'categories',
            'mainCategories',
            'search',
            'parentFilter',
            'totalMain',
            'totalSub',
            'totalCategories'
        ));
    }

    /**
     * Simpan Kategori / Sub-Kategori baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'slug' => 'nullable|string|max:160|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:50',
            'badge_color' => 'nullable|string|max:30',
            'description' => 'nullable|string',
            'tags' => 'nullable|string',
        ]);

        $slug = $request->filled('slug')
            ? Str::slug($request->slug)
            : Str::slug($request->name);

        // Ensure unique slug
        $originalSlug = $slug;
        $counter = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        $normalizedTags = $request->filled('tags')
            ? collect(preg_split('/[,;\n]+/', $request->tags))
                ->map(fn($t) => trim($t))
                ->filter()
                ->unique()
                ->implode(', ')
            : null;

        $category = Category::create([
            'parent_id' => $request->parent_id ?: null,
            'name' => $request->name,
            'slug' => $slug,
            'icon' => $request->icon ?: ($request->parent_id ? (Category::find($request->parent_id)?->icon ?? 'book-open') : 'book-open'),
            'badge_color' => $request->badge_color ?: 'teal',
            'description' => $request->description,
            'tags' => $normalizedTags,
        ]);

        if (class_exists(ActivityLog::class)) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'create_category',
                'description' => "Menambahkan kategori baru: {$category->name} " . ($category->parent_id ? '(Sub-Kategori)' : '(Kategori Utama)'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori \"{$category->name}\" berhasil ditambahkan!");
    }

    /**
     * Tampilkan halaman/modal edit kategori.
     */
    public function edit(Category $category)
    {
        $mainCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.categories.edit', compact('category', 'mainCategories'));
    }

    /**
     * Perbarui Kategori / Sub-Kategori.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'slug' => 'nullable|string|max:160|unique:categories,slug,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:50',
            'badge_color' => 'nullable|string|max:30',
            'description' => 'nullable|string',
            'tags' => 'nullable|string',
        ]);

        // Prevent setting itself as parent
        $parentId = $request->parent_id ?: null;
        if ($parentId && (int)$parentId === (int)$category->id) {
            return back()->with('error', 'Kategori tidak dapat menjadi induk bagi dirinya sendiri!')->withInput();
        }

        $slug = $request->filled('slug')
            ? Str::slug($request->slug)
            : Str::slug($request->name);

        $normalizedTags = $request->filled('tags')
            ? collect(preg_split('/[,;\n]+/', $request->tags))
                ->map(fn($t) => trim($t))
                ->filter()
                ->unique()
                ->implode(', ')
            : null;

        $category->update([
            'parent_id' => $parentId,
            'name' => $request->name,
            'slug' => $slug,
            'icon' => $request->icon ?: $category->icon,
            'badge_color' => $request->badge_color ?: $category->badge_color,
            'description' => $request->description,
            'tags' => $normalizedTags,
        ]);

        if (class_exists(ActivityLog::class)) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'update_category',
                'description' => "Memperbarui kategori: {$category->name}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori \"{$category->name}\" berhasil diperbarui!");
    }

    /**
     * Hapus Kategori / Sub-Kategori.
     */
    public function destroy(Request $request, Category $category)
    {
        $name = $category->name;
        $isParent = $category->parent_id === null;

        // If it's a parent, detach its subcategories so they become standalone main categories or get deleted
        if ($isParent) {
            Category::where('parent_id', $category->id)->update(['parent_id' => null]);
        }

        // Nullify foreign keys in courses
        Course::where('category_id', $category->id)->update(['category_id' => null]);
        Course::where('sub_category_id', $category->id)->update(['sub_category_id' => null]);

        $category->delete();

        if (class_exists(ActivityLog::class)) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'delete_category',
                'description' => "Menghapus kategori: {$name}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori \"{$name}\" berhasil dihapus!");
    }

    /**
     * Sinkronkan / Import data dari file Excel katagori lms.xlsx
     */
    public function importExcel(Request $request)
    {
        try {
            $seeder = new CategoryTaxonomySeeder();
            $seeder->setCommand(new class {
                public function info($msg) {}
                public function error($msg) {}
            });
            $seeder->run();

            return redirect()->route('admin.categories.index')
                ->with('success', 'Taksonomi Kategori, Sub-Kategori, dan Tag berhasil disinkronkan dari file "katagori lms.xlsx"!');
        } catch (\Throwable $e) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Gagal menyinkronkan Excel: ' . $e->getMessage());
        }
    }
}
