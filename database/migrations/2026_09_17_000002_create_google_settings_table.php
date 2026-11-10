<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(false);
            $table->string('client_id')->nullable();
            $table->text('client_secret')->nullable();
            $table->string('button_label')->default('Masuk dengan Akun Google');
            $table->boolean('auto_register')->default(true);
            $table->string('default_role')->default('student');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_settings');
    }
};
