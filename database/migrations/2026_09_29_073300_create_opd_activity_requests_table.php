<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel pengajuan kegiatan oleh Admin OPD ke BKPSDM (KAK Pasal VII)
        Schema::create('opd_activity_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submitted_by')->constrained('users')->cascadeOnDelete(); // Admin OPD
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();   // Course terkait (jika sudah dibuat)
            
            // Informasi Kegiatan Usulan
            $table->string('title');                        // Judul Kegiatan / Diklat
            $table->text('description')->nullable();        // Deskripsi Kegiatan
            $table->string('opd_name');                     // Nama OPD Pengusul
            $table->string('learning_method')->nullable();  // Metode: e-course, webinar, in-class, dll
            $table->string('course_type')->nullable();      // Jenis kegiatan
            $table->integer('jp')->nullable();              // Jumlah JP yang diusulkan
            $table->string('target_audience')->nullable();  // Sasaran peserta
            $table->text('objectives')->nullable();         // Tujuan pembelajaran
            $table->string('speaker_name')->nullable();     // Narasumber
            $table->date('proposed_start_date')->nullable();
            $table->date('proposed_end_date')->nullable();
            $table->string('proposed_location')->nullable();
            $table->integer('estimated_participants')->nullable(); // Estimasi peserta
            $table->string('budget_source')->nullable();    // Sumber anggaran
            $table->text('supporting_notes')->nullable();   // Catatan pendukung
            $table->string('attachment_path')->nullable();  // File lampiran (TOR, dll)
            
            // Status Alur Approval
            $table->enum('status', ['draft', 'pending', 'under_review', 'approved', 'rejected', 'revision_needed'])
                  ->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete(); // Super Admin / BKPSDM
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();       // Catatan review / alasan penolakan
            $table->text('revision_request')->nullable();   // Permintaan revisi jika status revision_needed
            
            $table->timestamps();
        });
        
        // Tambah kolom opd_request_id ke courses agar bisa dilacak dari mana kursus ini berasal
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'opd_request_id')) {
                $table->unsignedBigInteger('opd_request_id')->nullable()->after('created_by');
                $table->string('submitter_opd')->nullable()->after('opd_request_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'opd_request_id')) {
                $table->dropColumn(['opd_request_id', 'submitter_opd']);
            }
        });
        Schema::dropIfExists('opd_activity_requests');
    }
};
