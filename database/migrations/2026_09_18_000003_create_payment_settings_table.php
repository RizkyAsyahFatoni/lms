<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->default('midtrans'); // midtrans, xendit, tripay
            $table->boolean('is_active')->default(false);
            $table->string('environment')->default('sandbox'); // sandbox, production
            $table->string('merchant_id')->nullable();
            $table->string('client_key')->nullable();
            $table->string('server_key')->nullable();
            $table->string('webhook_secret')->nullable();
            $table->timestamps();
        });

        // Insert initial default record
        DB::table('payment_settings')->insert([
            'provider' => 'midtrans',
            'is_active' => false,
            'environment' => 'sandbox',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
