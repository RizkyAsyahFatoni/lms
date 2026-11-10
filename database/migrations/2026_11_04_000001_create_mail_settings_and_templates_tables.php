<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('mail_settings')) {
            Schema::create('mail_settings', function (Blueprint $table) {
                $table->id();
                $table->boolean('is_active')->default(false); // Toggle pengiriman email aktif/nonaktif
                $table->string('mail_mailer')->default('smtp'); // smtp, sendmail, log
                $table->string('mail_host')->default('smtp.gmail.com');
                $table->integer('mail_port')->default(587);
                $table->string('mail_username')->nullable();
                $table->text('mail_password')->nullable();
                $table->string('mail_encryption')->default('tls'); // tls, ssl, null
                $table->string('mail_from_address')->default('bkpsdm@beraukab.go.id');
                $table->string('mail_from_name')->default('LMS KAB Berau - Corporate University');
                $table->timestamp('last_tested_at')->nullable();
                $table->string('last_test_status')->nullable(); // 'success', 'failed'
                $table->text('last_test_message')->nullable();
                $table->timestamps();
            });

            // Seed default row
            DB::table('mail_settings')->insert([
                'is_active' => false,
                'mail_mailer' => 'smtp',
                'mail_host' => 'smtp.gmail.com',
                'mail_port' => 587,
                'mail_username' => 'admin@beraukab.go.id',
                'mail_password' => null,
                'mail_encryption' => 'tls',
                'mail_from_address' => 'bkpsdm@beraukab.go.id',
                'mail_from_name' => 'LMS KAB Berau - BKPSDM',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (!Schema::hasTable('email_templates')) {
            Schema::create('email_templates', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique(); // registration_success, registration_approved, activity_schedule, webinar_reminder, evaluation_reminder, certificate_available, opd_approval
                $table->string('name');
                $table->string('category')->default('system'); // course, webinar, certificate, evaluation, approval, system
                $table->string('description')->nullable();
                $table->string('subject');
                $table->text('body_html');
                $table->json('available_variables')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            // 7 Template Bawaan Sesuai KAK
            $defaultTemplates = [
                [
                    'code' => 'registration_success',
                    'name' => '1. Pendaftaran Berhasil',
                    'category' => 'course',
                    'description' => 'Dikirim ke peserta saat berhasil mendaftar kursus mandiri atau webinar pembelajaran.',
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
                    'available_variables' => json_encode(['{nama_peserta}', '{nip}', '{instansi}', '{nama_kegiatan}', '{nomor_tiket}', '{link_kegiatan}']),
                ],
                [
                    'code' => 'registration_approved',
                    'name' => '2. Pendaftaran Disetujui',
                    'category' => 'approval',
                    'description' => 'Dikirim saat verifikator/admin menyetujui pendaftaran diklat atau akun kepegawaian ASN.',
                    'subject' => 'Pendaftaran Disetujui: {nama_kegiatan} - LMS KAB Berau',
                    'body_html' => "<p>Yth. <strong>{nama_peserta}</strong>,</p>
<p>Kabar baik! Pengajuan pendaftaran Anda untuk kegiatan <strong>{nama_kegiatan}</strong> telah <strong>DISETUJUI</strong> oleh Tim Pengelola Kepegawaian & Diklat BKPSDM.</p>
<div style='background:#ecfdf5; border-left:4px solid #059669; padding:12px 16px; margin:16px 0; border-radius:8px;'>
    <p style='margin:0 0 6px 0; color:#065f46;'><strong>Status Persetujuan: DISETUJUI & AKTIF</strong></p>
    <p style='margin:0; font-size:13px; color:#047857;'>Hak akses materi kursus dan pengakuan Jam Pelajaran (JP) Anda telah diaktifkan secara otomatis.</p>
</div>
<p style='text-align:center; margin:24px 0;'><a href='{link_kegiatan}' style='background:#059669; color:#ffffff; padding:12px 24px; text-decoration:none; border-radius:8px; font-weight:bold; display:inline-block;'>Masuk ke Ruang Kelas &rarr;</a></p>
<p>Salam sukses belajar,<br><strong>BKPSDM Kabupaten Berau</strong></p>",
                    'available_variables' => json_encode(['{nama_peserta}', '{nip}', '{instansi}', '{nama_kegiatan}', '{link_kegiatan}']),
                ],
                [
                    'code' => 'activity_schedule',
                    'name' => '3. Jadwal Kegiatan & Tiket',
                    'category' => 'webinar',
                    'description' => 'Memuat informasi detail jadwal pelaksanaan lokakarya webinar Zoom atau kelas tatap muka Sasana Praja.',
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
                    'available_variables' => json_encode(['{nama_peserta}', '{nama_kegiatan}', '{tanggal_kegiatan}', '{metode_kegiatan}', '{lokasi_kegiatan}', '{nomor_tiket}', '{bobot_jp}', '{link_kegiatan}']),
                ],
                [
                    'code' => 'webinar_reminder',
                    'name' => '4. Pengingat Webinar (H-1 / Hari H)',
                    'category' => 'webinar',
                    'description' => 'Dikirim sebelum sesi webinar daring Zoom dimulai agar peserta bersiap dan hadir tepat waktu.',
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
                    'available_variables' => json_encode(['{nama_peserta}', '{nama_kegiatan}', '{waktu_mulai}', '{link_webinar}', '{nomor_tiket}']),
                ],
                [
                    'code' => 'evaluation_reminder',
                    'name' => '5. Pengingat Evaluasi Pembelajaran & Kuis',
                    'category' => 'evaluation',
                    'description' => 'Mengingatkan peserta yang belum menyelesaikan post-test kuis atau evaluasi kepuasan diklat.',
                    'subject' => 'Pengingat Evaluasi & Kuis: Selesaikan untuk Menerbitkan E-Sertifikat Anda',
                    'body_html' => "<p>Yth. <strong>{nama_peserta}</strong>,</p>
<p>Terima kasih telah mengikuti materi pada kegiatan <strong>{nama_kegiatan}</strong>.</p>
<p>Sistem mendeteksi bahwa Anda belum menuntaskan <strong>Evaluasi Pemahaman (Post-Test Kuis)</strong> dan/atau <strong>Kuesioner Umpan Balik Kegiatan</strong>.</p>
<div style='background:#fef2f2; border-left:4px solid #ef4444; padding:12px 16px; margin:16px 0; border-radius:8px;'>
    <p style='margin:0; font-size:13px; color:#991b1b;'><strong>Perhatian:</strong> Penerbitan E-Sertifikat dan pencatatan resmi Jam Pelajaran (JP) ke profil ASN Anda hanya akan diproses setelah evaluasi ini diselesaikan dengan nilai mencapai batas kelulusan (*passing grade*).</p>
</div>
<p style='text-align:center; margin:24px 0;'><a href='{link_evaluasi}' style='background:#0e6c84; color:#ffffff; padding:12px 24px; text-decoration:none; border-radius:8px; font-weight:bold; display:inline-block;'>Kerjakan Evaluasi Sekarang &rarr;</a></p>
<p>Salam hangat,<br><strong>Tim Penyelenggara Diklat BKPSDM</strong></p>",
                    'available_variables' => json_encode(['{nama_peserta}', '{nama_kegiatan}', '{passing_grade}', '{link_evaluasi}']),
                ],
                [
                    'code' => 'certificate_available',
                    'name' => '6. Sertifikat Tersedia & Siap Diunduh',
                    'category' => 'certificate',
                    'description' => 'Dikirim ke peserta saat kelulusan terkonfirmasi dan e-sertifikat sah ber-QR Code telah terbit.',
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
                    'available_variables' => json_encode(['{nama_peserta}', '{nip}', '{nama_kegiatan}', '{nomor_sertifikat}', '{bobot_jp}', '{link_sertifikat}']),
                ],
                [
                    'code' => 'opd_approval',
                    'name' => '7. Persetujuan Usulan Kegiatan OPD',
                    'category' => 'approval',
                    'description' => 'Memberitahukan pejabat pengusul di OPD mengenai status persetujuan, permintaan revisi, atau penolakan pengajuan bimbingan teknis.',
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
                    'available_variables' => json_encode(['{nama_pengusul}', '{nama_opd}', '{judul_usulan}', '{status_usulan}', '{catatan_review}', '{link_usulan}']),
                ],
            ];

            foreach ($defaultTemplates as $tpl) {
                DB::table('email_templates')->insert(array_merge($tpl, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('mail_settings');
    }
};

