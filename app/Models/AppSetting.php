<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_name',
        'tagline',
        'badge_text',
        'logo_path',
        'favicon_path',
        'primary_color',
        'featured_grid_layout',
        'jp_year_start_month',
        'jp_default_target',
        'footer_description',
        'footer_address',
        'contact_email',
        'contact_phone',
        'footer_copyright',
        'social_facebook',
        'social_instagram',
        'social_youtube',
        'social_tiktok',
        'social_twitter',
        'social_linkedin',
    ];

    /**
     * Get the current JP fiscal year label based on config.
     * If start_month=1 (Jan-Dec): "2026"
     * If start_month=4 (Apr-Mar): "2026/2027"
     */
    public function getJpYearLabel(?int $refYear = null): string
    {
        $startMonth = $this->jp_year_start_month ?: 1;
        $now = now();
        $year = $refYear ?? $now->year;

        if ($startMonth === 1) {
            return (string) $year;
        }

        // For Apr-Mar fiscal year
        if (!$refYear && $now->month < $startMonth) {
            $year = $now->year - 1;
        }

        return $year . '/' . ($year + 1);
    }

    /**
     * Get the current JP fiscal year boundaries.
     * Returns [start_date, end_date].
     */
    public function getJpYearBounds(?int $refYear = null): array
    {
        $startMonth = $this->jp_year_start_month ?: 1;
        $now = now();

        if ($startMonth === 1) {
            $year = $refYear ?? $now->year;
            return [
                "{$year}-01-01",
                "{$year}-12-31",
            ];
        }

        // Fiscal year (e.g., Apr 2026 - Mar 2027)
        $year = $refYear ?? ($now->month >= $startMonth ? $now->year : $now->year - 1);
        $endYear = $year + 1;

        return [
            "{$year}-" . str_pad($startMonth, 2, '0', STR_PAD_LEFT) . "-01",
            "{$endYear}-" . str_pad($startMonth - 1, 2, '0', STR_PAD_LEFT) . "-" . cal_days_in_month(CAL_GREGORIAN, $startMonth - 1, $endYear),
        ];
    }

    /**
     * Singleton instance helper for application branding and settings.
     */
    public static function getSettings(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'app_name' => 'LMS KAB Berau',
                'tagline' => 'Ekosistem Pembelajaran Mandiri Aparatur Sipil Negara Kabupaten Berau',
                'badge_text' => 'OFFICIAL',
                'logo_path' => null,
                'favicon_path' => null,
                'primary_color' => '#0f766e',
                'footer_description' => 'Platform pembelajaran mandiri dan pemenuhan jam pelajaran (JP) terintegrasi bagi aparatur pemerintah di lingkungan Pemerintah Kabupaten Berau.',
                'footer_address' => 'Jl. APT. Pranoto No. 1, Tanjung Redeb, Kabupaten Berau, Kalimantan Timur',
                'contact_email' => 'bkpsdm@beraukab.go.id',
                'contact_phone' => '(0554) 21064',
                'footer_copyright' => '© ' . date('Y') . ' Pemerintah Kabupaten Berau - BKPSDM. Seluruh Hak Cipta Dilindungi.',
            ]
        );
    }

    /**
     * Get the full URL for the logo, or null if using default vector SVG.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo_path) {
            return asset($this->logo_path);
        }
        return null;
    }

    /**
     * Get the full URL for the favicon.
     */
    public function getFaviconUrlAttribute(): string
    {
        if ($this->favicon_path) {
            return asset($this->favicon_path);
        }
        return asset('favicon.svg');
    }
}
