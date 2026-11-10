<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Category;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\Certificate;
use App\Models\AttendanceSession;
use App\Models\Attendance;
use Illuminate\Support\Str;

class LearnerFeatureTest extends TestCase
{
    protected User $learner;
    protected User $instructor;
    protected Category $category;
    protected Course $course;
    protected Lesson $lessonReading;
    protected Lesson $lessonQuiz;
    protected Quiz $quiz;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Akun Instruktur
        $this->instructor = User::updateOrCreate(
            ['email' => 'narasumber.learner.test@berau.go.id'],
            [
                'name' => 'Fajar Hidayat, M.Kom',
                'password' => bcrypt('password'),
                'role' => 'narasumber',
                'nip' => '197903152005011008',
                'jabatan' => 'Widyaiswara Ahli Madya',
                'opd_name' => 'BKPSDM Kabupaten Berau',
                'is_active' => true,
            ]
        );

        // 2. Akun Peserta / ASN Learner
        $this->learner = User::updateOrCreate(
            ['email' => 'budi@example.com'],
            [
                'name' => 'Budi Santoso, S.Kom',
                'password' => bcrypt('password'),
                'role' => 'asn_learner',
                'nip' => '198805142014021003',
                'nik' => '6403011405880003',
                'jabatan' => 'Pranata Komputer Pertama',
                'opd_name' => 'Dinas Komunikasi dan Informatika',
                'pangkat_golongan' => 'Penata Muda Tk. I (III/b)',
                'is_active' => true,
                'jp_target_per_year' => 20,
            ]
        );

        // 3. Kategori Kursus
        $this->category = Category::firstOrCreate(
            ['slug' => 'transformasi-digital-asn'],
            [
                'name' => 'Transformasi Digital ASN',
                'color' => 'blue',
                'icon' => 'laptop',
                'is_active' => true,
            ]
        );

        // 4. Kursus Pembelajaran
        $this->course = Course::updateOrCreate(
            ['slug' => 'literasi-keamanan-data-asn-berau'],
            [
                'title' => 'Literasi Keamanan Data dan Perlindungan Informasi ASN Berau',
                'category_id' => $this->category->id,
                'instructor_id' => $this->instructor->id,
                'description' => 'Materi peningkatan kesadaran keamanan siber bagi aparatur pemerintah daerah.',
                'course_type' => 'e-course',
                'level' => 'Dasar',
                'jp' => 4,
                'duration_hours' => 2,
                'approval_status' => 'approved',
                'is_published' => true,
                'certificate_available' => true,
                'created_by' => $this->instructor->id,
            ]
        );

        // 5. Modul 1: Bacaan Materi
        $this->lessonReading = Lesson::updateOrCreate(
            ['course_id' => $this->course->id, 'slug' => 'pengenalan-prinsip-keamanan-informasi'],
            [
                'section_title' => 'BAB 1: Fondasi Keamanan',
                'title' => 'Pengenalan Prinsip Keamanan Informasi (CIA Triad)',
                'order' => 1,
                'type' => 'reading',
                'duration_minutes' => 30,
                'content' => 'Prinsip Confidentiality, Integrity, dan Availability dalam SPBE.',
            ]
        );

        // 6. Modul 2: Kuis Evaluasi (Post-Test)
        $this->lessonQuiz = Lesson::updateOrCreate(
            ['course_id' => $this->course->id, 'slug' => 'evaluasi-pemahaman-keamanan-siber'],
            [
                'section_title' => 'BAB 2: Evaluasi Kompetensi',
                'title' => 'Uji Pemahaman Materi Keamanan Siber',
                'order' => 2,
                'type' => 'post_test',
                'duration_minutes' => 15,
            ]
        );

        $this->quiz = Quiz::updateOrCreate(
            ['lesson_id' => $this->lessonQuiz->id],
            [
                'title' => 'Kuis Uji Kelulusan Keamanan Siber',
                'passing_score' => 70,
                'duration_minutes' => 15,
            ]
        );

        QuizQuestion::updateOrCreate(
            ['quiz_id' => $this->quiz->id, 'order' => 1],
            [
                'question' => 'Apa aspek keamanan yang memastikan data tidak dapat diubah oleh pihak tidak berwenang?',
                'options' => [
                    'A' => 'Confidentiality (Kerahasiaan)',
                    'B' => 'Integrity (Integritas Data)',
                    'C' => 'Availability (Ketersediaan)',
                    'D' => 'Non-Repudiation',
                ],
                'correct_answer' => 'B',
                'explanation' => 'Integrity menjamin keakuratan dan kelengkapan data yang tersimpan.',
            ]
        );

