<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: NIK Encryption + NIP Blind Index
 *
 * Strategi enkripsi:
 * - NIK: disimpan terenkripsi (AES-256 via Laravel encrypt), ditambahkan kolom nik_hash (SHA-256 HMAC)
 *   untuk keperluan verifikasi identitas tanpa dekripsi.
 * - NIP: kolom tipe TEXT untuk menampung nilai terenkripsi (lebih panjang dari 30 char),
 *   ditambahkan kolom nip_hash (HMAC-SHA256) untuk pencarian yang aman.
 *
 * Setelah migration ini, kolom 'nip' dan 'nik' akan berisi ciphertext terenkripsi.
 * Seluruh akses melalui model Eloquent yang menggunakan Encrypted cast.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Tambah kolom hash untuk pencarian aman (blind index)
            //    HMAC-SHA256 dari NIK - tidak bisa di-reverse ke NIK asli
            $table->string('nik_hash', 64)->nullable()->index()->after('nik')
                ->comment('HMAC-SHA256 dari NIK untuk verifikasi identitas tanpa dekripsi');

            // 2. Ubah kolom NIK menjadi TEXT untuk menampung ciphertext (lebih panjang dari 20 char)
            $table->text('nik')->nullable()->change();

            // 3. Ubah kolom NIP menjadi TEXT untuk menampung ciphertext
            $table->text('nip')->nullable()->change();

            // 4. Tambah kolom nip_hash untuk pencarian aman
            $table->string('nip_hash', 64)->nullable()->index()->after('nip')
                ->comment('HMAC-SHA256 dari NIP untuk pencarian aman tanpa dekripsi');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nik_hash', 'nip_hash']);
            $table->string('nik', 20)->nullable()->change();
            $table->string('nip', 30)->nullable()->change();
        });
    }
};
