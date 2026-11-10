<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SsoSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_active',
        'provider_name',
        'client_id',
        'client_secret',
        'authorization_url',
        'token_url',
        'userinfo_url',
        'scopes',
        'button_label',
        'auto_register',
        'default_role',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'auto_register' => 'boolean',
    ];

    /**
     * Singleton instance helper for SSO configuration.
     */
    public static function getSettings(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'is_active' => false,
                'provider_name' => 'SSO ASN Instansi Daerah',
                'client_id' => '',
                'client_secret' => '',
                'authorization_url' => '',
                'token_url' => '',
                'userinfo_url' => '',
                'scopes' => 'openid profile email nip nik',
                'button_label' => 'Masuk dengan Akun SSO',
                'auto_register' => true,
                'default_role' => 'student',
            ]
        );
    }
}
