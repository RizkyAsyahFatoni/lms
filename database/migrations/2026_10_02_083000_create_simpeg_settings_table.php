<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('simpeg_settings')) {
            Schema::create('simpeg_settings', function (Blueprint $table) {
                $table->id();
                $table->boolean('is_active')->default(false);
                $table->string('provider_name')->default('SIMPEG BKPSDM Kabupaten');
                $table->string('server_url')->nullable(); // contoh: https://simpeg.kabupaten.go.id/api/v1
                $table->string('auth_type')->default('bearer'); // 'bearer', 'api_key_header', 'basic', 'none'
                $table->text('api_key')->nullable(); // Token atau API Key
                $table->string('header_name')->default('Authorization'); // Header key (e.g. Authorization, X-API-KEY)
                $table->string('endpoint_all')->default('/pegawai'); // Endpoint list semua pegawai
                $table->string('endpoint_single')->default('/pegawai/{nip}'); // Endpoint get pegawai per NIP
                $table->string('http_method')->default('GET'); // GET atau POST
                $table->json('field_mapping')->nullable(); // Mapping kolom JSON SIMPEG ke field LMS
                $table->string('data_wrapper')->default('data'); // Pembungkus response JSON (misal "data" atau null)
                $table->string('default_password')->default('Asn12345!');
                $table->boolean('auto_sync_on_login')->default(false); // Sinkronisasi otomatis saat ASN login
                $table->timestamp('last_sync_at')->nullable();
                $table->integer('last_sync_count')->default(0);
                $table->text('last_sync_message')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('simpeg_settings');
    }
};
