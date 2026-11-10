<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Slider;
use App\Models\Webinar;
use App\Models\Popup;
use App\Models\Certificate;

class AdminAddonsTest extends TestCase
{
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::where('role', 'admin')->first();
    }

    public function test_admin_can_manage_lms_users_and_update_status()
    {
        $this->actingAs($this->admin);

        // View users list
        $response = $this->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('Manajemen User & Hak Akses');

        // Update student
        $student = User::where('role', 'student')->first();
        $updateResponse = $this->post("/admin/users/{$student->id}", [
            'name' => $student->name,
            'email' => $student->email,
            'role' => 'student',
            'phone' => '081299998888',
            'headline' => 'Analis Kepegawaian Ahli Pertama',
            'bio' => 'Peserta aktif pengembangan kompetensi ASN Kabupaten Berau.',
        ]);

        $updateResponse->assertRedirect();
        $student->refresh();
        $this->assertEquals('081299998888', $student->phone);
        $this->assertEquals('Analis Kepegawaian Ahli Pertama', $student->headline);
    }

    public function test_admin_can_crud_homepage_sliders_and_renders_on_home()
    {
        $this->actingAs($this->admin);

        // View sliders
        $response = $this->get('/admin/sliders');
        $response->assertStatus(200);
        $response->assertSee('Manajemen Slider');

        // Create new slider
        $storeResponse = $this->post('/admin/sliders', [
            'title' => 'Slider Testing Nasional',
            'subtitle' => 'Deskripsi banner pengujian sistem.',
            'badge_text' => 'SPECIAL BADGE',
            'image_url' => 'https://example.com/banner.jpg',
            'cta_text' => 'Daftar Sekarang',
            'cta_link' => '/courses',
            'order' => 99,
            'is_active' => true,
        ]);

        $storeResponse->assertRedirect();
        $this->assertDatabaseHas('sliders', ['title' => 'Slider Testing Nasional']);

        // Home view check
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Slider Testing Nasional');
    }

    public function test_admin_can_crud_webinars_and_renders_on_home()
    {
        $this->actingAs($this->admin);

        // View webinars
        $response = $this->get('/admin/webinars');
        $response->assertStatus(200);
        $response->assertSee('Manajemen Agenda');

        // Store new webinar
        $storeResponse = $this->post('/admin/webinars', [
            'delivery_mode' => 'online',
            'title' => 'Workshop Akselerasi SPBE dan Satu Data Berau',
            'speaker_name' => 'Dr. Ir. Narasumber Utama, M.Kom',
            'speaker_title' => 'Tenaga Ahli Tata Kelola Digital',
            'event_date' => now()->addDays(10)->toDateString(),
            'time_start' => '10:00',
            'time_end' => '12:00',
            'location_type' => 'Online via Zoom & LMS Berau',
            'quota' => 200,
            'price' => 0,
            'status' => 'upcoming',
            'is_featured' => true,
        ]);

        $storeResponse->assertRedirect();
        $this->assertDatabaseHas('webinars', ['title' => 'Workshop Akselerasi SPBE dan Satu Data Berau']);

        // Home view check
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Workshop Akselerasi SPBE dan Satu Data Berau');
    }

    public function test_admin_can_manage_popups_and_renders_active_popup()
    {
        $this->actingAs($this->admin);

        // View popups
        $response = $this->get('/admin/popups');
        $response->assertStatus(200);
        $response->assertSee('Sistem Modal Popup Beranda');

        // Store active popup
        $storeResponse = $this->post('/admin/popups', [
            'title' => 'Modal Popup Promosi Khusus 2026',
            'badge_text' => 'PENGUMUMAN PENTING',
            'content' => 'Ini adalah konten popup promosi pengujian.',
            'button_text' => 'Buka Informasi',
            'button_link' => '/courses',
            'is_active' => true,
        ]);

        $storeResponse->assertRedirect();
        $this->assertDatabaseHas('popups', ['title' => 'Modal Popup Promosi Khusus 2026', 'is_active' => true]);

        // Home view check
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Modal Popup Promosi Khusus 2026');
    }

    public function test_admin_can_customize_certificate_builder_and_shows_on_certificate()
    {
        $this->actingAs($this->admin);

        $course = Course::first();

        // View builder
        $builderResponse = $this->get("/admin/courses/{$course->id}/certificate-builder");
        $builderResponse->assertStatus(200);
        $builderResponse->assertSee('Interactive Certificate Builder');

        // Update certificate design
        $updateResponse = $this->post("/admin/courses/{$course->id}/certificate-builder", [
            'cert_title' => 'SERTIFIKAT KELULUSAN PELATIHAN APARATUR SIPIL NEGARA',
            'cert_institution_name' => 'BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA',
            'cert_institution_sub' => 'Pemerintah Kabupaten Berau',
            'cert_signatory_name' => 'Dra. Hj. Sri Eka Yuliana, M.Si',
            'cert_signatory_role' => 'Kepala BKPSDM Kabupaten Berau',
            'cert_signatory_nip' => 'NIP. 19700101 199503 2 001',
            'cert_theme_color' => 'indigo',
            'cert_statement' => 'Telah menyelesaikan seluruh modul pelatihan dengan predikat sangat memuaskan.',
            'certificate_available' => true,
        ]);

        $updateResponse->assertRedirect();
        $course->refresh();
        $this->assertEquals('SERTIFIKAT KELULUSAN PELATIHAN APARATUR SIPIL NEGARA', $course->cert_title);
        $this->assertEquals('BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA', $course->cert_institution_name);

        // Check that Certificate view dynamically reflects this
        $cert = Certificate::where('course_id', $course->id)->first();
        if (!$cert) {
            $student = User::where('role', 'student')->first();
            $cert = Certificate::create([
                'user_id' => $student->id,
                'course_id' => $course->id,
                'certificate_number' => 'CERT-BERAU-9999',
                'issued_at' => now(),
                'verification_hash' => 'HASH12345',
            ]);
        }

        $certResponse = $this->get("/certificates/{$cert->certificate_number}");
        $certResponse->assertStatus(200);
        $certResponse->assertSee('SERTIFIKAT KELULUSAN PELATIHAN APARATUR SIPIL NEGARA');
        $certResponse->assertSee('BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA');
        $certResponse->assertSee('Dra. Hj. Sri Eka Yuliana, M.Si');
    }
}
