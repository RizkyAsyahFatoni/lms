<?php

namespace App\Services;

use App\Models\SimpegSetting;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SimpegApiService
{
    protected SimpegSetting $setting;
    protected EncryptionService $enc;

    public function __construct(EncryptionService $enc)
    {
        $this->setting = SimpegSetting::getSettings();
        $this->enc = $enc;
    }

    /**
     * Dapatkan instance konfigurasi SIMPEG saat ini
     */
    public function getSetting(): SimpegSetting
    {
        return $this->setting;
    }

    /**
     * Build HTTP Client dengan Header & Auth sesuai konfigurasi SIMPEG
     */
    protected function buildHttpClient(): \Illuminate\Http\Client\PendingRequest
    {
        $http = Http::timeout(30)->withHeaders([
            'Accept'     => 'application/json',
            'User-Agent' => 'LMS-KAB-Berau-BKPSDM/2.0',
        ]);

        $authType = $this->setting->auth_type;
        $token = trim((string) $this->setting->api_key);
        $headerName = trim((string) $this->setting->header_name) ?: 'Authorization';

        if ($authType === 'bearer' && !empty($token)) {
            $http->withToken($token);
        } elseif ($authType === 'api_key_header' && !empty($token)) {
            $http->withHeaders([$headerName => $token]);
        } elseif ($authType === 'basic' && !empty($token)) {
            // Jika token berformat username:password
            $parts = explode(':', $token, 2);
            $user = $parts[0] ?? '';
            $pass = $parts[1] ?? '';
            $http->withBasicAuth($user, $pass);
        }

        return $http;
    }

    /**
     * Format URL endpoint lengkap
     */
    protected function formatUrl(string $endpoint): string
    {
        $baseUrl = rtrim((string) $this->setting->server_url, '/');
        $cleanEndpoint = '/' . ltrim($endpoint, '/');
        return $baseUrl . $cleanEndpoint;
    }

    /**
     * Uji Koneksi ke REST API SIMPEG (Ping Test)
     */
    public function testConnection(): array
    {
        if (empty($this->setting->server_url)) {
            return [
                'success'     => false,
                'status_code' => 0,
                'message'     => 'Server URL SIMPEG belum dikonfigurasi.',
                'sample_data' => null,
            ];
        }

        try {
            $url = $this->formatUrl($this->setting->endpoint_all);
            $http = $this->buildHttpClient();

            $response = $http->get($url, ['limit' => 2]);

            $statusCode = $response->status();
            $body = $response->json();

            if ($response->successful()) {
                // Ekstrak sampel data
                $sample = $this->extractDataArray($body);
                $firstRow = is_array($sample) && isset($sample[0]) ? $sample[0] : $sample;

                return [
                    'success'     => true,
                    'status_code' => $statusCode,
                    'message'     => 'Koneksi ke REST API SIMPEG Berhasil! Server merespons HTTP ' . $statusCode,
                    'sample_data' => $firstRow,
                    'raw_json'    => $body,
                ];
            }

            return [
                'success'     => false,
                'status_code' => $statusCode,
                'message'     => 'Server SIMPEG merespons dengan kode kesalahan HTTP ' . $statusCode . ': ' . ($body['message'] ?? $response->body()),
                'sample_data' => null,
            ];
        } catch (\Throwable $e) {
            return [
                'success'     => false,
                'status_code' => 500,
                'message'     => 'Gagal menghubungi server SIMPEG: ' . $e->getMessage(),
                'sample_data' => null,
            ];
        }
    }

    /**
     * Ekstrak list array pegawai dari response JSON (membuka pembungkus "data" jika ada)
     */
    protected function extractDataArray($responseJson): array
    {
        if (!is_array($responseJson)) {
            return [];
        }

        $wrapper = trim((string) $this->setting->data_wrapper);

        if (!empty($wrapper) && isset($responseJson[$wrapper]) && is_array($responseJson[$wrapper])) {
            return $responseJson[$wrapper];
        }

        // Cek jika response langsung berbentuk array numerik
        if (array_is_list($responseJson)) {
            return $responseJson;
        }

        // Cek umum: jika dibungkus 'data.pegawai' atau 'results'
        if (isset($responseJson['results']) && is_array($responseJson['results'])) {
            return $responseJson['results'];
        }

        if (isset($responseJson['pegawai']) && is_array($responseJson['pegawai'])) {
            return $responseJson['pegawai'];
        }

        return [$responseJson];
    }

    /**
     * Tarik Seluruh Data Pegawai dari REST API SIMPEG (Sync All)
     */
    public function pullAllPegawai(?int $limit = null): array
    {
        if (empty($this->setting->server_url)) {
            return [
                'success' => false,
                'message' => 'URL Server SIMPEG belum dikonfigurasi.',
            ];
        }

        try {
            $url = $this->formatUrl($this->setting->endpoint_all);
            $http = $this->buildHttpClient();

            $params = [];
            if ($limit) {
                $params['limit'] = $limit;
            }

            $response = $http->get($url, $params);

            if (!$response->successful()) {
                $msg = "Gagal mengambil data dari SIMPEG (HTTP {$response->status()}): " . $response->body();
                $this->updateSyncLog(0, $msg);
                return ['success' => false, 'message' => $msg];
            }

            $json = $response->json();
            $items = $this->extractDataArray($json);

            if (empty($items)) {
                $msg = 'API SIMPEG terhubung, namun tidak ada data pegawai yang diterima.';
                $this->updateSyncLog(0, $msg);
                return ['success' => false, 'message' => $msg];
            }

            $created = 0;
            $updated = 0;
            $skipped = 0;

            foreach ($items as $item) {
                if (!is_array($item)) {
                    $skipped++;
                    continue;
                }

                $status = $this->syncSinglePegawai($item);
                if ($status === 'created') $created++;
                elseif ($status === 'updated') $updated++;
                else $skipped++;
            }

            $totalProcessed = $created + $updated;
            $summaryMsg = "Sinkronisasi Berhasil! {$created} ASN baru ditambahkan, {$updated} ASN diperbarui data mutasinya.";
            if ($skipped > 0) {
                $summaryMsg .= " ({$skipped} data dilewati karena NIP/Email tidak valid).";
            }

            $this->updateSyncLog($totalProcessed, $summaryMsg);

            ActivityLog::record(
                'simpeg_sync',
                "Sinkronisasi REST API SIMPEG: {$created} ASN baru, {$updated} diperbarui",
                auth()->id() ?? null,
                ['created' => $created, 'updated' => $updated, 'skipped' => $skipped]
            );

            return [
                'success' => true,
                'created' => $created,
                'updated' => $updated,
                'skipped' => $skipped,
                'message' => $summaryMsg,
            ];
        } catch (\Throwable $e) {
            $err = 'Error saat sinkronisasi API SIMPEG: ' . $e->getMessage();
            Log::error($err);
            $this->updateSyncLog(0, $err);
            return ['success' => false, 'message' => $err];
        }
    }

    /**
     * Tarik Data 1 Pegawai Berdasarkan NIP dari REST API SIMPEG
     */
    public function pullByNip(string $nip): array
    {
        $cleanNip = preg_replace('/[^0-9]/', '', $nip);
        if (empty($cleanNip)) {
            return ['success' => false, 'message' => 'NIP tidak valid.'];
        }

        try {
            $endpoint = str_replace('{nip}', $cleanNip, (string) $this->setting->endpoint_single);
            $url = $this->formatUrl($endpoint);
            $http = $this->buildHttpClient();

            $response = $http->get($url);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => "Pegawai dengan NIP {$cleanNip} tidak ditemukan di SIMPEG (HTTP {$response->status()}).",
                ];
            }

            $json = $response->json();
            $data = is_array($json) && isset($json['data']) ? $json['data'] : $json;

            // Jika dibungkus array
            if (isset($data[0]) && is_array($data[0])) {
                $data = $data[0];
            }

            $status = $this->syncSinglePegawai($data);
            if ($status === 'skipped') {
                return ['success' => false, 'message' => "Data pegawai diterima namun NIP/Email tidak valid."];
            }

            $user = User::where('nip_hash', $this->enc->hashNip($cleanNip))->first();

            return [
                'success' => true,
                'action'  => $status,
                'user'    => $user,
                'message' => "Data pegawai [{$user->name}] (NIP: {$cleanNip}) berhasil disinkronkan dari SIMPEG!",
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Gagal menarik NIP dari SIMPEG: ' . $e->getMessage()];
        }
    }

    /**
     * Eksekusi Upsert 1 Pegawai ke Database LMS
     */
    public function syncSinglePegawai(array $item): string
    {
        $map = $this->setting->getActiveFieldMapping();

        // Ambil nilai berdasarkan mapping (case-insensitive key search)
        $nipRaw = $this->getValueByMappedKey($item, $map['nip'] ?? 'nip');
        $nipClean = $nipRaw ? preg_replace('/[^0-9]/', '', (string)$nipRaw) : null;

        $name = $this->getValueByMappedKey($item, $map['name'] ?? 'nama_lengkap')
            ?: $this->getValueByMappedKey($item, 'nama')
            ?: $this->getValueByMappedKey($item, 'name');

        $email = $this->getValueByMappedKey($item, $map['email'] ?? 'email');
        if (!empty($email)) {
            $email = strtolower(trim((string)$email));
        }

        // Jika email kosong tapi NIP ada, gunakan email format standar NIP
        if (empty($email) && !empty($nipClean)) {
            $email = "{$nipClean}@lms.local";
        }

        if (empty($nipClean) && empty($email)) {
            return 'skipped';
        }

        $opdName = $this->getValueByMappedKey($item, $map['opd_name'] ?? 'nama_opd')
            ?: $this->getValueByMappedKey($item, 'opd')
            ?: $this->getValueByMappedKey($item, 'skpd');

        $bagian = $this->getValueByMappedKey($item, $map['bagian'] ?? 'bagian')
            ?: $this->getValueByMappedKey($item, 'bidang')
            ?: $this->getValueByMappedKey($item, 'unit_kerja');

        $jabatan = $this->getValueByMappedKey($item, $map['jabatan'] ?? 'jabatan')
            ?: $this->getValueByMappedKey($item, 'nama_jabatan');

        $pangkatGol = $this->getValueByMappedKey($item, $map['pangkat_golongan'] ?? 'pangkat_golongan')
            ?: $this->getValueByMappedKey($item, 'golongan');

        $phone = $this->getValueByMappedKey($item, $map['phone'] ?? 'nomor_hp')
            ?: $this->getValueByMappedKey($item, 'no_hp')
            ?: $this->getValueByMappedKey($item, 'telepon');

        // Cari user yang sudah ada berdasarkan Blind Index NIP atau Email
        $existingUser = null;
        if (!empty($nipClean)) {
            $nipHash = $this->enc->hashNip($nipClean);
            $existingUser = User::where('nip_hash', $nipHash)->first();
        }
        if (!$existingUser && !empty($email)) {
            $existingUser = User::where('email', $email)->first();
        }

        if ($existingUser) {
            // SINKRONISASI / MUTASI: Update OPD, Bagian, Jabatan, Pangkat tanpa merusak riwayat kursus
            $existingUser->update([
                'name'             => $name ?: $existingUser->name,
                'opd_name'         => $opdName ?: $existingUser->opd_name,
                'bagian'           => $bagian ?: $existingUser->bagian,
                'jabatan'          => $jabatan ?: $existingUser->jabatan,
                'pangkat_golongan' => $pangkatGol ?: $existingUser->pangkat_golongan,
                'phone'            => $phone ?: $existingUser->phone,
                'headline'         => $jabatan ? "{$jabatan} di " . ($opdName ?: $existingUser->opd_name) : $existingUser->headline,
            ]);

            return 'updated';
        }

        // BUAT USER ASN BARU
        $defaultPassword = $this->setting->default_password ?: 'Asn12345!';
        User::create([
            'name'             => $name ?: "ASN {$nipClean}",
            'email'            => $email,
            'password'         => Hash::make($defaultPassword),
            'role'             => 'asn_learner',
            'nip'              => $nipClean,
            'opd_name'         => $opdName,
            'bagian'           => $bagian,
            'jabatan'          => $jabatan,
            'pangkat_golongan' => $pangkatGol,
            'phone'            => $phone,
            'headline'         => $jabatan ? "{$jabatan} di {$opdName}" : null,
            'is_active'        => true,
        ]);

        return 'created';
    }

    /**
     * Ambil nilai dari array JSON dengan toleransi case-insensitive
     */
    protected function getValueByMappedKey(array $item, ?string $mappedKey): ?string
    {
        if (empty($mappedKey)) {
            return null;
        }

        // 1. Direct match
        if (isset($item[$mappedKey]) && !is_array($item[$mappedKey])) {
            return trim((string)$item[$mappedKey]);
        }

        // 2. Case insensitive match
        $lowerMapped = strtolower($mappedKey);
        foreach ($item as $k => $v) {
            if (strtolower($k) === $lowerMapped && !is_array($v)) {
                return trim((string)$v);
            }
        }

        return null;
    }

    /**
     * Catat status sinkronisasi ke tabel simpeg_settings
     */
    protected function updateSyncLog(int $count, string $message): void
    {
        $this->setting->update([
            'last_sync_at'      => now(),
            'last_sync_count'   => $count,
            'last_sync_message' => $message,
        ]);
    }
}
