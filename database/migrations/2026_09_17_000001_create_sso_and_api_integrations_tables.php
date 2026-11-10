<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add NIP, NIK, SSO fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip', 30)->nullable()->index()->after('email');
            $table->string('nik', 20)->nullable()->index()->after('nip');
            $table->string('sso_provider')->nullable()->after('bio');
            $table->string('sso_id')->nullable()->after('sso_provider');
        });

        // 2. Add JP (Jam Pelajaran) to courses table
        Schema::table('courses', function (Blueprint $table) {
            $table->integer('jp')->default(20)->after('duration_hours'); // Standar Jam Pelajaran Diklat ASN
        });

        // 3. SSO Dynamic Settings Table
        Schema::create('sso_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(false);
            $table->string('provider_name')->default('SSO ASN Instansi Daerah');
            $table->string('client_id')->nullable();
            $table->text('client_secret')->nullable();
            $table->string('authorization_url')->nullable();
            $table->string('token_url')->nullable();
            $table->string('userinfo_url')->nullable();
            $table->string('scopes')->default('openid profile email nip nik');
            $table->string('button_label')->default('Masuk dengan Akun SSO ASN');
            $table->boolean('auto_register')->default(true);
            $table->string('default_role')->default('student');
            $table->timestamps();
        });

        // 4. API Keys for External ASN Applications (SIMPEG, SIASN, e-Kinerja)
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. "SIMPEG BKPSDM Banyuwangi"
            $table->string('token', 80)->unique();
            $table->string('allowed_ips')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_keys');
        Schema::dropIfExists('sso_settings');

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('jp');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nip', 'nik', 'sso_provider', 'sso_id']);
        });
    }
};
