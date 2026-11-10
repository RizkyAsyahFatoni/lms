<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->default('umum')->index(); // regulasi, panduan, presentasi, best_practice, kebijakan, umum
            $table->string('opd_name')->nullable()->index();
            $table->string('author_name')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('cover_image')->nullable();
            $table->boolean('is_published')->default(true)->index();
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('article_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->string('title'); // e.g. "Perbup Nomor 12 Tahun 2025" atau "Slide Paparan Modul 1"
            $table->string('attachment_type')->default('dokumen')->index(); // regulasi, panduan, presentasi, modul, dokumen, lainnya
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->unsignedBigInteger('file_size')->default(0); // dalam bytes
            $table->string('file_extension', 20)->nullable(); // pdf, pptx, docx, xlsx, zip, dll
            $table->string('external_url')->nullable(); // Jika attachment dari link external
            $table->unsignedInteger('downloads_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_attachments');
        Schema::dropIfExists('articles');
    }
};
