<?php

namespace Tests\Feature;

use App\Models\GoogleSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleAuthAndApiIntegrationTest extends TestCase
{
    public function test_google_login_button_is_visible_on_login_page_when_active(): void
    {
        $setting = GoogleSetting::getSettings();
        $setting->update([
            'is_active' => true,
            'button_label' => 'Masuk dengan Akun Google Resmi',
        ]);

        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Masuk dengan Akun Google Resmi', false);
        $response->assertSee(route('auth.google.redirect'));
    }

    public function test_google_redirect_points_to_google_oauth(): void
    {
        $setting = GoogleSetting::getSettings();
        $setting->update([
            'is_active' => true,
            'client_id' => 'test-google-client-id-123.apps.googleusercontent.com',
        ]);

        $response = $this->get('/auth/google/redirect');
        $response->assertStatus(302);
        
        $location = $response->headers->get('Location');
        $this->assertStringContainsString('accounts.google.com/o/oauth2/v2/auth', $location);
        $this->assertStringContainsString('client_id=test-google-client-id-123.apps.googleusercontent.com', $location);
        $this->assertStringContainsString('redirect_uri=', $location);
    }

    public function test_admin_can_update_google_oauth_settings(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/api-integrations/google', [
            'is_active' => '1',
            'client_id' => 'updated-client-id-889.apps.googleusercontent.com',
            'client_secret' => 'GOCSPX-updatedSecret990',
            'button_label' => 'Login Cepat Google ASN',
            'auto_register' => '1',
            'default_role' => 'student',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('google_settings', [
            'client_id' => 'updated-client-id-889.apps.googleusercontent.com',
            'button_label' => 'Login Cepat Google ASN',
        ]);
    }

    public function test_google_mock_login_registers_and_authenticates_user(): void
    {
        $response = $this->post('/auth/google/mock-login', [
            'name' => 'Dokter Pengujian Google',
            'email' => 'dr.uji.google@instansi.go.id',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        
        $this->assertDatabaseHas('users', [
            'email' => 'dr.uji.google@instansi.go.id',
            'sso_provider' => 'google',
        ]);
    }
}
