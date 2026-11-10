<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_active',
        'mail_mailer',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
        'last_tested_at',
        'last_test_status',
        'last_test_message',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'mail_port' => 'integer',
        'last_tested_at' => 'datetime',
    ];

    /**
     * Singleton instance helper for settings.
     */
    public static function getSettings(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'is_active' => false,
                'mail_mailer' => 'smtp',
                'mail_host' => 'smtp.gmail.com',
                'mail_port' => 587,
                'mail_username' => 'admin@beraukab.go.id',
                'mail_password' => null,
                'mail_encryption' => 'tls',
                'mail_from_address' => 'bkpsdm@beraukab.go.id',
                'mail_from_name' => 'LMS KAB Berau - BKPSDM',
            ]
        );
    }

    /**
     * Apply this SMTP configuration to Laravel runtime mailer.
     */
    public function applyToConfig(): void
    {
        config([
            'mail.default' => $this->mail_mailer ?: 'smtp',
            'mail.mailers.smtp.host' => $this->mail_host,
            'mail.mailers.smtp.port' => (int) ($this->mail_port ?: 587),
            'mail.mailers.smtp.encryption' => ($this->mail_encryption && $this->mail_encryption !== 'none') ? $this->mail_encryption : null,
            'mail.mailers.smtp.username' => $this->mail_username,
            'mail.mailers.smtp.password' => $this->mail_password,
            'mail.from.address' => $this->mail_from_address ?: 'bkpsdm@beraukab.go.id',
            'mail.from.name' => $this->mail_from_name ?: 'LMS KAB Berau',
        ]);
    }
}

