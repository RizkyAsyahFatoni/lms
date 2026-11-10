<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Display unified Settings page with top tabs: Identity, Footer, JP ASN.
     */
    public function index(Request $request): View
    {
        $setting = AppSetting::getSettings();
        $totalAsn = User::where('role', 'student')->whereNotNull('nip')->count();
        $totalUsers = User::count();

        return view('admin.settings.index', compact('setting', 'totalAsn', 'totalUsers'));
    }

    /**
     * Update LMS Identity, Logo and Favicon.
     */
    public function updateIdentity(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:60',
            'tagline' => 'nullable|string|max:150',
            'badge_text' => 'nullable|string|max:20',
            'primary_color' => 'nullable|string|max:30',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'favicon' => 'nullable|file|mimes:ico,png,svg,webp|max:1024',
        ]);

        $setting = AppSetting::getSettings();

        // Handle Custom Logo Upload
        if ($request->hasFile('logo')) {
            $logoFile = $request->file('logo');
            $logoName = 'logo_' . time() . '.' . $logoFile->getClientOriginalExtension();
            $logoFile->move(public_path('uploads/branding'), $logoName);
            $validated['logo_path'] = 'uploads/branding/' . $logoName;
        }

        // Handle Custom Favicon Upload
        if ($request->hasFile('favicon')) {
            $faviconFile = $request->file('favicon');
            $faviconName = 'favicon_' . time() . '.' . $faviconFile->getClientOriginalExtension();
            $faviconFile->move(public_path('uploads/branding'), $faviconName);
            $validated['favicon_path'] = 'uploads/branding/' . $faviconName;
        }

        $setting->update($validated);

        return redirect()->route('admin.settings.index', ['tab' => 'identity'])
            ->with('success', 'Identitas brand, nama aplikasi, logo, dan favicon berhasil disimpan!');
    }

    /**
     * Update Footer information, address, and social media.
     */
    public function updateFooter(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'footer_description' => 'nullable|string|max:1000',
            'footer_address' => 'nullable|string|max:500',
            'contact_email' => 'nullable|email|max:100',
            'contact_phone' => 'nullable|string|max:50',
            'footer_copyright' => 'nullable|string|max:255',
            'social_facebook' => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_youtube' => 'nullable|url|max:255',
            'social_tiktok' => 'nullable|url|max:255',
            'social_twitter' => 'nullable|url|max:255',
            'social_linkedin' => 'nullable|url|max:255',
        ]);

        $setting = AppSetting::getSettings();
        $setting->update($validated);

        return redirect()->route('admin.settings.index', ['tab' => 'footer'])
            ->with('success', 'Pengaturan footer, kontak, dan tautan sosial media berhasil diperbarui!');
    }

    /**
     * Update ASN JP Target and Fiscal Period calculation cycle.
     */
    public function updateJp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jp_default_target' => 'required|integer|min:1|max:100',
            'jp_year_start_month' => 'required|integer|in:1,4',
        ]);

        $setting = AppSetting::getSettings();
        $setting->update($validated);

        return redirect()->route('admin.settings.index', ['tab' => 'jp'])
            ->with('success', 'Pengaturan target JP tahunan dan siklus periode perhitungan ASN berhasil diperbarui!');
    }

    /**
     * Update Homepage Grid Layout (2x4 / 2x3).
     */
    public function updateLayout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'featured_grid_layout' => 'required|in:2x4,2x3',
        ]);

        $setting = AppSetting::getSettings();
        $setting->update($validated);

        return redirect()->route('admin.settings.index', ['tab' => 'layout'])
            ->with('success', 'Format tata letak Grid Program Unggulan Beranda berhasil diperbarui menjadi ' . ($request->featured_grid_layout === '2x4' ? '2x4 (4 Kolom per Baris)' : '2x3 (3 Kolom per Baris)') . '!');
    }

    /**
     * Reset logo to default LMS KAB Berau vector graphic.
     */
    public function resetLogo(): RedirectResponse
    {
        $setting = AppSetting::getSettings();
        $setting->update(['logo_path' => null]);

        return redirect()->route('admin.settings.index', ['tab' => 'identity'])
            ->with('success', 'Logo berhasil dikembalikan ke logo bawaan LMS KAB Berau!');
    }

    /**
     * Reset favicon to default.
     */
    public function resetFavicon(): RedirectResponse
    {
        $setting = AppSetting::getSettings();
        $setting->update(['favicon_path' => null]);

        return redirect()->route('admin.settings.index', ['tab' => 'identity'])
            ->with('success', 'Favicon berhasil dikembalikan ke favicon bawaan LMS KAB Berau!');
    }
}