        // Bersihkan progress sebelumnya agar pengujian deterministik
        LessonProgress::where('user_id', $this->learner->id)->whereIn('lesson_id', [$this->lessonReading->id, $this->lessonQuiz->id])->delete();
        Certificate::where('user_id', $this->learner->id)->where('course_id', $this->course->id)->delete();
        Enrollment::where('user_id', $this->learner->id)->where('course_id', $this->course->id)->delete();
    }

    /**
     * 1. Uji Akses Portal Belajar ASN (Learner Dashboard & Target 20 JP)
     */
    public function test_asn_learner_can_access_dashboard_and_jp_tracker(): void
    {
        $response = $this->actingAs($this->learner)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee($this->learner->name);
        $response->assertSee($this->learner->nip);
        $response->assertSee('JP');
        $response->assertSee('Cetak Transkrip JP');
    }

    /**
     * 2. Uji Pendaftaran Kursus dan Akses Ruang Belajar (Classroom Player)
     */
    public function test_asn_learner_can_enroll_and_navigate_classroom(): void
    {
        // Masuk ke ruang belajar kursus (otomatis enroll jika belum terdaftar)
        $classroomResponse = $this->actingAs($this->learner)
            ->get("/learn/{$this->course->slug}/{$this->lessonReading->slug}");

        $classroomResponse->assertStatus(200);
        $classroomResponse->assertSee($this->course->title);
        $classroomResponse->assertSee($this->lessonReading->title);

        // Verifikasi pendaftaran (Enrollment) tersimpan
        $this->assertDatabaseHas('enrollments', [
            'user_id' => $this->learner->id,
            'course_id' => $this->course->id,
        ]);

        // Tandai modul 1 (Bacaan) selesai
        $completeResponse = $this->actingAs($this->learner)
            ->post("/learn/{$this->course->slug}/{$this->lessonReading->slug}/complete");

        $completeResponse->assertSessionHasNoErrors();
        
        $progress = LessonProgress::where('user_id', $this->learner->id)
            ->where('lesson_id', $this->lessonReading->id)
            ->first();
        $this->assertNotNull($progress);
        $this->assertTrue((bool)$progress->is_completed);
    }

    /**
     * 3. Uji Pengerjaan Kuis dan Perhitungan Kelulusan Skor
     */
    public function test_asn_learner_can_take_quiz_and_pass(): void
    {
        // Pastikan terdaftar
        Enrollment::firstOrCreate([
            'user_id' => $this->learner->id,
            'course_id' => $this->course->id,
        ], ['progress_percent' => 50]);

        $question = $this->quiz->questions()->firstOrFail();

        // Submit jawaban benar (100% skor -> LULUS)
        $quizResponse = $this->actingAs($this->learner)
            ->post("/learn/{$this->course->slug}/{$this->lessonQuiz->slug}/quiz-submit", [
                'answers' => [
                    $question->id => 'B', // Jawaban benar adalah B
                ],
            ]);

        $quizResponse->assertSessionHasNoErrors();

        // Verifikasi riwayat pengerjaan kuis (QuizAttempt)
        $this->assertDatabaseHas('quiz_attempts', [
            'user_id' => $this->learner->id,
            'quiz_id' => $this->quiz->id,
            'score' => 100,
            'is_passed' => 1,
        ]);

        // Verifikasi modul kuis ditandai selesai
        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $this->learner->id,
            'lesson_id' => $this->lessonQuiz->id,
            'is_completed' => 1,
        ]);
    }

    /**
     * 4. Uji Presensi Digital via QR Code
     */
    public function test_asn_learner_can_record_qr_attendance(): void
    {
        // Buat sesi presensi aktif dengan token unik
        $sessionCode = 'QR-' . strtoupper(Str::random(6));
        $qrToken = 'SECRET-' . strtoupper(Str::random(10));

        $session = AttendanceSession::create([
            'title' => 'Sesi Tatap Muka: Pembekalan ASN Literasi Digital',
            'session_code' => $sessionCode,
            'attendable_type' => Course::class,
            'attendable_id' => $this->course->id,
            'date' => now()->toDateString(),
            'start_time' => now()->subMinutes(10),
            'end_time' => now()->addHours(2),
            'allowed_methods' => 'both',
            'min_duration_minutes' => 60,
            'is_active' => true,
            'qr_secret_token' => $qrToken,
        ]);

        // 1. Tinjau halaman pemindaian QR
        $scanViewResponse = $this->actingAs($this->learner)->get("/attendance/scan/{$sessionCode}?token={$qrToken}");
        $scanViewResponse->assertStatus(200);
        $scanViewResponse->assertSee($session->title);

        // 2. Submit presensi via QR
        $submitResponse = $this->actingAs($this->learner)->post("/attendance/session/{$session->id}/qr", [
            'token' => $qrToken,
        ]);

        $submitResponse->assertRedirect(route('attendance.history'));

        // Verifikasi presensi tercatat di database
        $this->assertDatabaseHas('attendances', [
            'attendance_session_id' => $session->id,
            'user_id' => $this->learner->id,
            'method' => 'qr_code',
            'status' => 'present',
        ]);

        // 3. Buka riwayat presensi pribadi
        $historyResponse = $this->actingAs($this->learner)->get('/presensi');
        $historyResponse->assertStatus(200);
        $historyResponse->assertSee($session->title);
    }

    /**
     * 5. Uji Terbit & Verifikasi E-Sertifikat Digital (100% Selesai)
     */
    public function test_certificate_generated_and_verified_after_completion(): void
    {
        // Pastikan enrollment terdaftar
        Enrollment::firstOrCreate([
            'user_id' => $this->learner->id,
            'course_id' => $this->course->id,
        ], ['progress_percent' => 0]);

        // Tandai modul 1 (Bacaan) selesai
        LessonProgress::updateOrCreate(
            ['user_id' => $this->learner->id, 'lesson_id' => $this->lessonReading->id],
            ['is_completed' => true, 'completed_at' => now()]
        );

        // Selesaikan modul 2 (Kuis) untuk mencapai progres 100%
        $completeResponse = $this->actingAs($this->learner)
            ->post("/learn/{$this->course->slug}/{$this->lessonQuiz->slug}/complete");

        $completeResponse->assertSessionHasNoErrors();

        // Verifikasi Sertifikat otomatis terbit di database
        $this->assertDatabaseHas('certificates', [
            'user_id' => $this->learner->id,
            'course_id' => $this->course->id,
        ]);

        $cert = Certificate::where('user_id', $this->learner->id)
            ->where('course_id', $this->course->id)
            ->firstOrFail();

        // Buka tampilan resmi sertifikat (Plural & Singular)
        $certResponse = $this->actingAs($this->learner)->get("/certificates/{$cert->certificate_number}");
        $certResponse->assertStatus(200);
        $certResponse->assertSee($this->learner->name);
        $certResponse->assertSee($this->course->title);
        $certResponse->assertSee($cert->certificate_number);

        // Uji rute singular /certificate/{number}
        $certSingularResponse = $this->get("/certificate/{$cert->certificate_number}");
        $certSingularResponse->assertStatus(200);

        // Uji rute sertifikat seeded CERT-SATU-2026-00892
        $satuCertResponse = $this->get("/certificate/CERT-SATU-2026-00892");
        $satuCertResponse->assertStatus(200);
        $satuCertResponse->assertSee('CERT-SATU-2026-00892');

        // Verifikasi publik lewat hash QR
        $verifyResponse = $this->get("/certificates/verify/{$cert->verification_hash}");
        $verifyResponse->assertStatus(200);
        $verifyResponse->assertSee($this->learner->name);
    }

    /**
     * 6. Uji Akses Transkrip Jam Pelajaran Resmi (Official Learning Record)
     */
    public function test_asn_learner_can_view_official_jp_transcript(): void
    {
        $transcriptResponse = $this->actingAs($this->learner)->get('/learning-record/transcript');

        $transcriptResponse->assertStatus(200);
        $transcriptResponse->assertSee('BUKU CATATAN PEMBELAJARAN (LEARNING RECORD) ASN');
        $transcriptResponse->assertSee($this->learner->name);
        $transcriptResponse->assertSee($this->learner->nip);
        $transcriptResponse->assertSee($this->learner->opd_name);
        $transcriptResponse->assertSee('20 JP');
    }
}
