<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SimpegApiService;

class SimpegSyncCommand extends Command
{
    protected $signature = 'simpeg:sync 
                            {--all : Tarik dan sinkronkan seluruh pegawai dari REST API SIMPEG}
                            {--nip= : Tarik dan sinkronkan 1 pegawai spesifik berdasarkan NIP}
                            {--test : Uji koneksi ke endpoint SIMPEG tanpa menyimpan}';

    protected $description = 'Sinkronisasi data kepegawaian ASN dari REST API SIMPEG Kabupaten';

    public function handle(SimpegApiService $simpegService): int
    {
        $this->info("=== SINKRONISASI REST API SIMPEG KABUPATEN ===");

        // Mode 1: Test Connection
        if ($this->option('test')) {
            $this->info("Menguji koneksi ke endpoint SIMPEG...");
            $res = $simpegService->testConnection();
            if ($res['success']) {
                $this->info("✅ " . $res['message']);
                return Command::SUCCESS;
            }
            $this->error("❌ " . $res['message']);
            return Command::FAILURE;
        }

        // Mode 2: Sync By NIP
        if ($nip = $this->option('nip')) {
            $this->info("Menarik data pegawai dengan NIP: {$nip}...");
            $res = $simpegService->pullByNip($nip);
            if ($res['success']) {
                $this->info("✅ " . $res['message']);
                return Command::SUCCESS;
            }
            $this->error("❌ " . $res['message']);
            return Command::FAILURE;
        }

        // Mode 3: Sync All Pegawai
        $this->info("Memulai penarikan seluruh data pegawai dari SIMPEG...");
        $res = $simpegService->pullAllPegawai();

        if ($res['success']) {
            $this->info("✅ " . $res['message']);
            return Command::SUCCESS;
        }

        $this->error("❌ " . $res['message']);
        return Command::FAILURE;
    }
}
