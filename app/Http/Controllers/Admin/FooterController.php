<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FooterController extends Controller
{
    /**
     * Display Footer configuration page.
     */
    public function index(): View
    {
        $setting = AppSetting::getSettings();
        return view('admin.settings.footer', compact('setting'));
    }

    /**
     * Update footer information, contact support, and social media links.
     */
    public function update(Request $request): RedirectResponse
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

        return redirect()->back()
            ->with('success', 'Konfigurasi footer dan informasi kontak berhasil diperbarui!');
    }
}
