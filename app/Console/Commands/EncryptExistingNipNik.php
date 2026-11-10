<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Services\EncryptionService;

/**
 * Artisan command untuk mengenkripsi data NIP & NIK yang sudah ada di database.
 * Jalankan SATU KALI setelah migration berhasil.
 *
 * Usage:
 *   php artisan security:encrypt-nip-nik
 *   php artisan security:encrypt-nip-nik --dry-run   (test tanpa menyimpan)
 */
class EncryptExistingNipNik extends Command
{
    protected $signature = 'security:encrypt-nip-nik {--dry-run : Tampilkan apa yang akan dilakukan tanpa menyimpan}';

    protected $description = 'Enkripsi semua data NIP dan NIK yang ada di database (jalankan sekali setelah migration)';

    public function handle(EncryptionService $encryptionService): int
    {
        $isDryRun = $this->option('dry-run');

        $this->newLine();
        $this->line('🔐 <fg=yellow>LMS KAB Berau — NIP/NIK Encryption Tool</>');
        $this->line('   Standar: UU PDP No. 27/2022 | BSSN | OWASP ASVS Level 2');
        $this->newLine();

        if ($isDryRun) {
            $this->warn('⚠️  DRY RUN MODE: Tidak ada data yang akan disimpan');
            $this->newLine();
        }

        $users = DB::table('users')
            ->select('id', 'nip', 'nik')
            ->get();

        $totalUsers = $users->count();
        $encrypted = 0;
        $skipped = 0;
        $errors = 0;

        $this->info("Memproses {$totalUsers} pengguna...");
        $bar = $this->output->createProgressBar($totalUsers);
        $bar->start();

        foreach ($users as $user) {
            try {
                $updateData = [];
                $changed = false;

                // === Proses NIP ===
                if (!empty($user->nip)) {
                    // Cek apakah sudah terenkripsi (ciphertext dimulai dengan "eyJ" base64 encoded JSON)
                    if (!$this->isAlreadyEncrypted($user->nip)) {
                        $encryptedNip = $encryptionService->encryptNip($user->nip);
                        $nipHash = $encryptionService->hashNip($user->nip);

                        if ($encryptedNip) {
                            $updateData['nip'] = $encryptedNip;
                            $updateData['nip_hash'] = $nipHash;
                            $changed = true;
                        }
                    }
                }

                // === Proses NIK ===
                if (!empty($user->nik)) {
                    // Cek apakah sudah terenkripsi
                    if (!$this->isAlreadyEncrypted($user->nik)) {
                        $encryptedNik = $encryptionService->encryptNik($user->nik);
                        $nikHash = $encryptionService->hashNik($user->nik);

                        if ($encryptedNik) {
                            $updateData['nik'] = $encryptedNik;
                            $updateData['nik_hash'] = $nikHash;
                            $changed = true;
                        }
                    }
                }

                if ($changed) {
                    if (!$isDryRun) {
                        DB::table('users')
                            ->where('id', $user->id)
                            ->update($updateData);
                    }
                    $encrypted++;
                } else {
                    $skipped++;
                }
            } catch (\Exception $e) {
                $errors++;
                $this->newLine();
                $this->error("Error pada user ID {$user->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->table(
            ['Kategori', 'Jumlah'],
            [
                ['Total pengguna', $totalUsers],
                ['Berhasil dienkripsi', $encrypted],
                ['Dilewati (sudah terenkripsi/kosong)', $skipped],
                ['Error', $errors],
            ]
        );

        if ($isDryRun) {
            $this->newLine();
            $this->warn('DRY RUN selesai. Jalankan tanpa --dry-run untuk menyimpan perubahan.');
        } else {
            $this->newLine();
            $this->info('✅ Enkripsi selesai! Data NIP/NIK sekarang tersimpan terenkripsi (AES-256).');
            $this->newLine();
            $this->comment('Langkah selanjutnya:');
            $this->comment('  1. Verifikasi login masih berfungsi normal');
            $this->comment('  2. Verifikasi pencarian NIP di admin panel');
            $this->comment('  3. Backup database terenkripsi');
        }

        return Command::SUCCESS;
    }

    /**
     * Deteksi apakah nilai sudah terenkripsi dengan Laravel Crypt
     * Laravel Crypt menghasilkan JSON yang di-base64, dimulai dengan "eyJ"
     */
    protected function isAlreadyEncrypted(string $value): bool
    {
        if (strlen($value) < 50) {
            return false; // NIP/NIK asli tidak sepanjang ini
        }

        try {
            Crypt::decryptString($value);
            return true; // Berhasil decrypt = sudah terenkripsi
        } catch (\Exception) {
            return false; // Gagal decrypt = plaintext
        }
    }
}
