<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * KAK PRESENSI DIGITAL:
     * Metode: QR Code dan Tombol Presensi
     * Pencatatan: Nama peserta, Kegiatan, Tanggal, Waktu, Status kehadiran, Durasi/kehadiran sesuai konfigurasi.
     */
    public function up(): void
    {
        // 1. Sesi & Konfigurasi Presensi Digital
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Nama kegiatan/sesi
            $table->string('session_code', 40)->unique(); // Kode sesi misal PRES-WBN-8291
            $table->nullableMorphs('attendable'); // Polimorfik ke Webinar atau Course
            $table->date('date'); // Tanggal kegiatan
            $table->time('time_start')->nullable(); // Waktu mulai kegiatan
            $table->time('time_end')->nullable();   // Waktu selesai kegiatan
            $table->timestamp('open_at')->nullable();  // Waktu jendela presensi dibuka
            $table->timestamp('close_at')->nullable(); // Waktu jendela presensi ditutup
            $table->integer('tolerance_late_minutes')->default(15); // Toleransi keterlambatan (menit)
            $table->integer('min_duration_minutes')->default(60);   // Durasi kehadiran minimal (menit)
            $table->string('allowed_methods')->default('both');     // 'both', 'qr_code', 'button'
            $table->boolean('is_active')->default(true);           // Status pembukaan presensi
            $table->string('qr_secret_token', 64)->unique();       // Token rahasia QR code
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Pencatatan Kehadiran Digital (Presensi)
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_session_id')->nullable()->constrained('attendance_sessions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            // Pencatatan Peserta (Snapshot untuk arsip & ekspor cepat)
            $table->string('participant_name');
            $table->string('participant_nip')->nullable();
            $table->string('participant_opd')->nullable();
            $table->string('participant_jabatan')->nullable();

            // Pencatatan Kegiatan
            $table->nullableMorphs('attendable');
            $table->string('activity_title');
            $table->string('session_code', 40)->nullable();

            // Pencatatan Waktu & Tanggal
            $table->date('date');
            $table->time('time'); // Waktu presensi dicatat
            $table->timestamp('check_in_at');
            $table->timestamp('check_out_at')->nullable();

            // Pencatatan Status Kehadiran
            // 'present' (Hadir Tepat Waktu), 'late' (Terlambat), 'excused' (Izin), 'sick' (Sakit), 'absent' (Alpa)
            $table->string('status')->default('present');

            // Metode Presensi ('qr_code', 'button', 'manual')
            $table->string('method')->default('button');

            // Durasi & Kehadiran Sesuai Konfigurasi
            $table->integer('duration_minutes')->default(0);
            $table->integer('min_duration_minutes')->default(60);
            $table->boolean('is_duration_qualified')->default(true);

            // Audit & Keamanan
            $table->string('qr_token', 64)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            // Mencegah duplikasi presensi per sesi dan per user
            $table->unique(['attendance_session_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('attendance_sessions');
    }
};
