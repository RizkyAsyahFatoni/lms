<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\SsoSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SsoAndApiIntegrationTest extends TestCase
{
    public function test_sso_settings_can_be_retrieved_and_updated_by_admin(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/sso');
        $response->assertStatus(200);
        $response->assertSee('Sistem SSO Ready (Single Sign-On)', false);

        // Test updating SSO Settings
        $updateResponse = $this->actingAs($admin)->post('/admin/sso', [
            'is_active' => '1',
            'provider_name' => 'Lembaga Administrasi Negara (LAN RI SSO)',
            'client_id' => 'lan-ri-client-123',
            'client_secret' => 'lan-ri-secret-456',
            'authorization_url' => 'https://sso.lan.go.id/oauth/authorize',
            'token_url' => 'https://sso.lan.go.id/oauth/token',
            'userinfo_url' => 'https://sso.lan.go.id/oauth/userinfo',
            'scopes' => 'openid profile email nip nik',
            'button_label' => 'Masuk dengan Akun ASN Digital LAN RI',
            'auto_register' => '1',
            'default_role' => 'student',
        ]);

        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('sso_settings', [
            'provider_name' => 'Lembaga Administrasi Negara (LAN RI SSO)',
            'client_id' => 'lan-ri-client-123',
        ]);
    }

    public function test_api_keys_can_be_created_and_managed(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/api-integrations');
        $response->assertStatus(200);
        $response->assertSee('API Gateway', false);
        $response->assertSee('API Gateway & Integrasi Sistem', false);

        $storeResponse = $this->actingAs($admin)->post('/admin/api-integrations', [
            'name' => 'SIMPEG BKPSDM Kabupaten Test',
            'allowed_ips' => '127.0.0.1, 10.0.0.1',
        ]);

        $storeResponse->assertRedirect();
        $this->assertDatabaseHas('api_keys', [
            'name' => 'SIMPEG BKPSDM Kabupaten Test',
        ]);
    }

    public function test_external_asn_api_rejects_unauthenticated_requests(): void
    {
        $response = $this->getJson('/api/v1/asn/participants');
        $response->assertStatus(401);
        $response->assertJsonStructure(['status', 'message']);
    }

    public function test_external_asn_api_returns_participants_with_jp_and_certificates(): void
    {
        $apiKey = ApiKey::firstOrCreate(
            ['name' => 'Automated Test Key'],
            [
                'token' => 'berau_lms_live_test_token_automated_unit_123',
                'is_active' => true,
            ]
        );

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $apiKey->token,
        ])->getJson('/api/v1/asn/participants');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'meta' => ['api_version', 'timestamp', 'total_results'],
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'nip',
                    'nik',
                    'email',
                    'metrics' => [
                        'total_jp',
                        'total_completed_courses',
                        'total_certificates',
                    ],
                    'completed_courses',
                ],
            ],
        ]);
    }

    public function test_external_asn_api_can_fetch_participant_by_nip(): void
    {
        $apiKey = ApiKey::first();

        $user = User::where('email', 'budi.santoso@dinkes.go.id')->first();
        if ($user) {
            $user->update(['nip' => '198805142014021003', 'role' => 'student']);
        } else {
            User::create([
                'name' => 'dr. Budi Santoso, Sp.A',
                'email' => 'budi.santoso@dinkes.go.id',
                'nip' => '198805142014021003',
                'password' => bcrypt('password'),
                'role' => 'student',
            ]);
        }

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $apiKey->token,
        ])->getJson('/api/v1/asn/participants/198805142014021003');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'data' => [
                'nip' => '198805142014021003',
            ],
        ]);
    }

    public function test_external_asn_api_can_verify_certificate(): void
    {
        $apiKey = ApiKey::first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $apiKey->token,
        ])->getJson('/api/v1/asn/certificates/verify/CERT-SATU-2026-00892');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'is_valid' => true,
        ]);
    }

    public function test_external_asn_api_stats(): void
    {
        $apiKey = ApiKey::first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $apiKey->token,
        ])->getJson('/api/v1/asn/stats');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'total_asn_participants',
                'total_courses_accredited',
                'total_certificates_issued',
                'total_jp_accumulated',
            ],
        ]);
    }
}
