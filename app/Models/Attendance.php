<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_session_id',
        'user_id',
        'participant_name',
        'participant_nip',
        'participant_opd',
        'participant_jabatan',
        'attendable_type',
        'attendable_id',
        'activity_title',
        'session_code',
        'date',
        'time',
        'check_in_at',
        'check_out_at',
        'status',
        'method',
        'duration_minutes',
        'min_duration_minutes',
        'is_duration_qualified',
        'qr_token',
        'ip_address',
        'user_agent',
        'notes',
        'verified_by',
    ];

    protected $casts = [
        'date'                  => 'date',
        'check_in_at'           => 'datetime',
        'check_out_at'          => 'datetime',
        'duration_minutes'      => 'integer',
        'min_duration_minutes'  => 'integer',
        'is_duration_qualified' => 'boolean',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function attendable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Label Status Kehadiran
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'present' => 'Hadir Tepat Waktu',
            'late'    => 'Terlambat',
            'excused' => 'Izin',
            'sick'    => 'Sakit',
            'absent'  => 'Alpa / Tidak Hadir',
            default   => ucfirst($this->status),
        };
    }

    /**
     * HTML Badge Status Kehadiran
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'present' => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Hadir Tepat Waktu</span>',
            'late'    => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Terlambat</span>',
            'excused' => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200"><span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Izin Dinas</span>',
            'sick'    => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200"><span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span> Sakit</span>',
            'absent'  => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Alpa</span>',
            default   => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">' . e($this->status_label) . '</span>',
        };
    }

    /**
     * Label Metode Presensi
     */
    public function getMethodLabelAttribute(): string
    {
        return match($this->method) {
            'qr_code' => 'QR Code Scanner',
            'button'  => 'Tombol Presensi Digital',
            'manual'  => 'Input Manual Panitia',
            default   => ucfirst($this->method),
        };
    }

    /**
     * HTML Badge Metode Presensi
     */
    public function getMethodBadgeAttribute(): string
    {
        return match($this->method) {
            'qr_code' => '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">📱 QR CODE</span>',
            'button'  => '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-teal-50 text-teal-700 border border-teal-200">🖱️ TOMBOL</span>',
            'manual'  => '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-600 border border-slate-200">✍️ MANUAL</span>',
            default   => '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-600">' . e(strtoupper($this->method)) . '</span>',
        };
    }

    /**
     * Durasi Kehadiran dalam Format Jam & Menit
     */
    public function getFormattedDurationAttribute(): string
    {
        $minutes = $this->duration_minutes;
        if ($minutes <= 0) {
            return '-';
        }

        $h = floor($minutes / 60);
        $m = $minutes % 60;

        if ($h > 0 && $m > 0) {
            return "{$h} Jam {$m} Menit";
        } elseif ($h > 0) {
            return "{$h} Jam";
        }
        return "{$m} Menit";
    }
}
