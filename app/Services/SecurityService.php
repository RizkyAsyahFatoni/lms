<?php

namespace App\Services;

use App\Models\User;
use App\Models\ExternalTraining;
use App\Models\ActivityLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SecurityService
{
    /**
     * Magic byte signatures for authorized document and image types.
     */
    protected static array $magicBytes = [
        'pdf'  => ['%PDF-'],
        'jpg'  => ["\xFF\xD8\xFF"],
        'jpeg' => ["\xFF\xD8\xFF"],
        'png'  => ["\x89PNG\r\n\x1a\n"],
        'webp' => ['RIFF', 'WEBP'],
        'doc'  => ["\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1"],
        'xls'  => ["\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1"],
        'docx' => ["PK\x03\x04"],
        'xlsx' => ["PK\x03\x04"],
    ];

    /**
     * Mime types mapping for allowed extensions.
     */
    protected static array $allowedMimes = [
        'pdf'  => ['application/pdf'],
        'jpg'  => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png'  => ['image/png'],
        'webp' => ['image/webp'],
        'doc'  => ['application/msword', 'application/vnd.ms-office'],
        'xls'  => ['application/vnd.ms-excel', 'application/vnd.ms-office', 'application/msexcel'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip'],
    ];

    /**
     * Deep validate an uploaded file:
     * 1. File extension validation
     * 2. Real MIME inspection (finfo)
     * 3. Magic Bytes binary header inspection
     * 4. Dangerous script/payload detection (webshell prevention)
     *
     * @throws ValidationException
     */
    public static function validateAndSanitizeUpload(
        UploadedFile $file,
        string $fieldLabel = 'Berkas',
        array $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'webp'],
        int $maxSizeBytes = 5242880 // 5 MB
    ): string {
        $clientExt = strtolower($file->getClientOriginalExtension());
        $realMime = $file->getMimeType();
        $realPath = $file->getRealPath();
        $fileSize = $file->getSize();

        // 1. Validasi Ukuran File
        if ($fileSize > $maxSizeBytes) {
            $maxMb = round($maxSizeBytes / 1048576, 1);
            throw ValidationException::withMessages([
                $fieldLabel => "Ukuran berkas melebihi batas maksimal ({$maxMb} MB)."
            ]);
        }

        // 2. Validasi Ekstensi yang Diizinkan
        if (!in_array($clientExt, $allowedExtensions, true)) {
            $allowedStr = strtoupper(implode(', ', $allowedExtensions));
            throw ValidationException::withMessages([
                $fieldLabel => "Format berkas tidak diizinkan. Format yang diperbolehkan: {$allowedStr}."
            ]);
        }

        // 3. Validasi Real MIME Type
        $validMimesForExt = self::$allowedMimes[$clientExt] ?? [];
        if (!in_array($realMime, $validMimesForExt, true)) {
            ActivityLog::record(
                'security_warning',
                "Upaya upload file dengan MIME tidak valid: Ekstensi {$clientExt}, MIME terdeteksi {$realMime}",
                null,
                ['file_name' => $file->getClientOriginalName(), 'ip' => request()->ip()]
            );

            throw ValidationException::withMessages([
                $fieldLabel => "Konten berkas tidak valid atau tidak sesuai dengan format {$clientExt}."
            ]);
        }

        // 4. Validasi Magic Bytes (Binary Header Check)
        $handle = @fopen($realPath, 'rb');
        if ($handle) {
            $header = fread($handle, 32);
            fclose($handle);

            $isValidMagic = false;

            if ($clientExt === 'pdf') {
                $isValidMagic = str_starts_with($header, '%PDF-');
            } elseif (in_array($clientExt, ['jpg', 'jpeg'])) {
                $isValidMagic = str_starts_with($header, "\xFF\xD8\xFF");
            } elseif ($clientExt === 'png') {
                $isValidMagic = str_starts_with($header, "\x89PNG\r\n\x1a\n");
            } elseif ($clientExt === 'webp') {
                $isValidMagic = str_starts_with($header, 'RIFF') && str_contains(substr($header, 8, 8), 'WEBP');
            } elseif (in_array($clientExt, ['doc', 'xls'])) {
                $isValidMagic = str_starts_with($header, "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1");
            } elseif (in_array($clientExt, ['docx', 'xlsx'])) {
                $isValidMagic = str_starts_with($header, "PK\x03\x04");
            }

            if (!$isValidMagic) {
                ActivityLog::record(
                    'security_alert',
                    "Upaya upload berkas dengan Magic Bytes palsu/korup pada field {$fieldLabel}",
                    null,
                    ['original_name' => $file->getClientOriginalName(), 'ext' => $clientExt]
                );

                throw ValidationException::withMessages([
                    $fieldLabel => "Berkas terdeteksi tidak valid atau telah dimodifikasi (Header Signature Corrupted)."
                ]);
            }
        }

        // 5. Pemeriksaan Skrip Berbahaya / Polyglot Webshell pada berkas gambar atau PDF
        if (file_exists($realPath) && is_readable($realPath)) {
            $contentHead = @file_get_contents($realPath, false, null, 0, 4096);
            if ($contentHead !== false) {
                $suspiciousSignatures = ['<?php', '<?= ', '<script', 'eval(', 'base64_decode(', 'system(', 'passthru(', 'shell_exec('];
                foreach ($suspiciousSignatures as $sig) {
                    if (stripos($contentHead, $sig) !== false) {
                        ActivityLog::record(
                            'security_critical',
                            "Potensi serangan WebShell/Malware diblokir saat upload berkas {$file->getClientOriginalName()}!",
                            null,
                            ['signature_found' => $sig, 'ip' => request()->ip()]
                        );

                        throw ValidationException::withMessages([
                            $fieldLabel => "Berkas ditolak demi keamanan sistem (mengandung skrip terlarang)."
                        ]);
                    }
                }
            }
        }

        // 6. Generate Sanitized Unique Random File Name (UUID v4)
        return Str::uuid()->toString() . '.' . $clientExt;
    }

    /**
     * Check Anti-IDOR Authorization for External Training Documents:
     * User can view document if:
     * - User is the owner of the submission
     * - User is Super Admin / BKPSDM
     * - User is Admin OPD of the same OPD
     * - User is Reviewer Talenta
     */
    public static function canAccessTrainingDocument(?User $user, ExternalTraining $training): bool
    {
        if (!$user) {
            return false;
        }

        // 1. Pemilik Dokumen
        if ($training->user_id === $user->id) {
            return true;
        }

        // 2. Super Admin / BKPSDM
        if ($user->isSuperAdmin() || in_array($user->role, ['admin', 'bkpsdm', 'super_admin'])) {
            return true;
        }

        // 3. Reviewer Talenta
        if ($user->isReviewerTalent()) {
            return true;
        }

        // 4. Admin OPD (harus OPD yang sama dengan pemilik dokumen)
        if ($user->isAdminOpd()) {
            $owner = $training->user;
            if ($owner && $user->opd_id && $owner->opd_id === $user->opd_id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Mask Sensitive Data for UU PDP Compliance (e.g. NIP: 198805142014021003 -> 19880514******1003)
     */
    public static function maskNip(?string $nip): string
    {
        if (!$nip || strlen($nip) < 10) {
            return $nip ?? '-';
        }

        $start = substr($nip, 0, 8);
        $end = substr($nip, -4);
        $maskLength = max(4, strlen($nip) - 12);

        return $start . str_repeat('*', $maskLength) . $end;
    }

    /**
     * Mask Email (e.g. budi.santoso@beraukab.go.id -> b***o@beraukab.go.id)
     */
    public static function maskEmail(?string $email): string
    {
        if (!$email || !str_contains($email, '@')) {
            return $email ?? '-';
        }

        [$username, $domain] = explode('@', $email, 2);
        if (strlen($username) <= 2) {
            $maskedUser = $username[0] . '*';
        } else {
            $maskedUser = $username[0] . str_repeat('*', strlen($username) - 2) . substr($username, -1);
        }

        return $maskedUser . '@' . $domain;
    }
}
