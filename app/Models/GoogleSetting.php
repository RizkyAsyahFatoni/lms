<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_active',
        'client_id',
        'client_secret',
        'button_label',
        'auto_register',
        'default_role',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'auto_register' => 'boolean',
    ];

    /**
     * Singleton instance helper for Google OAuth configuration.
     */
    public static function getSettings(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'is_active' => true,
                'client_id' => '1029384756-demo-apps.apps.googleusercontent.com',
                'client_secret' => 'GOCSPX-demoGoogleClientSecret2026',
                'button_label' => 'Masuk dengan Akun Google',
                'auto_register' => true,
                'default_role' => 'student',
            ]
        );
    }
}
