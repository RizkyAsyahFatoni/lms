<?php

namespace App\Providers;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share application branding settings globally to all views
        View::composer('*', function ($view) {
            try {
                if (Schema::hasTable('app_settings')) {
                    $view->with('appSetting', AppSetting::getSettings());
                }
            } catch (\Exception $e) {
                // Graceful fallback during initial migrations
            }
        });

        // Bagikan data notifikasi antrean verifikasi ke seluruh layout panel admin
        View::composer('layouts.admin', function ($view) {
            try {
                $pendingAsnCount = Schema::hasTable('profile_change_requests')
                    ? \App\Models\ProfileChangeRequest::where('status', 'pending')->count()
                    : 0;

                $pendingExternalCount = Schema::hasTable('external_trainings')
                    ? \App\Models\ExternalTraining::where('status', 'pending')->count()
                    : 0;

                $pendingExternalJp = Schema::hasTable('external_trainings')
                    ? (int) \App\Models\ExternalTraining::where('status', 'pending')->sum('jp_count')
                    : 0;

                $totalPendingVerifications = $pendingAsnCount + $pendingExternalCount;

                $view->with([
                    'pendingAsnCount'           => $pendingAsnCount,
                    'pendingExternalCount'      => $pendingExternalCount,
                    'pendingExternalJp'         => $pendingExternalJp,
                    'totalPendingVerifications' => $totalPendingVerifications,
                ]);
            } catch (\Throwable $e) {
                $view->with([
                    'pendingAsnCount'           => 0,
                    'pendingExternalCount'      => 0,
                    'pendingExternalJp'         => 0,
                    'totalPendingVerifications' => 0,
                ]);
            }
        });
    }
}
