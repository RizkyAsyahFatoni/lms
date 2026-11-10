<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\ExternalTraining;
use App\Models\User;
use App\Models\Webinar;
use App\Models\WebinarLearningRecord;
use App\Models\WebinarRegistration;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class JpService
{
    /** Standard learning types according to LAN RI and KAK */
    public const LEARNING_TYPES = [
        'elearning' => [
            'key'         => 'elearning',
            'label'       => 'E-Learning Mandiri',
            'short_label' => 'E-Learning',
            'badge_color' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            'icon'        => '💻',
            'description' => 'Kursus digital asynchronous melalui modul pembelajaran di platform.',
        ],
        'webinar' => [
            'key'         => 'webinar',
            'label'       => 'Webinar / Live Session',
            'short_label' => 'Webinar',
            'badge_color' => 'bg-teal-50 text-teal-700 border-teal-200',
            'icon'        => '📡',
            'description' => 'Seminar daring, lokakarya virtual interaktif, dan telekonferensi.',
        ],
        'in_class' => [
            'key'         => 'in_class',
            'label'       => 'Klasikal / Bimtek / Workshop',
            'short_label' => 'Klasikal/Bimtek',
            'badge_color' => 'bg-amber-50 text-amber-700 border-amber-200',
            'icon'        => '🏛️',
            'description' => 'Bimbingan teknis tatap muka langsung, workshop, dan diklat klasikal.',
        ],
        'hybrid' => [
            'key'         => 'hybrid',
            'label'       => 'Hybrid Learning',
            'short_label' => 'Hybrid',
            'badge_color' => 'bg-purple-50 text-purple-700 border-purple-200',
            'icon'        => '🔄',
            'description' => 'Kombinasi pembelajaran daring (online) dan tatap muka (luring).',
        ],
        'external' => [
            'key'         => 'external',
            'label'       => 'Diklat Eksternal Mandiri',
            'short_label' => 'Eksternal',
            'badge_color' => 'bg-blue-50 text-blue-700 border-blue-200',
            'icon'        => '📜',
            'description' => 'Pelatihan mandiri di luar instansi yang diverifikasi dan disahkan BKPSDM.',
        ],
    ];

    /** Default target JP per year */
    public static function getDefaultTargetJp(): int
    {
        $setting = AppSetting::first();
        return $setting->jp_default_target ?? 20;
    }

    /**
     * Resolve date bounds based on year, period type, or custom date strings
     */
    public static function resolveDateRange(?int $year, ?string $periodType = 'all', ?string $startDate = null, ?string $endDate = null): array
    {
        if ($startDate && $endDate) {
            return [
                'start' => Carbon::parse($startDate)->startOfDay(),
                'end'   => Carbon::parse($endDate)->endOfDay(),
                'label' => Carbon::parse($startDate)->translatedFormat('d M Y') . ' s.d. ' . Carbon::parse($endDate)->translatedFormat('d M Y'),
                'year'  => $year ?: Carbon::parse($startDate)->year,
                'type'  => 'custom',
            ];
        }

        $y = $year ?: (int) date('Y');

        return match ($periodType) {
            'q1' => [
                'start' => Carbon::create($y, 1, 1)->startOfDay(),
                'end'   => Carbon::create($y, 3, 31)->endOfDay(),
                'label' => "Triwulan I (Jan–Mar) {$y}",
                'year'  => $y,
                'type'  => 'q1',
            ],
            'q2' => [
                'start' => Carbon::create($y, 4, 1)->startOfDay(),
                'end'   => Carbon::create($y, 6, 30)->endOfDay(),
                'label' => "Triwulan II (Apr–Jun) {$y}",
                'year'  => $y,
                'type'  => 'q2',
            ],
            'q3' => [
                'start' => Carbon::create($y, 7, 1)->startOfDay(),
                'end'   => Carbon::create($y, 9, 30)->endOfDay(),
                'label' => "Triwulan III (Jul–Sep) {$y}",
                'year'  => $y,
                'type'  => 'q3',
            ],
            'q4' => [
                'start' => Carbon::create($y, 10, 1)->startOfDay(),
                'end'   => Carbon::create($y, 12, 31)->endOfDay(),
                'label' => "Triwulan IV (Okt–Des) {$y}",
                'year'  => $y,
                'type'  => 'q4',
            ],
            's1' => [
                'start' => Carbon::create($y, 1, 1)->startOfDay(),
                'end'   => Carbon::create($y, 6, 30)->endOfDay(),
                'label' => "Semester I (Jan–Jun) {$y}",
                'year'  => $y,
                'type'  => 's1',
            ],
            's2' => [
                'start' => Carbon::create($y, 7, 1)->startOfDay(),
                'end'   => Carbon::create($y, 12, 31)->endOfDay(),
                'label' => "Semester II (Jul–Des) {$y}",
                'year'  => $y,
                'type'  => 's2',
            ],
            default => [
                'start' => Carbon::create($y, 1, 1)->startOfDay(),
                'end'   => Carbon::create($y, 12, 31)->endOfDay(),
                'label' => "Tahun {$y}",
                'year'  => $y,
                'type'  => 'all',
            ],
        };
    }

    /**
     * Calculate JP breakdown for a single user
     */
    public function getUserJpSummary(User $user, ?int $year = null, ?string $periodType = 'all', ?string $startDate = null, ?string $endDate = null): array
    {
        $range = self::resolveDateRange($year, $periodType, $startDate, $endDate);
        $start = $range['start'];
        $end = $range['end'];

        // 1. Courses (E-Learning, In-Class, Hybrid)
        $courseEnrollments = Enrollment::with('course')
            ->where('user_id', $user->id)
            ->where(function ($q) {
                $q->whereNotNull('completed_at')
                  ->orWhere('progress_percent', '>=', 100);
            })
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('completed_at', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->whereNull('completed_at')
                         ->whereBetween('updated_at', [$start, $end]);
                  });
            })
            ->get();

        $jpElearning = 0;
        $jpInClass = 0;
        $jpHybrid = 0;

        foreach ($courseEnrollments as $enr) {
            $c = $enr->course;
            if (!$c) continue;
            $courseJp = (int) ($c->jp ?: 0);
            $type = strtolower($c->course_type ?? '');

            if (in_array($type, ['in-class', 'in_class', 'workshop', 'bimtek', 'seminar', 'klasikal'])) {
                $jpInClass += $courseJp;
            } elseif (in_array($type, ['hybrid', 'blended'])) {
                $jpHybrid += $courseJp;
            } else {
                // e-course, online, free, etc.
                $jpElearning += $courseJp;
            }
        }

        // 2. Webinars & Live Sessions
        // Calculate from WebinarLearningRecord or WebinarRegistration
        $webinarRecords = WebinarLearningRecord::with('webinar')
            ->where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('attended', true)
                  ->orWhere('certificate_issued', true);
            })
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('completed_at', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->whereNull('completed_at')
                         ->whereBetween('updated_at', [$start, $end]);
                  });
            })
            ->get();

        $jpWebinar = 0;
        $countedWebinarIds = [];

        foreach ($webinarRecords as $rec) {
            $w = $rec->webinar;
            $wJp = (int) ($rec->jp_earned ?: ($w?->jp ?: ($w?->course?->jp ?? 2)));
            $delivery = strtolower($w?->delivery_mode ?? 'online');

            if (in_array($delivery, ['in_class', 'workshop', 'bimtek', 'seminar'])) {
                $jpInClass += $wJp;
            } elseif ($delivery === 'hybrid') {
                $jpHybrid += $wJp;
            } else {
                $jpWebinar += $wJp;
            }
            if ($w) {
                $countedWebinarIds[] = $w->id;
            }
        }

        // Check standalone attended registrations that don't have learning record yet
        $additionalRegs = WebinarRegistration::with('webinar')
            ->where('user_id', $user->id)
            ->where('is_attended', true)
            ->whereNotIn('webinar_id', $countedWebinarIds)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('attended_at', [$start, $end])
                  ->orWhereBetween('updated_at', [$start, $end]);
            })
            ->get();

        foreach ($additionalRegs as $reg) {
            $w = $reg->webinar;
            if (!$w) continue;
            $wJp = (int) ($w->jp ?: ($w->course?->jp ?? 2));
            $delivery = strtolower($w->delivery_mode ?? 'online');

            if (in_array($delivery, ['in_class', 'workshop', 'bimtek', 'seminar'])) {
                $jpInClass += $wJp;
            } elseif ($delivery === 'hybrid') {
                $jpHybrid += $wJp;
            } else {
                $jpWebinar += $wJp;
            }
        }

        // 3. External Trainings (approved)
        $externalTrainings = ExternalTraining::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where(function ($q) use ($start, $end, $range) {
                if ($range['type'] === 'all') {
                    $q->where('year', $range['year'])
                      ->orWhereBetween('training_date', [$start, $end]);
                } else {
                    $q->whereBetween('training_date', [$start, $end]);
                }
            })
            ->get();

        $jpExternal = (int) $externalTrainings->sum('jp_count');

        $totalJp = $jpElearning + $jpWebinar + $jpInClass + $jpHybrid + $jpExternal;
        $targetJp = $user->jp_target_per_year ?: self::getDefaultTargetJp();
        $achieved = $totalJp >= $targetJp;
        $percent = $targetJp > 0 ? min(999, round(($totalJp / $targetJp) * 100)) : 0;
        $remaining = max(0, $targetJp - $totalJp);

        return [
            'user_id'       => $user->id,
            'name'          => $user->name,
            'nip'           => $user->nip ?: '-',
            'opd_name'      => $user->opd_name ?: 'Perangkat Daerah Belum Diisi',
            'jabatan'       => $user->jabatan ?: '-',
            'golongan'      => $user->pangkat_golongan ?: '-',
            'jp_elearning'  => $jpElearning,
            'jp_webinar'    => $jpWebinar,
            'jp_in_class'   => $jpInClass,
            'jp_hybrid'     => $jpHybrid,
            'jp_external'   => $jpExternal,
            'total_jp'      => $totalJp,
            'target_jp'     => $targetJp,
            'achieved'      => $achieved,
            'percentage'    => $percent,
            'remaining'     => $remaining,
            'period_label'  => $range['label'],
        ];
    }

    /**
     * Get itemized activities for a specific ASN
     */
    public function getUserJpActivities(User $user, ?int $year = null, ?string $periodType = 'all', ?string $startDate = null, ?string $endDate = null): Collection
    {
        $range = self::resolveDateRange($year, $periodType, $startDate, $endDate);
        $start = $range['start'];
        $end = $range['end'];

        $activities = collect();

        // 1. Internal Courses
        $enrollments = Enrollment::with(['course.category'])
            ->where('user_id', $user->id)
            ->where(function ($q) {
                $q->whereNotNull('completed_at')
                  ->orWhere('progress_percent', '>=', 100);
            })
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('completed_at', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->whereNull('completed_at')
                         ->whereBetween('updated_at', [$start, $end]);
                  });
            })
            ->get();

        foreach ($enrollments as $enr) {
            $c = $enr->course;
            if (!$c) continue;

            $type = strtolower($c->course_type ?? '');
            if (in_array($type, ['in-class', 'in_class', 'workshop', 'bimtek', 'seminar', 'klasikal'])) {
                $typeKey = 'in_class';
            } elseif (in_array($type, ['hybrid', 'blended'])) {
                $typeKey = 'hybrid';
            } else {
                $typeKey = 'elearning';
            }

            $activities->push([
                'id'            => 'course_' . $enr->id,
                'source'        => 'course',
                'title'         => $c->title,
                'date'          => $enr->completed_at ? Carbon::parse($enr->completed_at) : Carbon::parse($enr->updated_at),
                'type_key'      => $typeKey,
                'type_info'     => self::LEARNING_TYPES[$typeKey] ?? self::LEARNING_TYPES['elearning'],
                'category'      => $c->category?->name ?? 'Pelatihan Internal',
                'organizer'     => config('app.name', 'LMS KAB Berau BKPSDM'),
                'jp'            => (int) ($c->jp ?: 0),
                'status'        => 'Selesai',
                'badge_status'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'certificate_no'=> $user->certificates()->where('course_id', $c->id)->value('certificate_number'),
            ]);
        }

        // 2. Webinars
        $webinarRecords = WebinarLearningRecord::with('webinar')
            ->where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('attended', true)
                  ->orWhere('certificate_issued', true);
            })
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('completed_at', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->whereNull('completed_at')
                         ->whereBetween('updated_at', [$start, $end]);
                  });
            })
            ->get();

        $seenWebinarIds = [];

        foreach ($webinarRecords as $rec) {
            $w = $rec->webinar;
            if (!$w) continue;
            $seenWebinarIds[] = $w->id;

            $delivery = strtolower($w->delivery_mode ?? 'online');
            if (in_array($delivery, ['in_class', 'workshop', 'bimtek', 'seminar'])) {
                $typeKey = 'in_class';
            } elseif ($delivery === 'hybrid') {
                $typeKey = 'hybrid';
            } else {
                $typeKey = 'webinar';
            }

            $jpEarned = (int) ($rec->jp_earned ?: ($w->jp ?: ($w->course?->jp ?? 2)));

            $activities->push([
                'id'            => 'webinar_rec_' . $rec->id,
                'source'        => 'webinar',
                'title'         => $w->title,
                'date'          => $rec->completed_at ? Carbon::parse($rec->completed_at) : ($w->event_date ? Carbon::parse($w->event_date) : Carbon::parse($rec->updated_at)),
                'type_key'      => $typeKey,
                'type_info'     => self::LEARNING_TYPES[$typeKey] ?? self::LEARNING_TYPES['webinar'],
                'category'      => 'Webinar / Pembelajaran Interaktif',
                'organizer'     => $w->speaker_institution ?: ($w->speaker_name ? "Narasumber: {$w->speaker_name}" : 'Penyelenggara Internal'),
                'jp'            => $jpEarned,
                'status'        => 'Hadir & Terverifikasi',
                'badge_status'  => 'bg-teal-50 text-teal-700 border-teal-200',
                'certificate_no'=> $rec->certificate_number,
            ]);
        }

        // Webinars attended without record
        $additionalRegs = WebinarRegistration::with('webinar')
            ->where('user_id', $user->id)
            ->where('is_attended', true)
            ->whereNotIn('webinar_id', $seenWebinarIds)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('attended_at', [$start, $end])
                  ->orWhereBetween('updated_at', [$start, $end]);
            })
            ->get();

        foreach ($additionalRegs as $reg) {
            $w = $reg->webinar;
            if (!$w) continue;

            $delivery = strtolower($w->delivery_mode ?? 'online');
            $typeKey = in_array($delivery, ['in_class', 'workshop', 'bimtek', 'seminar']) ? 'in_class' : ($delivery === 'hybrid' ? 'hybrid' : 'webinar');

            $activities->push([
                'id'            => 'webinar_reg_' . $reg->id,
                'source'        => 'webinar',
                'title'         => $w->title,
                'date'          => $reg->attended_at ? Carbon::parse($reg->attended_at) : Carbon::parse($w->event_date),
                'type_key'      => $typeKey,
                'type_info'     => self::LEARNING_TYPES[$typeKey] ?? self::LEARNING_TYPES['webinar'],
                'category'      => 'Webinar / Presensi Live',
                'organizer'     => $w->speaker_institution ?: 'LMS Berau Live',
                'jp'            => (int) ($w->jp ?: ($w->course?->jp ?? 2)),
                'status'        => 'Presensi Terverifikasi',
                'badge_status'  => 'bg-teal-50 text-teal-700 border-teal-200',
                'certificate_no'=> null,
            ]);
        }

        // 3. External Trainings
        $externalTrainings = ExternalTraining::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where(function ($q) use ($start, $end, $range) {
                if ($range['type'] === 'all') {
                    $q->where('year', $range['year'])
                      ->orWhereBetween('training_date', [$start, $end]);
                } else {
                    $q->whereBetween('training_date', [$start, $end]);
                }
            })
            ->get();

        foreach ($externalTrainings as $ext) {
            $activities->push([
                'id'            => 'external_' . $ext->id,
                'source'        => 'external',
                'title'         => $ext->training_name,
                'date'          => Carbon::parse($ext->training_date),
                'type_key'      => 'external',
                'type_info'     => self::LEARNING_TYPES['external'],
                'category'      => 'Diklat Eksternal Mandiri',
                'organizer'     => $ext->institution_name,
                'jp'            => (int) $ext->jp_count,
                'status'        => 'Disetujui BKPSDM',
                'badge_status'  => 'bg-blue-50 text-blue-700 border-blue-200',
                'certificate_no'=> $ext->certificate_file ? 'Lampiran Bersertifikat' : null,
            ]);
        }

        return $activities->sortByDesc('date')->values();
    }

    /**
     * Get aggregate report for all ASN with filters
     */
    public function getAsnJpReport(array $filters = []): array
    {
        $year       = isset($filters['year']) && $filters['year'] !== 'all' ? (int) $filters['year'] : (int) date('Y');
        $periodType = $filters['period_type'] ?? 'all';
        $startDate  = $filters['start_date'] ?? null;
        $endDate    = $filters['end_date'] ?? null;
        $opdName    = $filters['opd_name'] ?? null;
        $status     = $filters['status'] ?? 'all'; // all, achieved, not_achieved
        $search     = $filters['search'] ?? null;
        $learningType = $filters['learning_type'] ?? 'all';

        $range = self::resolveDateRange($year, $periodType, $startDate, $endDate);

        // Fetch users (learners / ASN)
        $query = User::query();

        if ($opdName && $opdName !== 'all') {
            $query->where('opd_name', $opdName);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('opd_name', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->get();

        $rows = collect();
        $totalJpSum = 0;
        $achievedCount = 0;
        $notAchievedCount = 0;

        $typeSums = [
            'elearning' => 0,
            'webinar'   => 0,
            'in_class'  => 0,
            'hybrid'    => 0,
            'external'  => 0,
        ];

        foreach ($users as $user) {
            $summary = $this->getUserJpSummary($user, $year, $periodType, $startDate, $endDate);

            // Filter by compliance status
            if ($status === 'achieved' && !$summary['achieved']) {
                continue;
            }
            if ($status === 'not_achieved' && $summary['achieved']) {
                continue;
            }

            // Filter by learning type if specific type selected
            if ($learningType !== 'all') {
                $key = "jp_{$learningType}";
                if (!isset($summary[$key]) || $summary[$key] <= 0) {
                    continue;
                }
            }

            $rows->push($summary);
            $totalJpSum += $summary['total_jp'];
            if ($summary['achieved']) {
                $achievedCount++;
            } else {
                $notAchievedCount++;
            }

            $typeSums['elearning'] += $summary['jp_elearning'];
            $typeSums['webinar']   += $summary['jp_webinar'];
            $typeSums['in_class']  += $summary['jp_in_class'];
            $typeSums['hybrid']    += $summary['jp_hybrid'];
            $typeSums['external']  += $summary['jp_external'];
        }

        $totalAsn = $rows->count();
        $avgJp = $totalAsn > 0 ? round($totalJpSum / $totalAsn, 1) : 0;
        $compliancePercent = $totalAsn > 0 ? round(($achievedCount / $totalAsn) * 100, 1) : 0;

        return [
            'rows'              => $rows,
            'total_asn'         => $totalAsn,
            'total_jp_sum'      => $totalJpSum,
            'avg_jp'            => $avgJp,
            'achieved_count'    => $achievedCount,
            'not_achieved_count'=> $notAchievedCount,
            'compliance_percent'=> $compliancePercent,
            'type_sums'         => $typeSums,
            'range'             => $range,
            'available_opds'    => User::distinct()->whereNotNull('opd_name')->where('opd_name', '!=', '')->orderBy('opd_name')->pluck('opd_name'),
            'filters'           => [
                'year'          => $year,
                'period_type'   => $periodType,
                'start_date'    => $startDate,
                'end_date'      => $endDate,
                'opd_name'      => $opdName,
                'status'        => $status,
                'search'        => $search,
                'learning_type' => $learningType,
            ],
        ];
    }

    /**
     * Get aggregate report grouped by Perangkat Daerah (OPD)
     */
    public function getOpdJpReport(array $filters = []): array
    {
        $year       = isset($filters['year']) && $filters['year'] !== 'all' ? (int) $filters['year'] : (int) date('Y');
        $periodType = $filters['period_type'] ?? 'all';
        $startDate  = $filters['start_date'] ?? null;
        $endDate    = $filters['end_date'] ?? null;
        $search     = $filters['search'] ?? null;

        $range = self::resolveDateRange($year, $periodType, $startDate, $endDate);

        $users = User::all();

        // Group users by OPD
        $grouped = $users->groupBy(function ($u) {
            $opd = trim((string) $u->opd_name);
            return $opd !== '' ? $opd : 'Perangkat Daerah Lain / Belum Terdata';
        });

        $opdRows = collect();

        foreach ($grouped as $opdName => $opdUsers) {
            if ($search && !str_contains(strtolower($opdName), strtolower($search))) {
                continue;
            }

            $opdTotalAsn = $opdUsers->count();
            $opdTotalJp = 0;
            $opdAchievedCount = 0;
            $opdTypeSums = [
                'elearning' => 0,
                'webinar'   => 0,
                'in_class'  => 0,
                'hybrid'    => 0,
                'external'  => 0,
            ];

            foreach ($opdUsers as $u) {
                $summary = $this->getUserJpSummary($u, $year, $periodType, $startDate, $endDate);
                $opdTotalJp += $summary['total_jp'];
                if ($summary['achieved']) {
                    $opdAchievedCount++;
                }
                $opdTypeSums['elearning'] += $summary['jp_elearning'];
                $opdTypeSums['webinar']   += $summary['jp_webinar'];
                $opdTypeSums['in_class']  += $summary['jp_in_class'];
                $opdTypeSums['hybrid']    += $summary['jp_hybrid'];
                $opdTypeSums['external']  += $summary['jp_external'];
            }

            $avgJp = $opdTotalAsn > 0 ? round($opdTotalJp / $opdTotalAsn, 1) : 0;
            $complianceRate = $opdTotalAsn > 0 ? round(($opdAchievedCount / $opdTotalAsn) * 100, 1) : 0;

            // Determine predicate based on compliance rate
            if ($complianceRate >= 80) {
                $predicate = 'Sangat Baik';
                $predicateBadge = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            } elseif ($complianceRate >= 60) {
                $predicate = 'Baik';
                $predicateBadge = 'bg-blue-50 text-blue-700 border-blue-200';
            } elseif ($complianceRate >= 40) {
                $predicate = 'Cukup';
                $predicateBadge = 'bg-amber-50 text-amber-700 border-amber-200';
            } else {
                $predicate = 'Perlu Perhatian';
                $predicateBadge = 'bg-rose-50 text-rose-700 border-rose-200';
            }

            $opdRows->push([
                'opd_name'           => $opdName,
                'total_asn'          => $opdTotalAsn,
                'total_jp'           => $opdTotalJp,
                'avg_jp'             => $avgJp,
                'achieved_count'     => $opdAchievedCount,
                'not_achieved_count' => $opdTotalAsn - $opdAchievedCount,
                'compliance_rate'    => $complianceRate,
                'predicate'          => $predicate,
                'predicate_badge'    => $predicateBadge,
                'type_sums'          => $opdTypeSums,
            ]);
        }

        // Sort OPD by compliance rate desc then total JP desc
        $opdRows = $opdRows->sortByDesc('compliance_rate')->values();

        $grandTotalAsn = $opdRows->sum('total_asn');
        $grandTotalJp = $opdRows->sum('total_jp');
        $grandAchievedCount = $opdRows->sum('achieved_count');
        $grandAvgJp = $grandTotalAsn > 0 ? round($grandTotalJp / $grandTotalAsn, 1) : 0;
        $grandCompliance = $grandTotalAsn > 0 ? round(($grandAchievedCount / $grandTotalAsn) * 100, 1) : 0;

        return [
            'rows'              => $opdRows,
            'total_opd'         => $opdRows->count(),
            'grand_total_asn'   => $grandTotalAsn,
            'grand_total_jp'    => $grandTotalJp,
            'grand_avg_jp'      => $grandAvgJp,
            'grand_achieved'    => $grandAchievedCount,
            'grand_compliance'  => $grandCompliance,
            'range'             => $range,
            'filters'           => [
                'year'          => $year,
                'period_type'   => $periodType,
                'start_date'    => $startDate,
                'end_date'      => $endDate,
                'search'        => $search,
            ],
        ];
    }

    /**
     * Generate CSV content for ASN JP report
     */
    public function generateAsnCsv(Collection $rows, string $periodLabel): string
    {
        $output = fopen('php://temp', 'r+');

        // UTF-8 BOM for Microsoft Excel compatibility
        fputs($output, "\xEF\xBB\xBF");

        // Header info
        fputcsv($output, ['REKAPITULASI CAPAIAN JAM PELAJARAN (JP) APARATUR SIPIL NEGARA']);
        fputcsv($output, ['Periode:', $periodLabel]);
        fputcsv($output, ['Tanggal Ekspor:', now()->translatedFormat('d F Y H:i:s')]);
        fputcsv($output, []); // empty line

        // Column headings
        fputcsv($output, [
            'No',
            'Nama ASN',
            'NIP',
            'Perangkat Daerah (OPD)',
            'Jabatan',
            'Golongan',
            'JP E-Learning Mandiri',
            'JP Webinar/Live',
            'JP Klasikal/Bimtek',
            'JP Hybrid',
            'JP Eksternal Disetujui',
            'Total Akumulasi JP',
            'Target JP Tahunan',
            'Persentase Capaian (%)',
            'Status Pemenuhan KAK (Min 20 JP)',
            'Kekurangan JP',
        ]);

        $no = 1;
        foreach ($rows as $r) {
            fputcsv($output, [
                $no++,
                $r['name'],
                $r['nip'] !== '-' ? "'".$r['nip'] : '-',
                $r['opd_name'],
                $r['jabatan'],
                $r['golongan'],
                $r['jp_elearning'],
                $r['jp_webinar'],
                $r['jp_in_class'],
                $r['jp_hybrid'],
                $r['jp_external'],
                $r['total_jp'],
                $r['target_jp'],
                $r['percentage'] . '%',
                $r['achieved'] ? 'MEMENUHI TARGET' : 'BELUM MEMENUHI',
                $r['remaining'] > 0 ? $r['remaining'] . ' JP' : '0 JP (Tercapai)',
            ]);
        }

        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);

        return $content;
    }

    /**
     * Generate CSV content for OPD JP report
     */
    public function generateOpdCsv(Collection $rows, string $periodLabel): string
    {
        $output = fopen('php://temp', 'r+');

        // UTF-8 BOM for Microsoft Excel compatibility
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, ['REKAPITULASI KEPATUHAN PENGEMBANGAN KOMPETENSI (JP) PER PERANGKAT DAERAH']);
        fputcsv($output, ['Periode:', $periodLabel]);
        fputcsv($output, ['Tanggal Ekspor:', now()->translatedFormat('d F Y H:i:s')]);
        fputcsv($output, []);

        fputcsv($output, [
            'No',
            'Nama Perangkat Daerah (OPD)',
            'Jumlah ASN',
            'Total JP Akumulasi OPD',
            'Rata-rata JP per ASN',
            'ASN Memenuhi Target (>=20 JP)',
            'ASN Belum Memenuhi',
            'Tingkat Kepatuhan OPD (%)',
            'Predikat Kepatuhan',
            'JP E-Learning',
            'JP Webinar',
            'JP Klasikal/Bimtek',
            'JP Hybrid',
            'JP Eksternal',
        ]);

        $no = 1;
        foreach ($rows as $r) {
            fputcsv($output, [
                $no++,
                $r['opd_name'],
                $r['total_asn'],
                $r['total_jp'],
                $r['avg_jp'],
                $r['achieved_count'],
                $r['not_achieved_count'],
                $r['compliance_rate'] . '%',
                $r['predicate'],
                $r['type_sums']['elearning'],
                $r['type_sums']['webinar'],
                $r['type_sums']['in_class'],
                $r['type_sums']['hybrid'],
                $r['type_sums']['external'],
            ]);
        }

        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);

        return $content;
    }
}
