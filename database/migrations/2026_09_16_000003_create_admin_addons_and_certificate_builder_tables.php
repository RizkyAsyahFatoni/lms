<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Homepage Sliders / Hero Banners
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('subtitle')->nullable();
            $table->string('badge_text')->nullable();
            $table->string('image_url')->nullable();
            $table->string('cta_text')->default('Mulai Belajar Sekarang');
            $table->string('cta_link')->default('/courses');
            $table->integer('order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Webinars & Incoming Agenda
        Schema::create('webinars', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('speaker_name');
            $table->string('speaker_title')->nullable();
            $table->string('speaker_avatar')->nullable();
            $table->date('event_date');
            $table->string('time_start', 10)->default('09:00');
            $table->string('time_end', 10)->default('11:30');
            $table->string('location_type')->default('Online via Zoom & LMS Berau Live');
            $table->string('registration_link')->nullable();
            $table->string('cover_image')->nullable();
            $table->integer('quota')->default(500);
            $table->decimal('price', 10, 2)->default(0);
            $table->string('status')->default('upcoming'); // upcoming, ongoing, completed
            $table->boolean('is_featured')->default(true);
            $table->timestamps();
        });

        // 3. Homepage Modal Popups
        Schema::create('popups', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('badge_text')->nullable()->default('PENGUMUMAN PENTING');
            $table->text('content')->nullable();
            $table->string('image_url')->nullable();
            $table->string('button_text')->default('Pelajari Selengkapnya');
            $table->string('button_link')->default('/courses');
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });

        // 4. Certificate Builder Columns for Courses
        Schema::table('courses', function (Blueprint $table) {
            $table->string('cert_title')->default('SERTIFIKAT KOMPETENSI RESMI')->after('certificate_available');
            $table->string('cert_institution_name')->default('BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA')->after('cert_title');
            $table->string('cert_institution_sub')->default('Pusat Pengembangan Kompetensi Digital & RME Terpadu')->after('cert_institution_name');
            $table->string('cert_signatory_name')->nullable()->after('cert_institution_sub');
            $table->string('cert_signatory_role')->nullable()->after('cert_signatory_name');
            $table->string('cert_signatory_nip')->nullable()->after('cert_signatory_role');
            $table->string('cert_signature_image')->nullable()->after('cert_signatory_nip');
            $table->string('cert_logo_url')->nullable()->after('cert_signature_image');
            $table->string('cert_theme_color')->default('teal')->after('cert_logo_url');
            $table->text('cert_statement')->nullable()->after('cert_theme_color');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('popups');
        Schema::dropIfExists('webinars');
        Schema::dropIfExists('sliders');

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'cert_title',
                'cert_institution_name',
                'cert_institution_sub',
                'cert_signatory_name',
                'cert_signatory_role',
                'cert_signatory_nip',
                'cert_signature_image',
                'cert_logo_url',
                'cert_theme_color',
                'cert_statement',
            ]);
        });
    }
};
