<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BrandingSettingsTest extends TestCase
{
    public function test_admin_can_view_branding_settings_page(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/settings?tab=identity');
        $response->assertStatus(200);
        $response->assertSee('Identitas & Logo LMS', false);
        $response->assertSee('LMS KAB Berau', false);
    }

    public function test_admin_can_update_application_branding(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/branding', [
            'app_name' => 'LMS KAB Berau Enterprise',
            'tagline' => 'Platform Pembelajaran ASN & Tenaga Kesehatan Terpadu',
            'badge_text' => 'OFFICIAL',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('app_settings', [
            'app_name' => 'LMS KAB Berau Enterprise',
            'badge_text' => 'OFFICIAL',
        ]);
    }

    public function test_app_name_appears_in_public_and_admin_layouts(): void
    {
        $setting = AppSetting::getSettings();
        $setting->update([
            'app_name' => 'LMS KAB Berau',
            'tagline' => 'Ekosistem Pembelajaran Digital Terintegrasi',
        ]);

        $publicResponse = $this->get('/');
        $publicResponse->assertStatus(200);
        $publicResponse->assertSee('LMS KAB Berau', false);

        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $adminResponse = $this->actingAs($admin)->get('/admin');
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('LMS KAB Berau', false);
    }

    public function test_admin_can_reset_logo_and_favicon(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $setting = AppSetting::getSettings();
        $setting->update([
            'logo_path' => 'uploads/branding/custom_test_logo.png',
            'favicon_path' => 'uploads/branding/custom_test_favicon.ico',
        ]);

        $resetLogoResp = $this->actingAs($admin)->post('/admin/branding/reset-logo');
        $resetLogoResp->assertRedirect();
        $this->assertDatabaseHas('app_settings', ['logo_path' => null]);

        $resetFaviconResp = $this->actingAs($admin)->post('/admin/branding/reset-favicon');
        $resetFaviconResp->assertRedirect();
        $this->assertDatabaseHas('app_settings', ['favicon_path' => null]);
    }
}
