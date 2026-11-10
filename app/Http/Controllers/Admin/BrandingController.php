<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrandingController extends Controller
{
    /**
     * Display Branding, Logo & Favicon management page.
     */
    public function index(): View
    {
        $setting = AppSetting::getSettings();
        return view('admin.settings.branding', compact('setting'));
    }

    /**
     * Update application branding, upload logo and favicon.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:60',
            'tagline' => 'nullable|string|max:150',
            'badge_text' => 'nullable|string|max:20',
            'primary_color' => 'nullable|string|max:30',
            'jp_default_target' => 'nullable|integer|min:1|max:100',
            'jp_year_start_month' => 'nullable|integer|in:1,4',
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

        return redirect()->back()
            ->with('success', 'Identitas brand, logo, dan favicon aplikasi LMS KAB Berau berhasil diperbarui!');
    }

    /**
     * Reset logo to default LMS KAB Berau vector graphic.
     */
    public function resetLogo(): RedirectResponse
    {
        $setting = AppSetting::getSettings();
        $setting->update(['logo_path' => null]);

        return redirect()->back()
            ->with('success', 'Logo berhasil dikembalikan ke logo bawaan LMS KAB Berau!');
    }

    /**
     * Reset favicon to default.
     */
    public function resetFavicon(): RedirectResponse
    {
        $setting = AppSetting::getSettings();
        $setting->update(['favicon_path' => null]);

        return redirect()->back()
            ->with('success', 'Favicon berhasil dikembalikan ke favicon bawaan LMS KAB Berau!');
    }
}
