<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AsnIntegrationApiController extends Controller
{
    /**
     * Get list of ASN participants with completed courses, certificates, and total JP.
     * Accessible by external apps: SIMPEG, SIASN BKN, e-Kinerja.
     */
    public function participants(Request $request): JsonResponse
    {
        $query = User::where('role', 'student')
            ->with([
                'certificates.course.category',
                'enrollments' => function ($q) {
                    $q->where(function ($sq) {
                        $sq->whereNotNull('completed_at')
                           ->orWhere('progress_percent', '>=', 100);
                    })->with('course.category');
                }
            ]);

        // Filter by NIP (Nomor Induk Pegawai)
        if ($request->filled('nip')) {
            $query->where('nip', $request->query('nip'));
        }

        // Filter by NIK
        if ($request->filled('nik')) {
            $query->where('nik', $request->query('nik'));
        }

        // Filter by Email
        if ($request->filled('email')) {
            $query->where('email', $request->query('email'));
        }

        // General search query
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('headline', 'like', "%{$search}%");
            });
        }

        $perPage = min((int) ($request->query('per_page', 15)), 100);
        $paginated = $query->paginate($perPage);

        $transformedData = $paginated->getCollection()->map(function (User $user) {
            return $this->formatUserData($user);
        });

        return response()->json([
            'status' => 'success',
            'meta' => [
                'api_version' => 'v1.0-asn',
                'timestamp' => now()->toIso8601String(),
                'total_results' => $paginated->total(),
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
            ],
            'data' => $transformedData,
        ]);
    }

    /**
     * Get single participant transcript by NIP or ID.
     */
    public function participantDetail(string $nipOrId): JsonResponse
    {
        $nipHash = app(\App\Services\EncryptionService::class)->hashNip(preg_replace('/[^0-9]/', '', $nipOrId));
        $user = User::where('role', 'student')
            ->where(function ($q) use ($nipOrId, $nipHash) {
                $q->where('nip', $nipOrId)
                  ->orWhere('nip_hash', $nipHash)
                  ->orWhere('id', $nipOrId);
            })
            ->with([
                'certificates.course.category',
                'enrollments' => function ($q) {
                    $q->with('course.category');
                }
            ])
            ->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => "Data peserta ASN dengan identitas [{$nipOrId}] tidak ditemukan.",
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'meta' => [
                'api_version' => 'v1.0-asn',
                'timestamp' => now()->toIso8601String(),
            ],
            'data' => $this->formatUserData($user, true),
        ]);
    }

    /**
     * Verify official certificate for ASN promotion / PAK auditing.
     */
    public function verifyCertificate(Request $request, ?string $hashOrNumber = null): JsonResponse
    {
        $lookup = $hashOrNumber ?? $request->query('number') ?? $request->query('hash') ?? $request->query('q');

        if (!$lookup) {
            return response()->json([
                'status' => 'error',
                'is_valid' => false,
                'message' => 'Harap berikan nomor sertifikat atau kode hash verifikasi.',
            ], 400);
        }

        $cert = Certificate::with(['user', 'course.category'])
            ->where('verification_hash', $lookup)
            ->orWhere('certificate_number', $lookup)
            ->first();

        if (!$cert) {
            return response()->json([
                'status' => 'error',
                'is_valid' => false,
                'message' => 'Sertifikat tidak ditemukan atau kode verifikasi tidak sah.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'is_valid' => true,
            'certificate' => [
                'certificate_number' => $cert->certificate_number,
                'verification_hash' => $cert->verification_hash,
                'issued_at' => $cert->issued_at->toIso8601String(),
                'verify_url' => route('certificates.verify', $cert->verification_hash),
                'course' => [
                    'title' => $cert->course->title,
                    'category' => $cert->course->category->name ?? 'Umum',
                    'jp' => $cert->course->jp ?: 20,
                    'duration_hours' => $cert->course->duration_hours,
                ],
                'participant' => [
                    'name' => $cert->user->name,
                    'nip' => $cert->user->nip,
                    'nik' => $cert->user->nik,
                    'email' => $cert->user->email,
                ],
            ],
        ]);
    }

    /**
     * Aggregate stats for government/agency dashboard.
     */
    public function stats(): JsonResponse
    {
        $totalStudents = User::where('role', 'student')->count();
        $totalCertificates = Certificate::count();
        $totalCourses = Course::count();

        // Calculate total JP delivered across all issued certificates
        $totalJpDelivered = Certificate::join('courses', 'certificates.course_id', '=', 'courses.id')
            ->sum('courses.jp');

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_asn_participants' => $totalStudents,
                'total_courses_accredited' => $totalCourses,
                'total_certificates_issued' => $totalCertificates,
                'total_jp_accumulated' => (int) $totalJpDelivered,
                'server_time' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Format user record for standardized ASN JSON payload.
     */
    private function formatUserData(User $user, bool $detailed = false): array
    {
        $completedCourses = [];
        $totalJp = 0;

        foreach ($user->enrollments as $enrollment) {
            $isComplete = $enrollment->completed_at !== null || $enrollment->progress_percent >= 100;
            if ($isComplete && $enrollment->course) {
                $courseJp = $enrollment->course->jp ?: ($enrollment->course->duration_hours * 2);
                $totalJp += $courseJp;

                $cert = $user->certificates->firstWhere('course_id', $enrollment->course_id);

                $completedCourses[] = [
                    'course_id' => $enrollment->course->id,
                    'course_title' => $enrollment->course->title,
                    'course_slug' => $enrollment->course->slug,
                    'category' => $enrollment->course->category->name ?? 'Umum',
                    'jp' => $courseJp,
                    'duration_hours' => $enrollment->course->duration_hours,
                    'progress_percent' => $enrollment->progress_percent,
                    'completed_at' => $enrollment->completed_at ? $enrollment->completed_at->toIso8601String() : null,
                    'certificate' => $cert ? [
                        'certificate_number' => $cert->certificate_number,
                        'verification_hash' => $cert->verification_hash,
                        'issued_at' => $cert->issued_at->toIso8601String(),
                        'verify_url' => route('certificates.verify', $cert->verification_hash),
                    ] : null,
                ];
            }
        }

        $payload = [
            'id' => $user->id,
            'name' => $user->name,
            'nip' => $user->nip ?: null,
            'nik' => $user->nik ?: null,
            'email' => $user->email,
            'phone' => $user->phone ?: null,
            'headline' => $user->headline ?: 'Aparatur Sipil Negara / Tenaga Kesehatan',
            'metrics' => [
                'total_jp' => $totalJp,
                'total_completed_courses' => count($completedCourses),
                'total_certificates' => $user->certificates->count(),
            ],
            'completed_courses' => $completedCourses,
        ];

        return $payload;
    }
}
