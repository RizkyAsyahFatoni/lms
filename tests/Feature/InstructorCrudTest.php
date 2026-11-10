<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Category;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuestionBank;
use App\Models\BankQuestion;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Support\Str;

class InstructorCrudTest extends TestCase
{
    protected User $instructor;
    protected User $student;
    protected Course $course;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Siapkan Akun Instruktur / Narasumber
        $this->instructor = User::updateOrCreate(
            ['email' => 'narasumber@berau.go.id'],
            [
                'name' => 'Fajar Hidayat, M.Kom',
                'password' => bcrypt('password'),
                'role' => 'narasumber',
                'nip' => '197903152005011008',
                'nik' => '6403011503790003',
                'jabatan' => 'Widyaiswara Ahli Madya',
                'opd_name' => 'Badan Kepegawaian dan Pengembangan SDM',
                'trainer_status' => 'approved',
                'is_active' => true,
            ]
        );

        // Siapkan Siswa / Peserta Belajar ASN
        $this->student = User::updateOrCreate(
            ['email' => 'asn.learner.test@berau.go.id'],
            [
                'name' => 'Budi Santoso, S.AP',
                'password' => bcrypt('password'),
                'role' => 'asn_learner',
                'nip' => '199201012015031002',
                'nik' => '6403010101920002',
                'jabatan' => 'Pranata Komputer Pertama',
                'opd_name' => 'Dinas Komunikasi dan Informatika',
                'is_active' => true,
            ]
        );

        // Pastikan kategori tersedia
        $this->category = Category::firstOrCreate(
            ['slug' => 'teknologi-informasi'],
            [
                'name' => 'Teknologi Informasi & SPBE',
                'color' => 'blue',
                'icon' => 'server',
                'is_active' => true,
            ]
        );

