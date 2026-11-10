<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->integer('jp_year_start_month')->default(1)->after('primary_color'); // 1=Jan-Dec, 4=Apr-Mar
            $table->integer('jp_default_target')->default(20)->after('jp_year_start_month'); // Default target JP
        });
    }

    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn(['jp_year_start_month', 'jp_default_target']);
        });
    }
};
