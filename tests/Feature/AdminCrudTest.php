<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Article;
use App\Models\Popup;
use App\Models\Slider;
use App\Models\Webinar;
use App\Models\Course;
use App\Models\Setting;
use Illuminate\Support\Str;

class AdminCrudTest extends TestCase
{
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::where('role', 'admin')->orWhere('role', 'super_admin')->first();
    }

    public function test_admin_category_crud()
    {
        $this->actingAs($this->admin);

        // 1. CREATE Category
        $slug = 'kategori-uji-' . Str::random(5);
        $name = 'Kategori Uji CRUD ' . Str::random(5);
        $response = $this->post('/admin/categories', [
            'name' => $name,
            'description' => 'Deskripsi pengujian create kategori otomatis.',
            'parent_id' => null,
            'color' => '#10b981',
            'order' => 99,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('categories', ['name' => $name]);

        $category = Category::where('name', $name)->first();

        // 2. UPDATE Category
        $updatedName = $name . ' (Diperbarui)';
        $updateResponse = $this->put("/admin/categories/{$category->id}", [
            'name' => $updatedName,
            'description' => 'Deskripsi kategori yang telah berhasil diperbarui.',
            'parent_id' => null,
            'color' => '#3b82f6',
            'order' => 100,
        ]);

        $updateResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => $updatedName,
        ]);

        // 3. DELETE Category
        $deleteResponse = $this->delete("/admin/categories/{$category->id}");
        $deleteResponse->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_admin_article_crud()
    {
        $this->actingAs($this->admin);

        // 1. CREATE Article
        $title = 'Artikel Pengujian CRUD ' . Str::random(5);
        $createResponse = $this->post('/admin/articles', [
            'title' => $title,
            'category' => 'berita',
            'excerpt' => 'Ringkasan artikel pengujian sistem.',
            'content' => '<p>Konten lengkap artikel pengujian fitur create update delete.</p>',
            'is_published' => 0,
        ]);

        $createResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('articles', ['title' => $title]);

        $article = Article::where('title', $title)->first();

        // 2. UPDATE Article
        $updatedTitle = $title . ' - Terbit';
        $updateResponse = $this->post("/admin/articles/{$article->id}", [
            'title' => $updatedTitle,
            'category' => 'regulasi',
            'excerpt' => 'Ringkasan artikel telah diupdate.',
            'content' => '<p>Konten lengkap yang telah diperbarui.</p>',
            'is_published' => 1,
        ]);

        $updateResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => $updatedTitle,
            'is_published' => 1,
        ]);

        // 3. DELETE Article
        $deleteResponse = $this->delete("/admin/articles/{$article->id}");
        $deleteResponse->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    public function test_admin_popup_crud()
    {
        $this->actingAs($this->admin);

        // 1. CREATE Popup
        $title = 'Popup Promo CRUD ' . Str::random(5);
        $createResponse = $this->post('/admin/popups', [
            'title' => $title,
            'content' => 'Pengumuman modal tes create update save.',
            'badge_text' => 'INFO PENTING',
            'button_text' => 'Lihat Rincian',
            'button_link' => 'https://example.com/courses',
            'is_active' => 1,
        ]);

        $createResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('popups', ['title' => $title]);

        $popup = Popup::where('title', $title)->first();

        // 2. UPDATE Popup
        $updatedTitle = $title . ' (Updated)';
        $updateResponse = $this->post("/admin/popups/{$popup->id}", [
            'title' => $updatedTitle,
            'content' => 'Konten popup telah berhasil disimpan kembali.',
            'badge_text' => 'UPDATE',
            'button_text' => 'Buka Sekarang',
            'button_link' => 'https://example.com/catalog',
            'is_active' => 1,
        ]);

        $updateResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('popups', [
            'id' => $popup->id,
            'title' => $updatedTitle,
        ]);

        // 3. TOGGLE Popup Status
        $toggleResponse = $this->post("/admin/popups/{$popup->id}/toggle");
        $toggleResponse->assertSessionHasNoErrors();
        $popup->refresh();
        $this->assertFalse((bool)$popup->is_active);

        // Clean up
        $popup->delete();
    }

    public function test_admin_slider_crud()
    {
        $this->actingAs($this->admin);

        // 1. CREATE Slider
        $title = 'Slider Pengujian ' . Str::random(5);
        $createResponse = $this->post('/admin/sliders', [
            'title' => $title,
            'subtitle' => 'Subjudul slider otomatis',
            'badge_text' => 'UNGGULAN',
            'image_url' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3',
            'cta_text' => 'Pelajari',
            'cta_link' => '/courses',
            'order' => 99,
            'is_active' => 1,
        ]);

        $createResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('sliders', ['title' => $title]);

        $slider = Slider::where('title', $title)->first();

        // 2. UPDATE Slider
        $updatedTitle = $title . ' (Revisi)';
        $updateResponse = $this->post("/admin/sliders/{$slider->id}", [
            'title' => $updatedTitle,
            'subtitle' => 'Subjudul slider yang telah direvisi dan disimpan.',
            'badge_text' => 'TERBARU',
            'image_url' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3',
            'cta_text' => 'Buka Kursus',
            'cta_link' => '/katalog',
            'order' => 1,
            'is_active' => 1,
        ]);

        $updateResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('sliders', [
            'id' => $slider->id,
            'title' => $updatedTitle,
        ]);

        // 3. DELETE Slider
        $deleteResponse = $this->delete("/admin/sliders/{$slider->id}");
        $deleteResponse->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('sliders', ['id' => $slider->id]);
    }

    public function test_admin_webinar_crud()
    {
        $this->actingAs($this->admin);

        // 1. CREATE Webinar
        $title = 'Webinar Uji CRUD ' . Str::random(5);
        $createResponse = $this->post('/admin/webinars', [
            'delivery_mode' => 'online',
            'title' => $title,
            'description' => 'Deskripsi webinar pengujian fitur CRUD.',
            'speaker_name' => 'Dr. Instruktur Pengujian',
            'speaker_title' => 'Ahli Kebijakan Publik',
            'speaker_institution' => 'BKPSDM Kabupaten Berau',
            'event_date' => now()->addDays(7)->toDateString(),
            'time_start' => '09:00',
            'time_end' => '11:30',
            'location_type' => 'Online via Zoom LMS',
            'quota' => 150,
            'jp' => 3,
            'price' => 0,
            'status' => 'upcoming',
            'is_featured' => 1,
            'cert_available' => 1,
        ]);

        $createResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('webinars', ['title' => $title]);

        $webinar = Webinar::where('title', $title)->first();

        // 2. UPDATE Webinar
        $updatedTitle = $title . ' (Jadwal Terkonfirmasi)';
        $updateResponse = $this->post("/admin/webinars/{$webinar->id}", [
            'delivery_mode' => 'hybrid',
            'title' => $updatedTitle,
            'description' => 'Deskripsi webinar yang telah diperbarui dan siap dilaksanakan.',
            'speaker_name' => 'Dr. Instruktur Pengujian, M.Si',
            'speaker_title' => 'Widyaiswara Ahli Utama',
            'speaker_institution' => 'BKPSDM Kabupaten Berau',
            'event_date' => now()->addDays(10)->toDateString(),
            'time_start' => '08:30',
            'time_end' => '12:00',
            'location_type' => 'Gedung Diklat & Zoom Meeting',
            'quota' => 200,
            'jp' => 4,
            'price' => 0,
            'status' => 'upcoming',
            'is_featured' => 1,
            'cert_available' => 1,
        ]);

        $updateResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('webinars', [
            'id' => $webinar->id,
            'title' => $updatedTitle,
            'quota' => 200,
        ]);

        // 3. DELETE Webinar
        $deleteResponse = $this->delete("/admin/webinars/{$webinar->id}");
        $deleteResponse->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('webinars', ['id' => $webinar->id]);
    }

    public function test_admin_settings_grid_layout_save()
    {
        $this->actingAs($this->admin);

        // 1. SAVE Layout to 2x3
        $save2x3 = $this->post('/admin/settings/layout', [
            'featured_grid_layout' => '2x3',
        ]);
        $save2x3->assertSessionHasNoErrors();
        $this->assertDatabaseHas('app_settings', ['featured_grid_layout' => '2x3']);

        // 2. SAVE Layout back to 2x4
        $save2x4 = $this->post('/admin/settings/layout', [
            'featured_grid_layout' => '2x4',
        ]);
        $save2x4->assertSessionHasNoErrors();
        $this->assertDatabaseHas('app_settings', ['featured_grid_layout' => '2x4']);
    }

    public function test_admin_user_crud()
    {
        $this->actingAs($this->admin);

        // 1. CREATE User
        $email = strtolower('uji.pegawai.' . Str::random(6) . '@instansi.go.id');
        $createResponse = $this->post('/admin/users', [
            'name' => 'Pegawai Uji CRUD',
            'email' => $email,
            'password' => 'Secret123!',
            'nip' => '19900101' . rand(100000, 999999),
            'nik' => '320101' . rand(1000000000, 9999999999),
            'phone' => '081234567890',
            'role' => 'asn_learner',
        ]);

        $createResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', ['email' => $email]);

        $user = User::where('email', $email)->first();

        // 2. UPDATE User
        $updateResponse = $this->post("/admin/users/{$user->id}", [
            'name' => 'Pegawai Uji CRUD (Diperbarui)',
            'email' => $user->email,
            'nip' => $user->nip,
            'nik' => $user->nik,
            'phone' => '081298765432',
            'role' => 'instructor',
            'is_active' => 1,
            'headline' => 'Instruktur Pengujian Sistem',
            'bio' => 'Profil instruktur hasil update testing CRUD.',
        ]);

        $updateResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Pegawai Uji CRUD (Diperbarui)',
            'role' => 'instructor',
        ]);

        // 3. TOGGLE User Status
        $toggleResponse = $this->post("/admin/users/{$user->id}/toggle-status");
        $toggleResponse->assertSessionHasNoErrors();
        $user->refresh();
        $this->assertFalse((bool)$user->is_active);

        // Clean up
        $user->delete();
    }

    public function test_admin_course_crud()
    {
        $this->actingAs($this->admin);

        $category = Category::first();
        $instructor = User::where('role', 'instructor')->first() ?: $this->admin;

        // 1. CREATE Course
        $title = 'Kursus Pengujian CRUD ' . Str::random(5);
        $createResponse = $this->post('/admin/courses', [
            'title' => $title,
            'category_id' => $category->id,
            'instructor_id' => $instructor->id,
            'level' => 'Dasar',
            'price' => 0,
            'duration_hours' => 20,
            'subtitle' => 'Subjudul materi pelatihan pengujian',
            'description' => '<p>Deskripsi komprehensif materi kursus pengujian fitur.</p>',
            'learning_objectives' => 'Mampu memahami fitur create, update, save.',
            'learning_method' => 'E-Learning Mandiri (Asinkron)',
        ]);

        $createResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('courses', ['title' => $title]);

        $course = Course::where('title', $title)->first();

        // 2. UPDATE Course
        $updatedTitle = $title . ' (Versi Akreditasi)';
        $updateResponse = $this->post("/admin/courses/{$course->id}", [
            'title' => $updatedTitle,
            'category_id' => $category->id,
            'instructor_id' => $instructor->id,
            'level' => 'Menengah',
            'course_type' => 'e-course',
            'target_audience_type' => 'umum',
            'price' => 0,
            'duration_hours' => 24,
            'subtitle' => 'Subjudul materi telah disempurnakan',
            'description' => '<p>Deskripsi materi telah diperbarui oleh administrator.</p>',
            'learning_objectives' => 'Kompetensi telah diselaraskan.',
            'learning_method' => 'Blended Learning',
            'cert_title' => 'SERTIFIKAT KELULUSAN RESMI',
            'cert_institution_name' => 'BADAN KEPEGAWAIAN DAERAH',
            'cert_institution_sub' => 'Pemerintah Kabupaten Berau',
            'cert_theme_color' => 'indigo',
        ]);

        $updateResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'title' => $updatedTitle,
            'duration_hours' => 24,
        ]);

        // 3. CREATE Lesson under Course
        $lessonResponse = $this->post("/admin/courses/{$course->id}/lessons", [
            'title' => 'Modul 1: Pengantar Pelatihan',
            'section_title' => 'Bab I: Fondasi Dasar',
            'type' => 'reading',
            'duration_minutes' => 45,
            'content' => '<p>Materi bacaan silabus pelatihan.</p>',
        ]);

        $lessonResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('lessons', [
            'course_id' => $course->id,
            'title' => 'Modul 1: Pengantar Pelatihan',
        ]);

        // 4. APPROVE Course
        $approveResponse = $this->post("/admin/courses/{$course->id}/approve");
        $approveResponse->assertSessionHasNoErrors();
        $course->refresh();
        $this->assertEquals('approved', $course->approval_status);
        $this->assertTrue((bool)$course->is_published);

        // Clean up
        $course->lessons()->delete();
        $course->delete();
    }
}

