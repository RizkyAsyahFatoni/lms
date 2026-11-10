<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\MailSetting;
use App\Models\EmailTemplate;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Mail;
use App\Mail\DynamicNotificationMail;

class MailNotificationTest extends TestCase
{
    protected User $admin;
    protected User $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::whereIn('role', ['admin', 'super_admin'])->first() ?? User::factory()->create(['role' => 'super_admin']);
        $this->student = User::where('role', 'student')->first() ?? User::factory()->create(['role' => 'student']);
    }

    public function test_admin_can_access_mail_settings_page(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin/mail-settings');
        $response->assertStatus(200);
        $response->assertSee('Konfigurasi Email SMTP');
        $response->assertSee('Template Notifikasi');
        $response->assertSee('Pendaftaran Berhasil');
        $response->assertSee('registration_success');
    }

    public function test_non_admin_cannot_access_mail_settings(): void
    {
        $this->actingAs($this->student);

        $response = $this->get('/admin/mail-settings');
        $response->assertStatus(403);
    }

    public function test_admin_can_update_smtp_settings(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post('/admin/mail-settings/smtp', [
            'is_active'         => 1,
            'mail_mailer'       => 'smtp',
            'mail_host'         => 'mail.beraukab.go.id',
            'mail_port'         => 587,
            'mail_username'     => 'lms-notif@beraukab.go.id',
            'mail_password'     => 'RahasiaBerau2026!',
            'mail_encryption'   => 'tls',
            'mail_from_address' => 'noreply@beraukab.go.id',
            'mail_from_name'    => 'BKPSDM Berau Corpu Official',
        ]);

        $response->assertRedirect('/admin/mail-settings?tab=smtp');
        $response->assertSessionHas('success');

        $setting = MailSetting::getSettings();
        $this->assertTrue((bool) $setting->is_active);
        $this->assertEquals('mail.beraukab.go.id', $setting->mail_host);
        $this->assertEquals(587, $setting->mail_port);
        $this->assertEquals('noreply@beraukab.go.id', $setting->mail_from_address);
    }

    public function test_admin_can_update_email_template(): void
    {
        $this->actingAs($this->admin);

        $template = EmailTemplate::where('code', 'registration_success')->firstOrFail();

        $response = $this->post("/admin/mail-settings/templates/{$template->id}", [
            'subject'   => 'SELAMAT: Anda Terdaftar di {nama_kegiatan}!',
            'body_html' => '<p>Halo {nama_peserta}, tiket Anda adalah {nomor_tiket}.</p>',
            'is_active' => 1,
        ]);

        $response->assertRedirect("/admin/mail-settings?tab=templates&template_id={$template->id}");
        $response->assertSessionHas('success');

        $template->refresh();
        $this->assertEquals('SELAMAT: Anda Terdaftar di {nama_kegiatan}!', $template->subject);
        $this->assertStringContainsString('{nomor_tiket}', $template->body_html);
    }

    public function test_admin_can_reset_email_template(): void
    {
        $this->actingAs($this->admin);

        $template = EmailTemplate::where('code', 'registration_success')->firstOrFail();

        // Mutate first
        $template->update(['subject' => 'Subjek Sementara Diubah']);

        // Reset
        $response = $this->post("/admin/mail-settings/templates/{$template->id}/reset");
        $response->assertSessionHas('success');

        $template->refresh();
        $this->assertStringContainsString('Konfirmasi Pendaftaran', $template->subject);
    }

    public function test_admin_can_preview_email_template(): void
    {
        $this->actingAs($this->admin);

        $template = EmailTemplate::where('code', 'certificate_available')->firstOrFail();

        $response = $this->get("/admin/mail-settings/templates/{$template->id}/preview");
        $response->assertStatus(200);
        $response->assertSee('E-Sertifikat Kelulusan Resmi');
        $response->assertSee('Budi Santoso');
    }

    public function test_admin_can_test_smtp_connection_with_mock_mailer(): void
    {
        $this->actingAs($this->admin);
        Mail::fake();

        $response = $this->post('/admin/mail-settings/test', [
            'test_email' => 'penguji.asn@beraukab.go.id',
        ]);

        $response->assertRedirect('/admin/mail-settings?tab=smtp');
        $response->assertSessionHas('success');

        Mail::assertSent(DynamicNotificationMail::class, function ($mail) {
            return $mail->hasTo('penguji.asn@beraukab.go.id') &&
                   str_contains($mail->emailSubject, 'Uji Koneksi Server Email');
        });
    }

    public function test_notification_service_dispatches_template_and_in_app(): void
    {
        Mail::fake();

        // Ensure mail is active
        MailSetting::getSettings()->update(['is_active' => true]);

        NotificationService::sendTemplate(
            $this->student,
            'certificate_available',
            [
                'nama_kegiatan'    => 'Diklat Manajemen Risiko SPBE',
                'nomor_sertifikat' => 'CERT-2026-991',
                'bobot_jp'         => '16',
                'link_sertifikat'  => 'http://127.0.0.1:8000/certificates/CERT-2026-991',
            ]
        );

        // Assert in-app notification saved in database
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->student->id,
            'type'          => 'App\Notifications\GeneralNotification',
        ]);

        // Assert mail was sent via template
        Mail::assertSent(DynamicNotificationMail::class, function ($mail) {
            return $mail->hasTo($this->student->email) &&
                   str_contains($mail->emailSubject, 'Diklat Manajemen Risiko SPBE');
        });
    }
}
