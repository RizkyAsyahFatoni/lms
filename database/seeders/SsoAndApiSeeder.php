<?php

namespace Database\Seeders;

use App\Models\ApiKey;
use App\Models\Course;
use App\Models\SsoSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class SsoAndApiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure Default SSO Settings are initialized
        SsoSetting::updateOrCreate(
            ['id' => 1],
            [
                'provider_name' => 'SSO ASN Instansi Daerah',
                'client_id' => 'sso-berau-client-id',
                'client_secret' => 'sso-berau-secret-key-xyz987',
                'authorization_url' => 'https://sso.beraukab.go.id/oauth2/auth',
                'token_url' => 'https://sso.beraukab.go.id/oauth2/token',
                'userinfo_url' => 'https://sso.beraukab.go.id/oauth2/userinfo',
                'scopes' => 'openid profile email nip nik',
                'button_label' => 'Masuk dengan Akun SSO ASN',
                'is_active' => true,
                'auto_register' => true,
                'default_role' => 'student',
            ]
        );

        // 2. Ensure Default API Key for ASN (SIASN BKN / SIMPEG) is ready
        ApiKey::updateOrCreate(
            ['name' => 'Integrasi SIASN BKN & SIMPEG Daerah'],
            [
                'token' => 'berau_lms_live_demo_asn_bkn_siasn_key_2026',
                'allowed_ips' => null,
                'is_active' => true,
            ]
        );

        // 3. Update Courses with JP (Jam Pelajaran)
        $courseJps = [
            1 => 24, // 24 JP
            2 => 32, // 32 JP
            3 => 16, // 16 JP
            4 => 48, // 48 JP
        ];

        foreach ($courseJps as $courseId => $jp) {
            Course::where('id', $courseId)->update(['jp' => $jp]);
        }
        Course::whereNull('jp')->orWhere('jp', 0)->update(['jp' => 20]);

        // 4. Update Existing Users with NIP & NIK
        $demoUsers = [
            'budi@example.com' => [
                'nip' => '198805142014021003',
                'nik' => '3510091405880002',
                'sso_provider' => 'sso_berau',
                'sso_id' => 'SSO-ASN-880514',
            ],
            'admin@example.com' => [
                'nip' => '197501012000031001',
                'nik' => '3510010101750001',
                'sso_provider' => 'internal',
                'sso_id' => 'SSO-ADMIN-001',
            ],
        ];

        foreach ($demoUsers as $email => $data) {
            User::where('email', $email)->update($data);
        }
    }
}
