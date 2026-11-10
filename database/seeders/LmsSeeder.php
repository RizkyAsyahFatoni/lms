<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\Certificate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LmsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users (Admin, Instructors, Student)
        $admin = User::create([
            'name' => 'Administrator LMS',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'headline' => 'System & Academy Director',
            'bio' => 'Pengelola sistem manajemen pembelajaran dan kurikulum terintegrasi.',
        ]);

        $inst1 = User::create([
            'name' => 'dr. Raditya Pratama, Sp.A, M.Ked',
            'email' => 'raditya@example.com',
            'password' => Hash::make('password'),
            'role' => 'instructor',
            'headline' => 'Konsultan Transformasi Digital & Tata Kelola Pelayanan Publik',
            'bio' => 'Praktisi teknologi kesehatan dan fasilitator nasional implementasi interoperabilitas data rekam medis elektronik.',
        ]);

        $inst2 = User::create([
            'name' => 'Fajar Hidayat, M.Kom',
            'email' => 'fajar@example.com',
            'password' => Hash::make('password'),
            'role' => 'instructor',
            'headline' => 'Principal AI Architect & Data Systems Engineer',
            'bio' => 'Pengembang solusi machine learning dan otomasi kerja cerdas untuk organisasi publik dan korporasi modern.',
        ]);

        $inst3 = User::create([
            'name' => 'Sarah Wulandari, S.STP, M.P.A',
            'email' => 'sarah@example.com',
            'password' => Hash::make('password'),
            'role' => 'instructor',
            'headline' => 'Widyaiswara Ahli & Fasilitator Kepemimpinan Publik',
            'bio' => 'Spesialis transformasi birokrasi, budaya kerja agile, dan manajemen kinerja organisasi modern.',
        ]);

        $student = User::create([
            'name' => 'Budi Santoso, S.Kom',
            'email' => 'budi@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'headline' => 'Analis Sistem Informasi & Peserta Diklat',
            'bio' => 'Aktif mengembangkan kompetensi di bidang arsitektur perangkat lunak dan transformasi layanan publik.',
        ]);

        // 2. Categories
        $catHealth = Category::create([
            'name' => 'Kesehatan Digital & RME',
            'slug' => 'kesehatan-digital-rme',
            'icon' => 'activity',
            'badge_color' => 'teal',
            'description' => 'Materi regulasi SPBE terpadu, interoperabilitas data daerah, dan arsitektur layanan publik.',
        ]);

        $catAI = Category::create([
            'name' => 'Teknologi & AI Terapan',
            'slug' => 'teknologi-ai-terapan',
            'icon' => 'cpu',
            'badge_color' => 'cyan',
            'description' => 'Peningkatan produktivitas berbasis kecerdasan buatan, prompt engineering, dan otomasi.',
        ]);

        $catCyber = Category::create([
            'name' => 'Keamanan Siber & Regulasi PDP',
            'slug' => 'keamanan-siber-regulasi-pdp',
            'icon' => 'shield',
            'badge_color' => 'indigo',
            'description' => 'Proteksi data sensitif, kepatuhan UU PDP, dan mitigasi ancaman siber harian.',
        ]);

        $catLeadership = Category::create([
            'name' => 'Pengembangan ASN & Kepemimpinan',
            'slug' => 'pengembangan-asn-kepemimpinan',
            'icon' => 'award',
            'badge_color' => 'emerald',
            'description' => 'Upskilling kompetensi manajerial, pelayanan publik prima, dan kepemimpinan adaptif.',
        ]);

        // 3. Course 1 (Featured SPBE & Digital Governance)
        $course1 = Course::create([
            'category_id' => $catHealth->id,
            'instructor_id' => $inst1->id,
            'title' => 'Arsitektur & Tata Kelola Sistem Pemerintahan Berbasis Elektronik (SPBE) Terpadu',
            'slug' => 'arsitektur-tata-kelola-spbe-terpadu',
            'subtitle' => 'Panduan komprehensif implementasi arsitektur SPBE, interoperabilitas data antar-OPD, dan integrasi portal layanan publik Pemerintah Kabupaten Berau.',
            'description' => 'Pelatihan ini dirancang untuk tenaga kesehatan, pengembang perangkat lunak fasilitas kesehatan, dan dinas kesehatan guna memenuhi mandat integrasi rekam medis elektronik nasional sesuai Permenkes No. 24 Tahun 2022. Anda akan mempelajari arsitektur data FHIR, implementasi keamanan token OAuth2, hingga pengujian sandbox resmi.',
            'thumbnail' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=800&q=80',
            'level' => 'Menengah',
            'price' => 0, // Gratis Program Nasional
            'rating' => 4.98,
            'students_count' => 1420,
            'duration_hours' => 6,
            'certificate_available' => true,
            'is_featured' => true,
            'is_published' => true,
        ]);

        // Lessons for Course 1 (Comprehensive Multi-Format Showcase)
        
        // 1. PRE-TEST
        $lPreTest = Lesson::create([
            'course_id' => $course1->id,
            'section_title' => 'Bab 0: Evaluasi Awal (Pre-Test)',
            'title' => 'Pre-Test: Pemetaan Kompetensi Awal Standarisasi RME',
            'slug' => 'pre-test-kompetensi-awal-rme',
            'order' => 1,
            'type' => 'pre_test',
            'duration_minutes' => 15,
            'is_free_preview' => true,
            'content' => 'Pre-Test ini bertujuan untuk mengukur pemahaman awal Anda terkait tata kelola SPBE dan transformasi digital.',
        ]);

        $quizPre = \App\Models\Quiz::create([
            'lesson_id' => $lPreTest->id,
            'title' => 'Pre-Test Pemetaan Pengetahuan Tata Kelola SPBE',
            'description' => 'Evaluasi awal terdiri dari 3 soal pilihan ganda. Nilai kelulusan minimal 70.',
            'passing_score' => 70,
            'duration_minutes' => 15,
        ]);

        \App\Models\QuizQuestion::create([
            'quiz_id' => $quizPre->id,
            'question' => 'Apakah payung hukum utama yang mewajibkan seluruh fasilitas pelayanan kesehatan di Indonesia menerapkan Rekam Medis Elektronik (RME)?',
            'options' => [
                'A' => 'Permenkes No. 24 Tahun 2022',
                'B' => 'Permenkes No. 12 Tahun 2018',
                'C' => 'UU No. 11 Tahun 2008 tentang ITE',
                'D' => 'Kepmenkes No. 514 Tahun 2015',
            ],
            'correct_answer' => 'A',
            'explanation' => 'Peraturan Presiden No. 95 Tahun 2018 adalah regulasi resmi yang menjadi pedoman nasional implementasi SPBE terpadu.',
            'order' => 1,
        ]);

        \App\Models\QuizQuestion::create([
            'quiz_id' => $quizPre->id,
            'question' => 'Prinsip utama yang wajib diterapkan dalam arsitektur interoperabilitas Satu Data Indonesia (SDI) tingkat daerah adalah:',
            'options' => [
                'A' => 'DICOM Standard Version 3',
                'B' => 'HL7 FHIR (Fast Healthcare Interoperability Resources) Release 4',
                'C' => 'XML SOAP Service Definition',
                'D' => 'EDIFACT Healthcare Specification',
            ],
            'correct_answer' => 'B',
            'explanation' => 'Interoperabilitas data daerah mengedepankan standar data terbuka, metadata terstruktur, dan integrasi antar-OPD melalui API resmi.',
            'order' => 2,
        ]);

        \App\Models\QuizQuestion::create([
            'quiz_id' => $quizPre->id,
            'question' => 'Resource FHIR apa yang merepresentasikan riwayat kedatangan atau kontak pasien di fasilitas kesehatan?',
            'options' => [
                'A' => 'Condition',
                'B' => 'Observation',
                'C' => 'Encounter',
                'D' => 'Procedure',
            ],
            'correct_answer' => 'C',
            'explanation' => 'Resource Encounter mendefinisikan interaksi/kunjungan pasien di faskes (rawat jalan, igd, atau rawat inap).',
            'order' => 3,
        ]);

        // 2. VIDEO LESSON
        $lVideo = Lesson::create([
            'course_id' => $course1->id,
            'section_title' => 'Bab 1: Landasan Transformasi Digital & Regulasi',
            'title' => 'Video Pembelajaran: Kerangka Arsitektur SPBE Nasional & Mandat Perpres 95/2018',
            'slug' => 'video-arsitektur-ekosistem-spbe',
            'order' => 2,
            'type' => 'video',
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'duration_minutes' => 20,
            'is_free_preview' => true,
            'content' => 'Simak pemaparan video mengenai grand design arsitektur SPBE nasional dan implementasi interoperabilitas Satu Data di Pemerintah Daerah.',
        ]);

        // 3. PDF DOCUMENT LESSON
        $lPdf = Lesson::create([
            'course_id' => $course1->id,
            'section_title' => 'Bab 1: Landasan Transformasi Digital & Regulasi',
            'title' => 'Modul PDF: Buku Pedoman Standarisasi Variabel FHIR Kemenkes RI',
            'slug' => 'modul-pdf-buku-pedoman-standar-fhir',
            'order' => 3,
            'type' => 'pdf',
            'pdf_url' => 'https://raw.githubusercontent.com/mozilla/pdf.js/ba2edeae/web/compressed.tracemonkey-pldi-09.pdf',
            'duration_minutes' => 30,
            'is_free_preview' => true,
            'content' => 'Buku pedoman resmi berformat PDF yang memuat definisi operasional variabel data pasien, terminologi ICD-10, ICD-9-CM, SNOMED-CT, dan kamus data farmasi KFA Kemenkes RI.',
        ]);

        // 4. ASSIGNMENT (TUGAS PRAKTIK)
        $lAssign = Lesson::create([
            'course_id' => $course1->id,
            'section_title' => 'Bab 2: Implementasi Teknis & Validasi Data',
            'title' => 'Tugas Praktik: Penyusunan Payload JSON Encounter & Pemetaan ICD-10',
            'slug' => 'tugas-praktik-payload-json-encounter',
            'order' => 4,
            'type' => 'assignment',
            'duration_minutes' => 45,
            'is_free_preview' => false,
            'content' => 'Tugas mandiri untuk menguji kemampuan teknis Anda dalam menyusun struktur JSON payload Resource FHIR sesuai studi kasus pasien rawat jalan.',
        ]);

        \App\Models\Assignment::create([
            'lesson_id' => $lAssign->id,
            'title' => 'Studi Kasus: Pemetaan Kunjungan Pasien Rawat Jalan (Poli Umum)',
            'instructions' => "### Petunjuk Pengerjaan Tugas:\n\n1. Unduh template berkas `encounter_template.json` pada link yang tersedia.\n2. Isikan data kunjungan fiktif dengan ketentuan:\n   - NIK Pasien: 3271012304920001 (Budi Santoso)\n   - Faskes ID Kemenkes: 1000001\n   - Diagnosa Primer: ISPA Ringan (Kode ICD-10: J06.9)\n3. Tuliskan ringkasan penjelasan atau unggah dokumen hasil pengujian payload Anda pada form pengumpulan di bawah.\n4. Fasilitator akan mereview dan memberikan feedback nilai.",
            'attachment_url' => 'https://example.com/template_encounter.json',
            'max_score' => 100,
        ]);

        // 5. POST-TEST
        $lPostTest = Lesson::create([
            'course_id' => $course1->id,
            'section_title' => 'Bab 3: Evaluasi Akhir & Kelulusan (Post-Test)',
            'title' => 'Post-Test Kelulusan: Evaluasi Komprehensif Sertifikasi RME',
            'slug' => 'post-test-kelulusan-sertifikasi-rme',
            'order' => 5,
            'type' => 'post_test',
            'duration_minutes' => 20,
            'is_free_preview' => false,
            'content' => 'Post-Test adalah syarat akhir kelulusan kursus ini. Raih nilai minimal 70 untuk menyelesaikan kursus dan membuka akses unduh Sertifikat Resmi Anda.',
        ]);

        $quizPost = \App\Models\Quiz::create([
            'lesson_id' => $lPostTest->id,
            'title' => 'Post-Test Evaluasi Kompetensi SPBE & Satu Data',
            'description' => 'Evaluasi kelulusan terdiri dari 3 soal pilihan ganda. Waktu pengerjaan 20 menit.',
            'passing_score' => 70,
            'duration_minutes' => 20,
        ]);

        \App\Models\QuizQuestion::create([
            'quiz_id' => $quizPost->id,
            'question' => 'Protokol keamanan standar yang diterapkan pada pertukaran data API antar-layanan SPBE pemerintah daerah adalah:',
            'options' => [
                'A' => 'OAuth 2.0 Client Credentials Grant',
                'B' => 'Basic Authentication Base64',
                'C' => 'API Key Header Sederhana',
                'D' => 'SAML 2.0 Web Browser SSO',
            ],
            'correct_answer' => 'A',
            'explanation' => 'Pertukaran data resmi SPBE menggunakan standar RESTful API dengan enkripsi TLS dan autentikasi token OAuth2/JWT.',
            'order' => 1,
        ]);

        \App\Models\QuizQuestion::create([
            'quiz_id' => $quizPost->id,
            'question' => 'Kamus data terminologi standar farmasi yang diwajibkan oleh Kemenkes untuk penulisan resep elektronik (Medication) adalah:',
            'options' => [
                'A' => 'KFA (Kamus Farmasi dan Alat Kesehatan)',
                'B' => 'LOINC Database Internasional',
                'C' => 'SNOMED CT Farmasi Global',
                'D' => 'ICD-9 Clinical Modification',
            ],
            'correct_answer' => 'A',
            'explanation' => 'KFA (Kamus Farmasi dan Alat Kesehatan) adalah terminologi obat resmi dari Kementerian Kesehatan RI.',
            'order' => 2,
        ]);

        \App\Models\QuizQuestion::create([
            'quiz_id' => $quizPost->id,
            'question' => 'Lingkungan pengujian yang disediakan Kemenkes untuk memverifikasi payload sebelum go-live ke server produksi disebut:',
            'options' => [
                'A' => 'Staging Cluster Internal',
                'B' => 'Sandbox Developer Environment',
                'C' => 'Localhost Docker Container',
                'D' => 'Production Beta Tester',
            ],
            'correct_answer' => 'B',
            'explanation' => 'Lingkungan Sandbox resmi disediakan Kemenkes bagi developer untuk melakukan uji coba kirim payload tanpa memengaruhi data faskes nyata.',
            'order' => 3,
        ]);

        // 4. Course 2 (AI & Productivity)
        $course2 = Course::create([
            'category_id' => $catAI->id,
            'instructor_id' => $inst2->id,
            'title' => 'Kecerdasan Buatan (AI) & Otomasi Alur Kerja untuk Produktivitas Pegawai',
            'slug' => 'ai-dan-otomasi-alur-kerja-pegawai',
            'subtitle' => 'Kuasai teknik prompt engineering modern, pembuatan draf SOP otomatis, dan integrasi workflow cerdas dalam pekerjaan harian.',
            'description' => 'Tingkatkan efisiensi kerja hingga 3x lipat dengan memanfaatkan kecerdasan buatan generatif secara etis dan aman. Kelas ini membahas teknik perumusan instruksi akurat, analisis data tabular cepat, perancangan draf laporan instansi, dan perlindungan privasi data organisasi.',
            'thumbnail' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?auto=format&fit=crop&w=800&q=80',
            'level' => 'Pemula',
            'price' => 149000,
            'rating' => 4.95,
            'students_count' => 890,
            'duration_hours' => 4,
            'certificate_available' => true,
            'is_featured' => true,
            'is_published' => true,
        ]);

        $lessonsC2 = [
            [
                'section_title' => 'Modul 1: Membangun Pola Pikir & Arsitektur AI',
                'title' => 'Dasar LLM dan Peluang Otomasi Dokumen Kantor',
                'slug' => 'dasar-llm-otomasi-dokumen-kantor',
                'order' => 1,
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'content' => 'Pengenalan prinsip dasar Large Language Models, batasan halusinasi, dan cara memanfaatkan AI sebagai asisten berpikir strategis.',
                'duration_minutes' => 20,
                'is_free_preview' => true,
            ],
            [
                'section_title' => 'Modul 1: Membangun Pola Pikir & Arsitektur AI',
                'title' => 'Formula Prompt Presisi: Role, Context, Task, Constraint',
                'slug' => 'formula-prompt-presisi-rctc',
                'order' => 2,
                'type' => 'reading',
                'video_url' => null,
                'content' => "Gunakan framework **R-C-T-C**:\n1. **Role**: Peran yang diambil model (misal: 'Bertindaklah sebagai Analis Kebijakan Senior').\n2. **Context**: Situasi latar belakang dan audiens sasaran.\n3. **Task**: Instruksi spesifik yang harus diselesaikan.\n4. **Constraint**: Batasan gaya bahasa, format tabel, atau jumlah kata.",
                'duration_minutes' => 25,
                'is_free_preview' => false,
            ],
            [
                'section_title' => 'Modul 2: Praktik Analisis & Otomasi Kerja',
                'title' => 'Ekstraksi & Rekapitulasi Data Dokumen Rumit',
                'slug' => 'ekstraksi-rekapitulasi-data-rumit',
                'order' => 3,
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'content' => 'Praktik mengubah ratusan halaman draf laporan menjadi matriks evaluasi siap presentasi dalam hitungan detik.',
                'duration_minutes' => 30,
                'is_free_preview' => false,
            ],
        ];

        foreach ($lessonsC2 as $lData) {
            $lData['course_id'] = $course2->id;
            Lesson::create($lData);
        }

        // 5. Course 3 (Cybersecurity)
        $course3 = Course::create([
            'category_id' => $catCyber->id,
            'instructor_id' => $inst2->id,
            'title' => 'Cybersecurity Awareness: Proteksi Data Pribadi & Ketahanan Siber Organisasi',
            'slug' => 'cybersecurity-awareness-proteksi-data',
            'subtitle' => 'Langkah praktis mengenali ancaman social engineering, manajemen kredensial kuat, dan kepatuhan UU Pelindungan Data Pribadi (UU PDP).',
            'description' => 'Membangun benteng pertahanan digital pertama dari faktor manusia. Pelatihan ini melatih setiap aparatur dan profesional dalam mendeteksi email phishing, mengamankan perangkat kerja, dan menerapkan protokol respon insiden kebocoran data.',
            'thumbnail' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=800&q=80',
            'level' => 'Pemula',
            'price' => 0,
            'rating' => 4.93,
            'students_count' => 1120,
            'duration_hours' => 3,
            'certificate_available' => true,
            'is_featured' => false,
            'is_published' => true,
        ]);

        Lesson::create([
            'course_id' => $course3->id,
            'section_title' => 'Modul 1: Anatomi Serangan Modern',
            'title' => 'Mengenali Phishing, Spear-Phishing & Rekayasa Sosial',
            'slug' => 'mengenali-phishing-social-engineering',
            'order' => 1,
            'type' => 'video',
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'content' => 'Mengenal taktik penipuan siber yang paling sering menyerang instansi dan teknik verifikasi alamat pengirim.',
            'duration_minutes' => 20,
            'is_free_preview' => true,
        ]);

        // 6. Course 4 (Leadership)
        $course4 = Course::create([
            'category_id' => $catLeadership->id,
            'instructor_id' => $inst3->id,
            'title' => 'Manajemen Kinerja & Kepemimpinan Adaptif Era Digital ASN',
            'slug' => 'manajemen-kinerja-kepemimpinan-adaptif-asn',
            'subtitle' => 'Strategi penetapan SKP berbasis outcome, kolaborasi lintas sektor yang lincah (agile), dan pelayanan publik berorientasi kepuasan masyarakat.',
            'description' => 'Pengembangan kepemimpinan visioner bagi pejabat struktural dan fungsional guna mendorong birokrasi yang cepat merespons kebutuhan publik di era digital.',
            'thumbnail' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=800&q=80',
            'level' => 'Lanjutan',
            'price' => 199000,
            'rating' => 4.97,
            'students_count' => 640,
            'duration_hours' => 5,
            'certificate_available' => true,
            'is_featured' => true,
            'is_published' => true,
        ]);

        Lesson::create([
            'course_id' => $course4->id,
            'section_title' => 'Modul 1: Kepemimpinan Hasil',
            'title' => 'Transformasi Mindset Birokrasi: Dari Output ke Outcome',
            'slug' => 'transformasi-mindset-birokrasi',
            'order' => 1,
            'type' => 'video',
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'content' => 'Memahami pergeseran indikator kinerja aparatur sipil negara menuju dampak nyata yang dirasakan masyarakat.',
            'duration_minutes' => 25,
            'is_free_preview' => true,
        ]);

        // 7. Enrollment for Demo Student (Course 1 - 60% progress)
        $enrollment1 = Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course1->id,
            'progress_percent' => 60,
        ]);

        // Mark first 3 lessons of Course 1 as completed
        $c1Lessons = $course1->lessons()->take(3)->get();
        foreach ($c1Lessons as $l) {
            LessonProgress::create([
                'user_id' => $student->id,
                'lesson_id' => $l->id,
                'is_completed' => true,
                'completed_at' => now()->subHours(2),
            ]);
        }

        // Enrollment 2 (Course 3 - 100% completed with Certificate)
        $enrollment2 = Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course3->id,
            'progress_percent' => 100,
            'completed_at' => now()->subDays(1),
        ]);

        Certificate::create([
            'user_id' => $student->id,
            'course_id' => $course3->id,
            'certificate_number' => 'CERT-SATU-' . date('Y') . '-00892',
            'issued_at' => now()->subDays(1),
            'verification_hash' => Str::upper(Str::random(12)),
        ]);

        // 8. Add Certificate Builder Defaults to Courses
        $course1->update([
            'cert_title' => 'SERTIFIKAT KELULUSAN & KOMPETENSI TEKNIS RME',
            'cert_institution_name' => 'KEMENTERIAN KESEHATAN DIGITAL ACADEMY',
            'cert_institution_sub' => 'Badan Kepegawaian dan Pengembangan Sumber Daya Manusia',
            'cert_signatory_name' => 'dr. Raditya Pratama, Sp.A, M.Ked',
            'cert_signatory_role' => 'Kepala BKPSDM Kabupaten Berau',
            'cert_signatory_nip' => 'NIP. 19850412 201012 1 004',
            'cert_theme_color' => 'teal',
            'cert_statement' => 'Telah dinyatakan LULUS dan KOMPETEN dalam mengimplementasikan Arsitektur Rekam Medis Elektronik berbasis Standar HL7 FHIR dan Keamanan Data Medis Nasional.',
        ]);

        $course3->update([
            'cert_title' => 'SERTIFIKAT PROFESI KEPEMIMPINAN DIGITAL ASN',
            'cert_institution_name' => 'LEMBAGA PENGEMBANGAN APARATUR NEGARA',
            'cert_institution_sub' => 'Pusat Inovasi & Transformasi Birokrasi Berdampak',
            'cert_signatory_name' => 'Sarah Wulandari, S.STP, M.P.A',
            'cert_signatory_role' => 'Direktur Pembinaan Kompetensi Manajerial',
            'cert_signatory_nip' => 'NIP. 19890318 201201 2 002',
            'cert_theme_color' => 'indigo',
            'cert_statement' => 'Telah menyelesaikan pelatihan intensif tata kelola birokrasi agile dan perancangan pelayanan publik modern.',
        ]);

        // 9. Additional Registered Students for Superadmin Management
        $studentsData = [
            ['name' => 'Dewi Anggraini, S.Tr.Kes', 'email' => 'dewi@puskesmas.go.id', 'phone' => '081234567891', 'role' => 'student', 'headline' => 'Perekam Medis RSUD dr. Soetomo'],
            ['name' => 'Rian Hidayat, S.Kom', 'email' => 'rian@dinkes.jatimprov.go.id', 'phone' => '082198765432', 'role' => 'student', 'headline' => 'Staff IT Dinas Kesehatan'],
            ['name' => 'dr. Siti Rahmawati', 'email' => 'siti.rahma@kliniksentosa.com', 'phone' => '081377889900', 'role' => 'student', 'headline' => 'Dokter Penanggung Jawab Klinik Pratama'],
            ['name' => 'Ahmad Fauzi, S.Kep', 'email' => 'fauzi@rsudbanyuwangi.id', 'phone' => '085244556677', 'role' => 'student', 'headline' => 'Kepala Ruangan Rawat Inap RSUD'],
            ['name' => 'Nadia Putri, S.Si', 'email' => 'nadia@biomedika.co.id', 'phone' => '081911223344', 'role' => 'student', 'headline' => 'Data Analyst Laboratorium Medis'],
        ];

        foreach ($studentsData as $sd) {
            $newStu = User::create([
                'name' => $sd['name'],
                'email' => $sd['email'],
                'phone' => $sd['phone'],
                'password' => Hash::make('password'),
                'role' => $sd['role'],
                'headline' => $sd['headline'],
                'bio' => 'Peserta program percepatan standarisasi digital institusi kesehatan.',
            ]);

            // Enroll each in Course 1 or Course 2
            Enrollment::create([
                'user_id' => $newStu->id,
                'course_id' => $course1->id,
                'progress_percent' => rand(15, 85),
            ]);
        }

        // 10. Homepage Sliders / Hero Banners
        \App\Models\Slider::create([
            'title' => 'Arsitektur SPBE & Integrasi Layanan Publik Berau 2026',
            'subtitle' => 'Tingkatkan akreditasi faskes Anda dengan kurikulum terstruktur langsung dari fasilitator ahli Kementerian Kesehatan.',
            'badge_text' => 'KURIKULUM NASIONAL TERBARU',
            'image_url' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=1200&q=80',
            'cta_text' => 'Daftar Kelas RME',
            'cta_link' => '/courses/arsitektur-tata-kelola-spbe-terpadu',
            'order' => 1,
            'is_active' => true,
        ]);

        \App\Models\Slider::create([
            'title' => 'Kecerdasan Buatan (AI) & Otomasi Kerja Pemerintahan',
            'subtitle' => 'Pelajari cara merancang workflow AI, integrasi data publik cerdas, dan efisiensi laporan birokrasi hingga 80%.',
            'badge_text' => 'PROGRAM AKSELERASI ASN',
            'image_url' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=1200&q=80',
            'cta_text' => 'Pelajari Modul AI',
            'cta_link' => '/courses/ai-dan-otomasi-kerja-modern',
            'order' => 2,
            'is_active' => true,
        ]);

        \App\Models\Slider::create([
            'title' => 'Sertifikasi Keamanan Data Medis & Kepatuhan UU PDP',
            'subtitle' => 'Panduan komprehensif implementasi enkripsi, proteksi data pribadi pasien, dan audit keamanan sistem informasi.',
            'badge_text' => 'SERTIFIKASI PROFESI RESMI',
            'image_url' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=1200&q=80',
            'cta_text' => 'Lihat Silabus',
            'cta_link' => '/courses/keamanan-siber-dan-kepatuhan-pdp-medis',
            'order' => 3,
            'is_active' => true,
        ]);

        // 11. Incoming Webinars & Agenda
        \App\Models\Webinar::create([
            'title' => 'Webinar Nasional: Akselerasi Kematangan SPBE & Pengelolaan Satu Data Kabupaten Berau',
            'slug' => 'webinar-akselerasi-kematangan-spbe-satu-data',
            'speaker_name' => 'dr. Raditya Pratama, Sp.A & Tim DTO Kemenkes',
            'speaker_title' => 'Fasilitator Nasional DTO Kemenkes RI',
            'speaker_avatar' => 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=200&q=80',
            'event_date' => now()->addDays(5)->toDateString(),
            'time_start' => '09:00',
            'time_end' => '12:00',
            'location_type' => 'Online Live via Zoom & Portal LMS Berau',
            'registration_link' => 'https://zoom.us/webinar/register/spbe-berau-2026',
            'cover_image' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=800&q=80',
            'quota' => 1000,
            'price' => 0,
            'status' => 'upcoming',
            'is_featured' => true,
        ]);

        \App\Models\Webinar::create([
            'title' => 'Bedah Arsitektur HL7 FHIR: Penanganan Error Code & Sandbox Testing Rumah Sakit',
            'slug' => 'bedah-arsitektur-hl7-fhir-sandbox-testing',
            'speaker_name' => 'Fajar Hidayat, M.Kom & Praktisi Lead Dev RS',
            'speaker_title' => 'Fasilitator Utama Literasi Digital ASN',
            'speaker_avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80',
            'event_date' => now()->addDays(12)->toDateString(),
            'time_start' => '13:30',
            'time_end' => '16:00',
            'location_type' => 'Online Live Interactive Workshop',
            'registration_link' => 'https://zoom.us/webinar/register/fhir-sandbox-workshop',
            'cover_image' => 'https://images.unsplash.com/photo-1587614382346-4ec70e388b28?auto=format&fit=crop&w=800&q=80',
            'quota' => 500,
            'price' => 0,
            'status' => 'upcoming',
            'is_featured' => true,
        ]);

        \App\Models\Webinar::create([
            'title' => 'Sosialisasi Audit Kepatuhan UU Perlindungan Data Pribadi (PDP) di Sektor Pelayanan Kesehatan',
            'slug' => 'audit-kepatuhan-uu-pdp-kesehatan',
            'speaker_name' => 'Dr. Hendra Gunawan, S.H., M.H.',
            'speaker_title' => 'Pakar Hukum Siber & Konsultan Kepatuhan PDP',
            'speaker_avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=200&q=80',
            'event_date' => now()->addDays(19)->toDateString(),
            'time_start' => '09:00',
            'time_end' => '11:30',
            'location_type' => 'Hybrid (Live Streaming & Gedung Diklat)',
            'registration_link' => 'https://zoom.us/webinar/register/uu-pdp-kesehatan',
            'cover_image' => 'https://images.unsplash.com/photo-1450133064473-71024230f91b?auto=format&fit=crop&w=800&q=80',
            'quota' => 300,
            'price' => 0,
            'status' => 'upcoming',
            'is_featured' => true,
        ]);

        // 12. Homepage Promotional / Announcement Popup
        \App\Models\Popup::create([
            'title' => 'Pendaftaran Gelombang Khusus Pelatihan Akreditasi RME 2026 Telah Dibuka!',
            'badge_text' => 'PENGUMUMAN RESMI NASIONAL',
            'content' => 'Aparatur Sipil Negara (ASN) dan perangkat daerah dapat mengikuti pelatihan mandiri ini guna peningkatan kompetensi dan pemenuhan target Jam Pelajaran (JP).',
            'image_url' => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=800&q=80',
            'button_text' => 'Lihat Informasi & Daftar Sekarang',
            'button_link' => '/courses/arsitektur-tata-kelola-spbe-terpadu',
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(30),
        ]);
    }
}
