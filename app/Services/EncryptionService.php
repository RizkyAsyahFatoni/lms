<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

/**
 * Sensitive Data Encryption Service
 *
 * Mengelola enkripsi dan hashing data sensitif ASN:
 *
 * === NIK (Nomor Induk Kependudukan) ===
 * - Disimpan TERENKRIPSI di database (AES-256-CBC via Laravel Crypt)
 * - Tidak bisa dibaca tanpa APP_KEY
 * - Untuk verifikasi identitas: bandingkan nik_hash (HMAC-SHA256)
 *
 * === NIP (Nomor Induk Pegawai) ===
 * - Disimpan TERENKRIPSI di database
 * - Pencarian menggunakan nip_hash (blind index, HMAC-SHA256)
 * - Exact-match saja (tidak ada LIKE search)
 *
 * Standar: UU PDP No. 27/2022, BSSN, OWASP ASVS Level 2
 */
class EncryptionService
{
    /**
     * HMAC key turunan dari APP_KEY untuk blind indexing
     * Menggunakan turunan berbeda dari APP_KEY agar tidak ada reuse
     */
    protected function getHmacKey(): string
    {
        return hash_hmac('sha256', 'blind_index_v1', config('app.key'));
    }

    // =====================================================
    // NIK OPERATIONS
    // =====================================================

    /**
     * Enkripsi nilai NIK untuk disimpan ke database
     *
     * @param string|null $nik NIK plaintext (16 digit)
     * @return string|null Ciphertext terenkripsi
     */
    public function encryptNik(?string $nik): ?string
    {
        if (empty($nik)) {
            return null;
        }

        // Bersihkan: hanya angka
        $nik = preg_replace('/[^0-9]/', '', $nik);

        if (strlen($nik) !== 16) {
            // NIK tidak valid, jangan simpan
            return null;
        }

        return Crypt::encryptString($nik);
    }

    /**
     * Dekripsi nilai NIK dari database
     *
     * @param string|null $encryptedNik Ciphertext dari database
     * @return string|null NIK plaintext atau null jika gagal
     */
    public function decryptNik(?string $encryptedNik): ?string
    {
        if (empty($encryptedNik)) {
            return null;
        }

        try {
            return Crypt::decryptString($encryptedNik);
        } catch (\Exception $e) {
            Log::warning('[ENCRYPTION] Failed to decrypt NIK', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Buat HMAC-SHA256 dari NIK untuk blind index (pencarian/verifikasi)
     *
     * @param string|null $nik NIK plaintext
     * @return string|null Hash 64 karakter hex
     */
    public function hashNik(?string $nik): ?string
    {
        if (empty($nik)) {
            return null;
        }

        $nik = preg_replace('/[^0-9]/', '', $nik);

        return hash_hmac('sha256', $nik, $this->getHmacKey());
    }

    /**
     * Verifikasi NIK tanpa dekripsi (gunakan hash untuk perbandingan)
     */
    public function verifyNik(string $nikPlaintext, ?string $storedHash): bool
    {
        if (empty($storedHash)) {
            return false;
        }

        $hash = $this->hashNik($nikPlaintext);

        return hash_equals($storedHash, $hash);
    }

    // =====================================================
    // NIP OPERATIONS
    // =====================================================

    /**
     * Enkripsi nilai NIP untuk disimpan ke database
     *
     * @param string|null $nip NIP plaintext (18 digit standar BKN)
     * @return string|null Ciphertext terenkripsi
     */
    public function encryptNip(?string $nip): ?string
    {
        if (empty($nip)) {
            return null;
        }

        // Bersihkan: hanya angka
        $nip = preg_replace('/[^0-9]/', '', $nip);

        if (empty($nip)) {
            return null;
        }

        return Crypt::encryptString($nip);
    }

    /**
     * Dekripsi nilai NIP dari database
     */
    public function decryptNip(?string $encryptedNip): ?string
    {
        if (empty($encryptedNip)) {
            return null;
        }

        try {
            return Crypt::decryptString($encryptedNip);
        } catch (\Exception $e) {
            // Data belum dienkripsi (data lama sebelum enkripsi) - kembalikan as-is
            // Ini terjadi selama masa transisi migrasi data
            if (is_numeric($encryptedNip)) {
                return $encryptedNip;
            }

            Log::warning('[ENCRYPTION] Failed to decrypt NIP', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Buat HMAC-SHA256 dari NIP untuk blind index
     */
    public function hashNip(?string $nip): ?string
    {
        if (empty($nip)) {
            return null;
        }

        $nip = preg_replace('/[^0-9]/', '', $nip);

        return hash_hmac('sha256', $nip, $this->getHmacKey());
    }

    /**
     * Format tampilan NIP (dengan masking untuk privasi)
     * Contoh: "199001011990011001" → "1990010119900XXXXX"
     */
    public function maskNip(?string $nipPlaintext): string
    {
        if (empty($nipPlaintext)) {
            return '-';
        }

        $len = strlen($nipPlaintext);
        if ($len <= 8) {
            return str_repeat('*', $len);
        }

        return substr($nipPlaintext, 0, 13) . str_repeat('X', $len - 13);
    }

    /**
     * Format tampilan NIK (dengan masking untuk privasi)
     * Contoh: "3510091405880002" → "351009XXXXXXXX02"
     */
    public function maskNik(?string $nikPlaintext): string
    {
        if (empty($nikPlaintext)) {
            return '-';
        }

        $len = strlen($nikPlaintext);
        return substr($nikPlaintext, 0, 6) . str_repeat('X', $len - 8) . substr($nikPlaintext, -2);
    }
}
