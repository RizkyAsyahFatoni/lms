<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoryTaxonomySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = database_path('data/categories_taxonomy.json');

        if (!file_exists($jsonPath)) {
            if ($this->command) {
                $this->command->error("File {$jsonPath} not found!");
            }
            return;
        }

        $taxonomies = json_decode(file_get_contents($jsonPath), true);

        foreach ($taxonomies as $main) {
            $mainCat = Category::firstOrCreate(
                ['slug' => $main['slug']],
                [
                    'parent_id' => null,
                    'name' => $main['name'],
                    'icon' => $main['icon'] ?? 'book-open',
                    'badge_color' => $main['badge_color'] ?? 'teal',
                    'description' => 'Kategori utama kompetensi ' . $main['name'] . ' untuk ASN dan perangkat daerah Kabupaten Berau.',
                ]
            );

            // Update parent_id if previously null
            $mainCat->update([
                'parent_id' => null,
                'name' => $main['name'],
            ]);

            foreach ($main['sub_categories'] as $sub) {
                Category::updateOrCreate(
                    [
                        'parent_id' => $mainCat->id,
                        'slug' => $sub['slug']
                    ],
                    [
                        'name' => $sub['name'],
                        'tags' => $sub['tags'] ?? null,
                        'icon' => $mainCat->icon,
                        'badge_color' => $mainCat->badge_color,
                        'description' => 'Sub-kategori ' . $sub['name'] . ' di bawah ' . $mainCat->name,
                    ]
                );
            }
        }

        if ($this->command) {
            $this->command->info('Kategori & Sub-Kategori berhasil disinkronkan dari data taksonomi LMS!');
        }
    }
}
