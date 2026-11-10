<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * KAK Webinar & Kegiatan Pembelajaran:
     * Poin 3 : judul & deskripsi
     * Poin 4 : narasumber (speaker bio)
     * Poin 7 : tautan webinar (Zoom/GMeet URL)
     * Poin 8 : daftar peserta  → webinar_registrations
     * Poin 9 : presensi        → attended_at di registrations
     * Poin 10: materi          → webinar_materials
     * Poin 11: rekaman/video   → recording_url
     * Poin 12: evaluasi        → webinar_evaluations
     * Poin 13: sertifikat      → cert_available + cert_* fields
     * Poin 14: learning record → webinar_learning_records
     */
    public function up(): void
    {
        // 1. Tambah kolom baru ke tabel webinars
        Schema::table('webinars', function (Blueprint $table) {
            // Poin 3: Deskripsi kegiatan
            $table->text('description')->nullable()->after('title');

            // Poin 4: Narasumber tambahan (bio & institusi)
            $table->string('speaker_institution')->nullable()->after('speaker_title');
            $table->text('speaker_bio')->nullable()->after('speaker_institution');

            // Poin 7: Tautan webinar (link Zoom / GMeet / YouTube Live)
            $table->string('webinar_url')->nullable()->after('registration_link');

            // Poin 11: Rekaman / video setelah kegiatan
            $table->string('recording_url')->nullable()->after('webinar_url');

            // Poin 13: Sertifikat
            $table->boolean('cert_available')->default(false)->after('is_featured');
            $table->string('cert_signatory_name')->nullable()->after('cert_available');
            $table->string('cert_signatory_role')->nullable()->after('cert_signatory_name');
            $table->string('cert_signatory_nip')->nullable()->after('cert_signatory_role');
        });

        // 2. Poin 8 & 9: Daftar Peserta & Presensi
        Schema::create('webinar_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webinar_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Status pendaftaran
            $table->string('status')->default('registered'); // registered, confirmed, cancelled, waitlist
            $table->string('ticket_code')->nullable()->unique();

            // Poin 9: Presensi
            $table->boolean('is_attended')->default(false);
            $table->timestamp('attended_at')->nullable();
            $table->string('attendance_method')->nullable(); // 'qr_scan', 'manual', 'online_verify'
            $table->string('attendance_token')->nullable(); // token unik untuk self-checkin

            // Catatan tambahan
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->unique(['webinar_id', 'user_id']);
        });

        // 3. Poin 10: Materi Webinar (file/link yang dibagikan)
        Schema::create('webinar_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webinar_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('type')->default('document'); // 'document', 'video', 'link', 'presentation'
            $table->string('file_url')->nullable();       // URL Google Drive / S3 / lokal
            $table->string('external_url')->nullable();   // URL eksternal
            $table->integer('order')->default(0);
            $table->boolean('is_public')->default(false); // publik atau hanya peserta terdaftar
            $table->timestamps();
        });

        // 4. Poin 12: Evaluasi Webinar (reaksi peserta)
        Schema::create('webinar_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webinar_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Rating reaksi (1–5)
            $table->tinyInteger('rating_materi')->nullable();
            $table->tinyInteger('rating_narasumber')->nullable();
            $table->tinyInteger('rating_platform')->nullable();
            $table->tinyInteger('rating_relevansi')->nullable();
            $table->tinyInteger('rating_overall')->nullable();

            // Komentar terbuka
            $table->text('saran')->nullable();
            $table->text('komentar')->nullable();
            $table->boolean('will_recommend')->default(true);

            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['webinar_id', 'user_id']);
        });

        // 5. Poin 14: Learning Record Webinar
        Schema::create('webinar_learning_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webinar_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Rekaman aktivitas
            $table->boolean('registered')->default(true);
            $table->boolean('attended')->default(false);
            $table->boolean('material_downloaded')->default(false);
            $table->boolean('evaluation_submitted')->default(false);
            $table->boolean('certificate_issued')->default(false);

            // Data tambahan
            $table->integer('jp_earned')->default(0);    // Jam Pelajaran yang diperoleh
            $table->string('certificate_number')->nullable();

            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['webinar_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webinar_learning_records');
        Schema::dropIfExists('webinar_evaluations');
        Schema::dropIfExists('webinar_materials');
        Schema::dropIfExists('webinar_registrations');

        Schema::table('webinars', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'speaker_institution',
                'speaker_bio',
                'webinar_url',
                'recording_url',
                'cert_available',
                'cert_signatory_name',
                'cert_signatory_role',
                'cert_signatory_nip',
            ]);
        });
    }
};
