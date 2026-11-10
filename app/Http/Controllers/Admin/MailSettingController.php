<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailSetting;
use App\Models\EmailTemplate;
use App\Models\Course;
use App\Models\Webinar;
use App\Models\ActivityLog;
use App\Mail\DynamicNotificationMail;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailSettingController extends Controller
{
    private function checkAdmin(): void
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'super_admin', 'bkpsdm'])) {
            abort(403, 'Akses ditolak. Khusus Administrator BKPSDM.');
        }
    }

    /**
     * Display SMTP Server Settings and Email Templates Management.
     */
    public function index(Request $request): View
    {
        $this->checkAdmin();

        $mailSetting = MailSetting::getSettings();
        $templates = EmailTemplate::orderBy('id', 'asc')->get();
        $selectedTemplate = null;

        if ($request->filled('template_id')) {
            $selectedTemplate = EmailTemplate::find($request->template_id);
        }
        if (!$selectedTemplate && $templates->isNotEmpty()) {
            $selectedTemplate = $templates->first();
        }

        // List webinars & courses for broadcast reminder triggers
        $upcomingWebinars = Webinar::where('event_date', '>=', now()->toDateString())
            ->orderBy('event_date', 'asc')
            ->take(5)
            ->get();

        return view('admin.mail.index', compact('mailSetting', 'templates', 'selectedTemplate', 'upcomingWebinars'));
    }

    /**
     * Update SMTP Server configuration.
     */
    public function updateSmtp(Request $request): RedirectResponse
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'is_active'         => 'nullable|boolean',
            'mail_mailer'       => 'required|string|in:smtp,sendmail,log',
            'mail_host'         => 'required|string|max:255',
            'mail_port'         => 'required|integer|min:1|max:65535',
            'mail_username'     => 'nullable|string|max:255',
            'mail_password'     => 'nullable|string|max:255',
            'mail_encryption'   => 'nullable|string|in:tls,ssl,none',
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name'    => 'required|string|max:255',
        ]);

        $mailSetting = MailSetting::getSettings();
        
        $dataToUpdate = [
            'is_active'         => $request->boolean('is_active'),
            'mail_mailer'       => $validated['mail_mailer'],
            'mail_host'         => $validated['mail_host'],
            'mail_port'         => $validated['mail_port'],
            'mail_username'     => $validated['mail_username'],
            'mail_encryption'   => $validated['mail_encryption'] === 'none' ? null : $validated['mail_encryption'],
            'mail_from_address' => $validated['mail_from_address'],
            'mail_from_name'    => $validated['mail_from_name'],
        ];

        // Only update password if provided
        if ($request->filled('mail_password')) {
            $dataToUpdate['mail_password'] = $request->mail_password;
        }

        $mailSetting->update($dataToUpdate);

        ActivityLog::record(
            'update_mail_settings',
            "Memperbarui konfigurasi SMTP email: {$mailSetting->mail_host}:{$mailSetting->mail_port} (Status: " . ($mailSetting->is_active ? 'Aktif' : 'Nonaktif') . ")",
            auth()->id()
        );

        return redirect()->route('admin.mail.index', ['tab' => 'smtp'])
            ->with('success', 'Konfigurasi server SMTP berhasil disimpan!');
    }

    /**
     * Test SMTP connection by sending a diagnostic test email.
     */
    public function testConnection(Request $request): RedirectResponse
    {
        $this->checkAdmin();

        $request->validate([
            'test_email' => 'required|email',
        ]);

        $mailSetting = MailSetting::getSettings();
        $testEmail = $request->test_email;

        try {
            // Apply current config to runtime
            $mailSetting->applyToConfig();

            $subject = 'Uji Koneksi Server Email (SMTP) - LMS KAB Berau';
            $bodyHtml = "<p>Halo,</p>
<p>Ini adalah pesan email uji coba dari <strong>LMS Pemerintah Kabupaten Berau</strong>.</p>
<div style='background:#ecfdf5; border-left:4px solid #10b981; padding:12px 16px; margin:16px 0; border-radius:8px;'>
    <p style='margin:0; font-weight:bold; color:#065f46;'>Status: KONEKSI SMTP BERHASIL!</p>
    <p style='margin:4px 0 0 0; font-size:12px; color:#047857;'>Server: {$mailSetting->mail_host}:{$mailSetting->mail_port} ({$mailSetting->mail_encryption})</p>
    <p style='margin:2px 0 0 0; font-size:12px; color:#047857;'>Waktu Uji: " . now()->format('d M Y H:i:s') . " WITA</p>
</div>
<p>Sistem notifikasi email kini telah siap digunakan untuk mengirimkan balasan otomatis ke peserta.</p>";

            Mail::to($testEmail)->send(new DynamicNotificationMail($subject, $bodyHtml, route('admin.mail.index')));

            $mailSetting->update([
                'last_tested_at'    => now(),
                'last_test_status'  => 'success',
                'last_test_message' => "Email uji coba berhasil dikirim ke {$testEmail} pada " . now()->format('d M Y H:i:s'),
            ]);

            return redirect()->route('admin.mail.index', ['tab' => 'smtp'])
                ->with('success', "Sukses! Email uji coba berhasil dikirim ke [{$testEmail}]. Server SMTP berfungsi normal.");
        } catch (\Throwable $e) {
            $errorMsg = $e->getMessage();
            Log::error('SMTP Test Failed: ' . $errorMsg);

            $mailSetting->update([
                'last_tested_at'    => now(),
                'last_test_status'  => 'failed',
                'last_test_message' => "Gagal kirim ke {$testEmail}: " . substr($errorMsg, 0, 255),
            ]);

            return redirect()->route('admin.mail.index', ['tab' => 'smtp'])
                ->with('error', "Gagal menghubungi server SMTP: {$errorMsg}");
        }
    }

    /**
     * Update an email template (subject and body).
     */
    public function updateTemplate(Request $request, EmailTemplate $template): RedirectResponse
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'subject'   => 'required|string|max:255',
            'body_html' => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);

        $template->update([
            'subject'   => $validated['subject'],
            'body_html' => $validated['body_html'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLog::record(
            'update_email_template',
            "Memperbarui template email [{$template->name}] ({$template->code})",
            auth()->id()
        );

        return redirect()->route('admin.mail.index', ['tab' => 'templates', 'template_id' => $template->id])
            ->with('success', "Template balasan email '{$template->name}' berhasil diperbarui!");
    }

    /**
     * Reset email template back to default factory content.
     */
    public function resetTemplate(EmailTemplate $template): RedirectResponse
    {
        $this->checkAdmin();

        $defaults = self::getDefaultTemplateData($template->code);
        if ($defaults) {
            $template->update($defaults);
        }

        return redirect()->route('admin.mail.index', ['tab' => 'templates', 'template_id' => $template->id])
            ->with('success', "Template '{$template->name}' berhasil dikembalikan ke format standar!");
    }

    /**
     * Live HTML preview of an email template with sample data.
     */
    public function previewTemplate(EmailTemplate $template): View
    {
        $this->checkAdmin();

        $sampleVars = [
            'nama_peserta'     => 'Budi Santoso, S.Kom',
            'nip'              => '198805142014021003',
            'instansi'         => 'Dinas Komunikasi dan Informatika',
            'nama_kegiatan'    => 'Workshop Tata Kelola SPBE & Keamanan Informasi 2026',
            'nomor_tiket'      => 'TKT-2026-WBN-8842',
            'link_kegiatan'    => route('home'),
            'link_webinar'     => 'https://zoom.us/j/1234567890',
            'link_evaluasi'    => route('home'),
            'link_sertifikat'  => route('home'),
            'bobot_jp'         => '12',
            'tanggal_kegiatan' => '25 November 2026',
            'waktu_mulai'      => '09:00 - 15:30 WITA',
            'metode_kegiatan'  => 'Webinar Daring Interaktif (Zoom)',
            'lokasi_kegiatan'  => 'Ruang Rapat Sasana Praja & Zoom Cloud',
            'nomor_sertifikat' => 'CERT-LMS-2026-004-1002',
            'passing_grade'    => '75%',
            'nama_pengusul'    => 'Hendra Wijaya, S.Kom',
            'nama_opd'         => 'Dinas Komunikasi dan Informatika',
            'judul_usulan'     => 'Pelatihan Keamanan Jaringan dan CSIRT Daerah',
            'status_usulan'    => 'DISETUJUI & DITERBITKAN',
            'catatan_review'   => 'Usulan sangat relevan dengan prioritas SPBE Kabupaten Berau tahun 2026. Jadwal ditetapkan pada triwulan pertama.',
            'link_usulan'      => route('home'),
        ];

        $subject = $template->renderSubject($sampleVars);
        $bodyHtml = $template->renderBody($sampleVars);

        return view('emails.dynamic_notification', [
            'emailSubject' => $subject,
            'bodyHtml'     => $bodyHtml,
            'actionUrl'    => route('home'),
        ]);
    }

    /**
     * Broadcast Webinar Reminder to all registered participants (Manual Trigger).
     */
    public function sendWebinarReminder(Webinar $webinar): RedirectResponse
    {
        $this->checkAdmin();

        $participants = $webinar->registrations()->with('user')->get();
        $count = 0;

        foreach ($participants as $reg) {
            if ($reg->user) {
                NotificationService::sendTemplate(
                    $reg->user,
                    'webinar_reminder',
                    [
                        'nama_kegiatan' => $webinar->title,
                        'waktu_mulai'   => $webinar->event_date?->format('d M Y') . ' (' . ($webinar->time_start ?: '09:00') . ' WITA)',
                        'link_webinar'  => $webinar->webinar_url ?: route('webinars.show', $webinar->slug),
                        'nomor_tiket'   => $reg->ticket_code ?: 'TKT-' . $reg->id,
                    ],
                    route('webinars.show', $webinar->slug),
                    'video'
                );
                $count++;
            }
        }

        return redirect()->back()
            ->with('success', "Pengingat webinar '{$webinar->title}' berhasil dikirimkan ke {$count} peserta terdaftar via In-App & Email!");
    }

    /**
     * Factory default template data for reset functionality.
     */
    private static function getDefaultTemplateData(string $code): ?array
    {
        $defaults = [
            'registration_success' => [
                'subject' => 'Konfirmasi Pendaftaran: {nama_kegiatan} - LMS KAB Berau',
                'body_html' => "<p>Yth. <strong>{nama_peserta}</strong>,</p>
<p>Selamat! Pendaftaran Anda untuk kegiatan pembelajaran <strong>{nama_kegiatan}</strong> telah berhasil terkonfirmasi di sistem LMS Pemerintah Kabupaten Berau.</p>
<div style='background:#f1f5f9; border-left:4px solid #0e6c84; padding:12px 16px; margin:16px 0; border-radius:8px;'>
    <p style='margin:0 0 6px 0;'><strong>Detail Pendaftaran:</strong></p>
    <ul style='margin:0; padding-left:20px;'>
        <li><strong>Nama Peserta:</strong> {nama_peserta} ({nip})</li>
        <li><strong>Instansi / OPD:</strong> {instansi}</li>
        <li><strong>Kegiatan:</strong> {nama_kegiatan}</li>
        <li><strong>Nomor Tiket / Registrasi:</strong> {nomor_tiket}</li>
    </ul>
</div>
<p>Silakan akses modul pembelajaran dan ikuti tahapan belajar melalui tautan berikut:</p>
<p style='text-align:center; margin:24px 0;'><a href='{link_kegiatan}' style='background:#0e6c84; color:#ffffff; padding:12px 24px; text-decoration:none; border-radius:8px; font-weight:bold; display:inline-block;'>Mulai Pembelajaran Sekarang &rarr;</a></p>
<p>Salam hangat,<br><strong>Badan Kepegawaian dan Pengembangan Sumber Daya Manusia (BKPSDM)<br>Pemerintah Kabupaten Berau</strong></p>",
            ],
            'registration_approved' => [
                'subject' => 'Pendaftaran Disetujui: {nama_kegiatan} - LMS KAB Berau',
                'body_html' => "<p>Yth. <strong>{nama_peserta}</strong>,</p>
<p>Kabar baik! Pengajuan pendaftaran Anda untuk kegiatan <strong>{nama_kegiatan}</strong> telah <strong>DISETUJUI</strong> oleh Tim Pengelola Kepegawaian & Diklat BKPSDM.</p>
<div style='background:#ecfdf5; border-left:4px solid #059669; padding:12px 16px; margin:16px 0; border-radius:8px;'>
    <p style='margin:0 0 6px 0; color:#065f46;'><strong>Status Persetujuan: DISETUJUI & AKTIF</strong></p>
    <p style='margin:0; font-size:13px; color:#047857;'>Hak akses materi kursus dan pengakuan Jam Pelajaran (JP) Anda telah diaktifkan secara otomatis.</p>
</div>
<p style='text-align:center; margin:24px 0;'><a href='{link_kegiatan}' style='background:#059669; color:#ffffff; padding:12px 24px; text-decoration:none; border-radius:8px; font-weight:bold; display:inline-block;'>Masuk ke Ruang Kelas &rarr;</a></p>
<p>Salam sukses belajar,<br><strong>BKPSDM Kabupaten Berau</strong></p>",
            ],
            'activity_schedule' => [
                'subject' => 'Jadwal & Tiket Kegiatan: {nama_kegiatan} ({tanggal_kegiatan})',
                'body_html' => "<p>Yth. <strong>{nama_peserta}</strong>,</p>
<p>Berikut adalah pengingat jadwal dan rincian teknis pelaksanaan kegiatan pembelajaran yang akan Anda ikuti:</p>
<div style='background:#f8fafc; border:1px solid #cbd5e1; padding:16px; margin:16px 0; border-radius:12px;'>
    <h3 style='margin:0 0 10px 0; color:#0f172a; font-size:16px;'>{nama_kegiatan}</h3>
    <table style='width:100%; font-size:13px; line-height:1.6;'>
        <tr><td style='width:140px; color:#64748b;'>Tanggal & Waktu</td><td style='font-weight:bold;'>: {tanggal_kegiatan}</td></tr>
        <tr><td style='color:#64748b;'>Metode Pelaksanaan</td><td style='font-weight:bold;'>: {metode_kegiatan}</td></tr>
        <tr><td style='color:#64748b;'>Lokasi / Tautan Link</td><td style='font-weight:bold;'>: {lokasi_kegiatan}</td></tr>
        <tr><td style='color:#64748b;'>Nomor Tiket Anda</td><td style='font-weight:bold; color:#0e6c84;'>: {nomor_tiket}</td></tr>
        <tr><td style='color:#64748b;'>Bobot Pembelajaran</td><td style='font-weight:bold;'>: {bobot_jp} JP</td></tr>
    </table>
</div>
<p>Mohon hadir tepat waktu dan siapkan perangkat Anda untuk presensi digital berbasis QR Code di lokasi kegiatan.</p>
<p style='text-align:center; margin:24px 0;'><a href='{link_kegiatan}' style='background:#0e6c84; color:#ffffff; padding:12px 24px; text-decoration:none; border-radius:8px; font-weight:bold; display:inline-block;'>Buka Detail Tiket & Panduan &rarr;</a></p>",
            ],
            'webinar_reminder' => [
                'subject' => 'PENGINGAT: Webinar {nama_kegiatan} Dimulai {waktu_mulai}!',
                'body_html' => "<p>Yth. <strong>{nama_peserta}</strong>,</p>
<p>Ini adalah pengingat bahwa sesi webinar kedinasan <strong>{nama_kegiatan}</strong> akan segera dimulai pada:</p>
<div style='background:#fffbeb; border-left:4px solid #f59e0b; padding:14px 18px; margin:16px 0; border-radius:8px;'>
    <p style='margin:0; font-size:15px; font-weight:bold; color:#92400e;'>⏰ Waktu: {waktu_mulai}</p>
    <p style='margin:6px 0 0 0; color:#b45309;'>Tautan Masuk Room: <a href='{link_webinar}' style='color:#0e6c84; font-weight:bold;'>{link_webinar}</a></p>
    <p style='margin:6px 0 0 0; color:#b45309;'>Nomor Tiket: <strong>{nomor_tiket}</strong></p>
</div>
<p>Pastikan Anda telah memasang aplikasi konferensi video dan mengisi presensi QR pada sesi acara untuk memenuhi syarat perolehan sertifikat JP.</p>
<p style='text-align:center; margin:24px 0;'><a href='{link_webinar}' style='background:#f59e0b; color:#ffffff; padding:12px 24px; text-decoration:none; border-radius:8px; font-weight:bold; display:inline-block;'>Gabung ke Room Webinar Sekarang &rarr;</a></p>",
            ],
            'evaluation_reminder' => [
                'subject' => 'Pengingat Evaluasi & Kuis: Selesaikan untuk Menerbitkan E-Sertifikat Anda',
                'body_html' => "<p>Yth. <strong>{nama_peserta}</strong>,</p>
<p>Terima kasih telah mengikuti materi pada kegiatan <strong>{nama_kegiatan}</strong>.</p>
<p>Sistem mendeteksi bahwa Anda belum menuntaskan <strong>Evaluasi Pemahaman (Post-Test Kuis)</strong> dan/atau <strong>Kuesioner Umpan Balik Kegiatan</strong>.</p>
<div style='background:#fef2f2; border-left:4px solid #ef4444; padding:12px 16px; margin:16px 0; border-radius:8px;'>
    <p style='margin:0; font-size:13px; color:#991b1b;'><strong>Perhatian:</strong> Penerbitan E-Sertifikat dan pencatatan resmi Jam Pelajaran (JP) ke profil ASN Anda hanya akan diproses setelah evaluasi ini diselesaikan dengan nilai mencapai batas kelulusan (*passing grade*).</p>
</div>
<p style='text-align:center; margin:24px 0;'><a href='{link_evaluasi}' style='background:#0e6c84; color:#ffffff; padding:12px 24px; text-decoration:none; border-radius:8px; font-weight:bold; display:inline-block;'>Kerjakan Evaluasi Sekarang &rarr;</a></p>
<p>Salam hangat,<br><strong>Tim Penyelenggara Diklat BKPSDM</strong></p>",
            ],
            'certificate_available' => [
                'subject' => 'Selamat! E-Sertifikat Anda Telah Terbit: {nama_kegiatan} ({bobot_jp} JP)',
                'body_html' => "<p>Yth. <strong>{nama_peserta}</strong>,</p>
<p>Selamat atas keberhasilan Anda menuntaskan seluruh kurikulum dan evaluasi pada kegiatan <strong>{nama_kegiatan}</strong>!</p>
<div style='background:#ecfdf5; border:1px solid #a7f3d0; padding:16px; margin:16px 0; border-radius:12px; text-align:center;'>
    <span style='font-size:32px;'>🎓</span>
    <h3 style='margin:8px 0; color:#065f46;'>E-Sertifikat Kelulusan Resmi</h3>
    <p style='margin:0; font-size:13px; color:#047857;'>Nomor Registrasi: <strong>{nomor_sertifikat}</strong></p>
    <p style='margin:4px 0 0 0; font-size:13px; color:#047857;'>Kredit Kompetensi: <strong>+{bobot_jp} Jam Pelajaran (JP)</strong></p>
</div>
<p>Sertifikat ini telah terintegrasi dengan kode QR verifikasi publik dan secara otomatis menambah akumulasi pemenuhan target 20 JP tahunan Anda.</p>
<p style='text-align:center; margin:24px 0;'><a href='{link_sertifikat}' style='background:#059669; color:#ffffff; padding:12px 24px; text-decoration:none; border-radius:8px; font-weight:bold; display:inline-block;'>Unduh E-Sertifikat PDF Sah &rarr;</a></p>",
            ],
            'opd_approval' => [
                'subject' => 'Pemberitahuan Status Usulan Kegiatan OPD: {judul_usulan}',
                'body_html' => "<p>Yth. <strong>{nama_pengusul}</strong>,</p>
<p>Pengelola Bimbingan Teknis & Diklat BKPSDM Kabupaten Berau telah memperbarui status usulan kegiatan dari OPD <strong>{nama_opd}</strong>:</p>
<div style='background:#f8fafc; border-left:4px solid #0e6c84; padding:14px 18px; margin:16px 0; border-radius:8px;'>
    <p style='margin:0 0 4px 0; font-size:12px; color:#64748b;'>JUDUL USULAN KEGIATAN:</p>
    <p style='margin:0 0 10px 0; font-size:15px; font-weight:bold; color:#0f172a;'>{judul_usulan}</p>
    <p style='margin:0 0 4px 0; font-size:12px; color:#64748b;'>STATUS TERAKHIR:</p>
    <p style='margin:0 0 10px 0; font-size:14px; font-weight:bold; color:#0e6c84;'>{status_usulan}</p>
    <p style='margin:0 0 4px 0; font-size:12px; color:#64748b;'>CATATAN VERIFIKATOR BKPSDM:</p>
    <p style='margin:0; font-size:13px; color:#334155; font-style:italic;'>\"{catatan_review}\"</p>
</div>
<p style='text-align:center; margin:24px 0;'><a href='{link_usulan}' style='background:#0e6c84; color:#ffffff; padding:12px 24px; text-decoration:none; border-radius:8px; font-weight:bold; display:inline-block;'>Buka Detail Pengajuan Kegiatan &rarr;</a></p>
<p>Terima kasih atas kerja sama aktif dalam peningkatan kompetensi aparatur perangkat daerah.<br><strong>BKPSDM Kabupaten Berau</strong></p>",
            ],
        ];

        return $defaults[$code] ?? null;
    }
}