        // Kursus yang diampu oleh instruktur
        $this->course = Course::firstOrCreate(
            ['slug' => 'manajemen-keamanan-informasi-spbe'],
            [
                'title' => 'Manajemen Keamanan Informasi & CSIRT SPBE Berau',
                'category_id' => $this->category->id,
                'instructor_id' => $this->instructor->id,
                'description' => 'Kursus teknis bagi aparatur pemerintah daerah untuk pengelolaan insiden siber.',
                'course_type' => 'e-course',
                'level' => 'Menengah',
                'jp' => 10,
                'duration_hours' => 5,
                'approval_status' => 'approved',
                'is_published' => true,
                'certificate_available' => true,
                'created_by' => $this->instructor->id,
            ]
        );
    }

    /**
     * 1. Uji akses ke Ruang Kerja / Dashboard Narasumber
     */
    public function test_instructor_can_access_workspace_dashboard(): void
    {
        $response = $this->actingAs($this->instructor)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Ruang Kerja Pengajar');
        $response->assertSee($this->instructor->name);
        $response->assertSee('Silabus yang Anda Ampu');
        $response->assertSee($this->course->title);
    }

    /**
     * 2. Uji CRUD Pembuatan & Manajemen Materi Modul Kursus (Lesson CRUD)
     */
    public function test_instructor_course_lessons_crud(): void
    {
        // A. CREATE: Tambah modul materi baru bertipe Video
        $videoTitle = 'Pengenalan Arsitektur Keamanan Siber ' . Str::random(4);
        $createVideoResponse = $this->actingAs($this->instructor)->post("/admin/courses/{$this->course->id}/lessons", [
            'section_title' => 'BAB 1: Fundamental Keamanan',
            'title' => $videoTitle,
            'type' => 'video',
            'duration_minutes' => 25,
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'is_free_preview' => '1',
        ]);

        $createVideoResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('lessons', [
            'course_id' => $this->course->id,
            'title' => $videoTitle,
            'type' => 'video',
        ]);

        $videoLesson = Lesson::where('title', $videoTitle)->firstOrFail();

        // B. CREATE: Tambah modul kuis evaluasi awal (Pre-Test) yang auto-generate Quiz & Soal
        $quizTitle = 'Evaluasi Awal CSIRT ' . Str::random(4);
        $createQuizResponse = $this->actingAs($this->instructor)->post("/admin/courses/{$this->course->id}/lessons", [
            'section_title' => 'BAB 1: Fundamental Keamanan',
            'title' => $quizTitle,
            'type' => 'pre_test',
            'duration_minutes' => 15,
        ]);

        $createQuizResponse->assertSessionHasNoErrors();
        $quizLesson = Lesson::where('title', $quizTitle)->firstOrFail();
        $this->assertDatabaseHas('quizzes', [
            'lesson_id' => $quizLesson->id,
        ]);
        $this->assertDatabaseHas('quiz_questions', [
            'quiz_id' => $quizLesson->quiz->id,
        ]);

        // C. READ: Tinjau silabus di halaman manajemen modul
        $syllabusResponse = $this->actingAs($this->instructor)->get("/admin/courses/{$this->course->id}/lessons");
        $syllabusResponse->assertStatus(200);
        $syllabusResponse->assertSee($videoTitle);
        $syllabusResponse->assertSee($quizTitle);

        // D. DELETE: Hapus salah satu modul materi dan verifikasi keterhapusan di database
        $deleteResponse = $this->actingAs($this->instructor)->delete("/admin/courses/lessons/{$videoLesson->id}");
        $deleteResponse->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('lessons', [
            'id' => $videoLesson->id,
        ]);
    }

    /**
     * 3. Uji CRUD Pengelolaan Bank Soal & Kuis (Question Bank CRUD)
     */
    public function test_instructor_quiz_and_question_bank_crud(): void
    {
        // A. CREATE: Buat Bank Soal Baru
        $bankName = 'Bank Soal Kriptografi & SPBE ' . Str::random(5);
        $storeBankResponse = $this->actingAs($this->instructor)->post('/admin/question-banks', [
            'name' => $bankName,
            'category' => 'Keamanan Siber',
            'description' => 'Kumpulan soal ujian sertifikasi kompetensi aparat persandian daerah.',
        ]);

        $storeBankResponse->assertSessionHasNoErrors();
        $bank = QuestionBank::where('name', $bankName)->firstOrFail();
        $this->assertEquals($this->instructor->id, $bank->created_by);

        // B. READ: Buka detail Bank Soal
        $detailResponse = $this->actingAs($this->instructor)->get("/admin/question-banks/{$bank->id}");
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee($bankName);

        // C. UPDATE: Perbarui nama dan deskripsi bank soal
        $updatedBankName = $bankName . ' (Revisi 2026)';
        $updateBankResponse = $this->actingAs($this->instructor)->post("/admin/question-banks/{$bank->id}", [
            'name' => $updatedBankName,
            'category' => 'Keamanan Siber Terapan',
            'description' => 'Bank soal telah dimutakhirkan dengan standar ISO 27001.',
        ]);

        $updateBankResponse->assertSessionHasNoErrors();
        $bank->refresh();
        $this->assertEquals($updatedBankName, $bank->name);

        // D. CREATE QUESTION: Tambahkan butir pertanyaan pilihan ganda ke bank soal
        $questionText = 'Apa kepanjangan dari CSIRT dalam tata kelola keamanan SPBE?';
        $storeQuestionResponse = $this->actingAs($this->instructor)->post("/admin/question-banks/{$bank->id}/questions", [
            'question' => $questionText,
            'type' => 'multiple_choice',
            'option_a' => 'Computer Security Incident Response Team',
            'option_b' => 'Cyber Security Information Resource Team',
            'option_c' => 'Central System Internet Remote Tool',
            'option_d' => 'Client Server Integration Rapid Technology',
            'correct_answer' => 'A',
            'explanation' => 'CSIRT merupakan tim tanggap insiden keamanan siber resmi di instansi pemerintah.',
            'difficulty' => 'easy',
            'points' => 5,
        ]);

        $storeQuestionResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('bank_questions', [
            'question_bank_id' => $bank->id,
            'question' => $questionText,
            'correct_answer' => 'A',
            'points' => 5,
        ]);

        $bankQuestion = BankQuestion::where('question', $questionText)->firstOrFail();

        // E. DELETE QUESTION: Hapus butir pertanyaan dari bank soal
        $deleteQuestionResponse = $this->actingAs($this->instructor)->delete("/admin/question-banks/questions/{$bankQuestion->id}");
        $deleteQuestionResponse->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('bank_questions', [
            'id' => $bankQuestion->id,
        ]);

        // F. DELETE BANK: Hapus seluruh bank soal
        $deleteBankResponse = $this->actingAs($this->instructor)->delete("/admin/question-banks/{$bank->id}");
        $deleteBankResponse->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('question_banks', [
            'id' => $bank->id,
        ]);
    }

    /**
     * 4. Uji Penilaian Tugas Peserta Didik (Assignment Grading & Feedback)
     */
    public function test_instructor_can_grade_student_assignment_submission(): void
    {
        // Siapkan materi bertipe assignment pada kursus yang diampu
        $assignmentLesson = Lesson::create([
            'course_id' => $this->course->id,
            'section_title' => 'BAB 2: Praktik Audit',
            'title' => 'Tugas Praktik: Penyusunan SOP Penanganan Insiden',
            'slug' => 'tugas-praktik-sop-insiden-' . Str::random(4),
            'order' => 10,
            'type' => 'assignment',
            'duration_minutes' => 60,
        ]);

        $assignment = Assignment::create([
            'lesson_id' => $assignmentLesson->id,
            'title' => 'Tugas Praktik: Penyusunan SOP Penanganan Insiden',
            'instructions' => 'Susun dokumen SOP penanganan insiden phishing dan upload file laporan.',
            'max_score' => 100,
        ]);

        // Simulasikan pengumpulan tugas oleh siswa
        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'user_id' => $this->student->id,
            'content' => 'Berikut draf SOP penanganan insiden phishing di lingkungan Diskominfo.',
            'file_path' => 'submissions/sop_insiden_budi.pdf',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        // Instruktur membuka antrian penilaian di dashboard
        $dashboardResponse = $this->actingAs($this->instructor)->get('/dashboard/narasumber');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee($this->student->name);
        $dashboardResponse->assertSee($assignment->title);

        // Instruktur memberikan nilai dan catatan umpan balik (Grade Submission)
        $score = 95;
        $feedback = 'Draf SOP sangat lengkap, alur eskalasi insiden ke BSSN sudah tepat sasaran.';

        $gradeResponse = $this->actingAs($this->instructor)->post("/narasumber/submissions/{$submission->id}/grade", [
            'score' => $score,
            'feedback' => $feedback,
        ]);

        $gradeResponse->assertSessionHasNoErrors();

        // Verifikasi hasil penilaian tersimpan di database
        $submission->refresh();
        $this->assertEquals('graded', $submission->status);
        $this->assertEquals(95, $submission->score);
        $this->assertEquals($feedback, $submission->feedback);
    }
}
