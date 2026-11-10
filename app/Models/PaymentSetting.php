<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'is_active',
        'environment',
        'merchant_id',
        'client_key',
        'server_key',
        'webhook_secret',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get or create the singleton instance of PaymentSetting.
     */
    public static function getSettings(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'provider' => 'midtrans',
                'is_active' => false,
                'environment' => 'sandbox',
            ]
        );
    }
}
