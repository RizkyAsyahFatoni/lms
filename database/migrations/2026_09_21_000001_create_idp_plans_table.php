<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('idp_plans', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 30)->index();
            $table->string('nama_asn')->nullable();
            $table->unsignedTinyInteger('tahun')->default(1)->comment('Tahun IDP ke-1 s/d ke-5');
            $table->string('judul_materi');
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->string('batch_id', 50)->nullable()->comment('Unique ID per sesi upload untuk grouping');
            $table->timestamps();

            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('idp_plans');
    }
};
