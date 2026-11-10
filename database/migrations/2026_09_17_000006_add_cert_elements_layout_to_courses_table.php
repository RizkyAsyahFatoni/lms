<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->json('cert_elements_layout')->nullable()->after('cert_seal_text');
            $table->boolean('cert_show_header')->default(true)->after('cert_elements_layout');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['cert_elements_layout', 'cert_show_header']);
        });
    }
};
