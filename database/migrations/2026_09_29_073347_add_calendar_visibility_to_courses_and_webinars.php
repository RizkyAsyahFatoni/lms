<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan field visibility kalender ke tabel courses
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'show_in_calendar')) {
                $table->boolean('show_in_calendar')->default(true)->after('is_published');
            }
            if (!Schema::hasColumn('courses', 'calendar_visibility')) {
                // public: semua bisa lihat, private: hanya login, opd_only: hanya OPD tertentu
                $table->string('calendar_visibility')->default('public')->after('show_in_calendar');
            }
            if (!Schema::hasColumn('courses', 'calendar_opd_filter')) {
                // Jika visibility=opd_only, isi dengan nama OPD (pisahkan koma jika banyak)
                $table->string('calendar_opd_filter')->nullable()->after('calendar_visibility');
            }
            if (!Schema::hasColumn('courses', 'calendar_color')) {
                // Warna kalender untuk UI (hex atau nama tailwind)
                $table->string('calendar_color')->nullable()->after('calendar_opd_filter');
            }
        });
        
        // Tambahkan field visibility kalender ke tabel webinars
        Schema::table('webinars', function (Blueprint $table) {
            if (!Schema::hasColumn('webinars', 'show_in_calendar')) {
                $table->boolean('show_in_calendar')->default(true)->after('status');
            }
            if (!Schema::hasColumn('webinars', 'calendar_visibility')) {
                $table->string('calendar_visibility')->default('public')->after('show_in_calendar');
            }
            if (!Schema::hasColumn('webinars', 'calendar_opd_filter')) {
                $table->string('calendar_opd_filter')->nullable()->after('calendar_visibility');
            }
            if (!Schema::hasColumn('webinars', 'calendar_color')) {
                $table->string('calendar_color')->nullable()->after('calendar_opd_filter');
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $cols = ['show_in_calendar', 'calendar_visibility', 'calendar_opd_filter', 'calendar_color'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('courses', $col)) $table->dropColumn($col);
            }
        });
        Schema::table('webinars', function (Blueprint $table) {
            $cols = ['show_in_calendar', 'calendar_visibility', 'calendar_opd_filter', 'calendar_color'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('webinars', $col)) $table->dropColumn($col);
            }
        });
    }
};
