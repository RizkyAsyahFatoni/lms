<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('cert_template_mode')->default('default')->after('cert_theme_color'); // 'default' or 'custom_bg'
            $table->string('cert_bg_image')->nullable()->after('cert_template_mode');
            $table->string('cert_font_family')->default('playfair')->after('cert_bg_image'); // playfair, great_vibes, cinzel, jakarta
            $table->string('cert_font_size')->default('large')->after('cert_font_family'); // normal, large, xlarge
            $table->string('cert_name_color', 30)->default('#0f172a')->after('cert_font_size');
            $table->integer('cert_name_vertical_pos')->default(46)->after('cert_name_color'); // Percentage 20 - 80
            $table->boolean('cert_show_qr')->default(true)->after('cert_name_vertical_pos');
            $table->boolean('cert_show_number')->default(true)->after('cert_show_qr');
            $table->boolean('cert_show_date')->default(true)->after('cert_show_number');
            $table->boolean('cert_show_seal')->default(true)->after('cert_show_date');
            $table->boolean('cert_show_jp')->default(true)->after('cert_show_seal');
            $table->string('cert_seal_text')->default('LMS BERAU VALID')->after('cert_show_jp');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'cert_template_mode',
                'cert_bg_image',
                'cert_font_family',
                'cert_font_size',
                'cert_name_color',
                'cert_name_vertical_pos',
                'cert_show_qr',
                'cert_show_number',
                'cert_show_date',
                'cert_show_seal',
                'cert_show_jp',
                'cert_seal_text',
            ]);
        });
    }
};
