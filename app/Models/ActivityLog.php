<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Audit Trail Logs are immutable: Cannot be edited or deleted (BSSN & Forensics Standard).
     */
    protected static function booted(): void
    {
        static::updating(function ($model) {
            throw new \RuntimeException('Audit trail logs are immutable and cannot be updated.');
        });

        static::deleting(function ($model) {
            throw new \RuntimeException('Audit trail logs are immutable and cannot be deleted.');
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record a system/user activity log.
     */
    public static function record(string $action, string $description, ?int $userId = null, ?array $properties = null): self
    {
        $userId = $userId ?? Auth::id();
        $ip = request()?->ip() ?? '127.0.0.1';
        $agent = request()?->userAgent();

        return self::create([
            'user_id'     => $userId,
            'action'      => $action,
            'description' => $description,
            'ip_address'  => $ip,
            'user_agent'  => $agent,
            'properties'  => $properties,
        ]);
    }

    /**
     * Record Bulk Data Export (UU PDP No. 27 Tahun 2022 Compliance)
     */
    public static function recordExport(string $module, int $recordCount, string $format = 'CSV', ?array $filters = null): self
    {
        $desc = "Mengunduh/Ekspor Data Massal: Modul {$module} ({$recordCount} baris data, Format {$format})";

        return self::record(
            action: 'bulk_export',
            description: $desc,
            userId: Auth::id(),
            properties: [
                'module'       => $module,
                'record_count' => $recordCount,
                'format'       => $format,
                'filters'      => $filters,
                'compliance'   => 'UU PDP No. 27/2022 Pasal 35',
            ]
        );
    }

    /**
     * Get badge HTML based on action type.
     */
    public function getActionBadgeAttribute(): string
    {
        $badge = match($this->action) {
            'login'                        => ['label' => 'Login', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
            'logout'                       => ['label' => 'Logout', 'class' => 'bg-slate-100 text-slate-600 border-slate-200'],
            'failed_login'                 => ['label' => 'Login Gagal', 'class' => 'bg-orange-50 text-orange-800 border-orange-300 font-bold'],
            'create_user'                  => ['label' => 'Tambah User', 'class' => 'bg-teal-50 text-teal-700 border-teal-200'],
            'update_user'                  => ['label' => 'Update Profil', 'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
            'change_role'                  => ['label' => 'Ubah Role', 'class' => 'bg-purple-50 text-purple-700 border-purple-200'],
            'reset_password'               => ['label' => 'Reset Password', 'class' => 'bg-amber-50 text-amber-700 border-amber-200'],
            'toggle_status'                => ['label' => 'Status Akun', 'class' => 'bg-rose-50 text-rose-700 border-rose-200'],
            'permissions'                  => ['label' => 'Hak Akses', 'class' => 'bg-indigo-50 text-indigo-700 border-indigo-200'],
            'bulk_export'                  => ['label' => 'Ekspor Massal (PDP)', 'class' => 'bg-rose-100 text-rose-800 border-rose-300 font-bold'],
            'view_document'                => ['label' => 'Akses Dokumen', 'class' => 'bg-cyan-50 text-cyan-800 border-cyan-200'],
            'submit_external_jp'           => ['label' => 'Pengajuan JP', 'class' => 'bg-emerald-50 text-emerald-800 border-emerald-200'],
            'unauthorized_access_attempt'  => ['label' => 'Akses Terlarang (IDOR)', 'class' => 'bg-red-100 text-red-900 border-red-300 font-extrabold'],
            'security_alert'               => ['label' => 'Peringatan Keamanan', 'class' => 'bg-amber-100 text-amber-900 border-amber-300 font-bold'],
            'security_critical'            => ['label' => 'Insiden Keamanan', 'class' => 'bg-red-200 text-red-950 border-red-400 font-black'],
            default                        => ['label' => ucfirst(str_replace('_', ' ', $this->action)), 'class' => 'bg-slate-100 text-slate-700 border-slate-200'],
        };

        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border ' . $badge['class'] . '">' . htmlspecialchars($badge['label']) . '</span>';
    }
}
