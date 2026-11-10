<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom 'bagian' di tabel users setelah 'opd_name'
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'bagian')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('bagian')->nullable()->after('opd_name');
            });
        }

        // 2. Buat tabel profile_change_requests untuk alur verifikasi data kepegawaian ASN
        if (!Schema::hasTable('profile_change_requests')) {
            Schema::create('profile_change_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->json('proposed_changes'); // Data baru yang diajukan (opd_name, bagian, jabatan, pangkat_golongan, nip, name)
                $table->json('old_values')->nullable(); // Snapshot data lama sebelum diajukan
                $table->string('reason')->nullable(); // Alasan perubahan (contoh: "SK Mutasi No...")
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable();
                $table->text('admin_notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_change_requests');

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'bagian')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('bagian');
            });
        }
    }
};
