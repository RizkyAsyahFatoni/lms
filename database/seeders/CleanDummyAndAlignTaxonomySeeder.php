<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Slider;
use App\Models\Webinar;
use Illuminate\Support\Facades\DB;

class CleanDummyAndAlignTaxonomySeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();
        try {
            // =========================================================================
            // 1. UPDATE ALL 8 COURSES TO ALIGN WITH OFFICIAL EXCEL TAXONOMY & BERAU CLIENT
            // =========================================================================

            // Course 1: SPBE Terpadu Berau
            $c1 = Course::find(1);
            if ($c1) {
                $c1->update([
                    'title' => 'Arsitektur & Tata Kelola Sistem Pemerintahan Berbasis Elektronik (SPBE) Terpadu',
                    'slug' => 'arsitektur-tata-kelola-spbe-terpadu',
                    'subtitle' => 'Panduan implementasi arsitektur SPBE, interoperabilitas data antar-OPD, dan integrasi portal layanan publik Pemerintah Kabupaten Berau.',
                    'description' => 'Pelatihan komprehensif bagi aparatur sipil negara dan pengelola TIK di lingkungan Pemerintah Kabupaten Berau untuk memahami regulasi Perpres No. 95 Tahun 2018 tentang SPBE. Modul ini membahas kerangka arsitektur SPBE daerah, penyusunan peta rencana TIK OPD, standarisasi interoperabilitas Satu Data Indonesia (SDI), manajemen risiko keamanan informasi, hingga tata cara penilaian mandiri indeks kematangan SPBE Kabupaten Berau.',
                    'category_id' => 30, // Kompetensi Teknis Digital & SPBE
                    'sub_category_id' => 32, // Tata Kelola Data & Regulasi Digital
                    'sub_category' => 'Tata Kelola Data & Regulasi Digital',
                    'tags' => 'Satu Data Indonesia (SDI), Metadata Statistik, Perlindungan Data Pribadi (UU PDP), Tata Kelola Big Data, Interoperabilitas Data, Arsitektur SPBE',
                    'thumbnail' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=800&q=80',
                    'cert_title' => 'SERTIFIKAT KOMPETENSI RESMI',
                    'cert_institution_name' => 'BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA',
                    'cert_institution_sub' => 'Pemerintah Kabupaten Berau',
                    'cert_signatory_name' => 'Dra. Hj. Sri Eka Yuliana, M.Si',
                    'cert_signatory_role' => 'Kepala BKPSDM Kabupaten Berau',
                    'cert_signatory_nip' => '19720514 199803 2 004',
                ]);

                // Update Course 1 Lessons
                Lesson::where('course_id', 1)->where('order', 1)->update([
                    'title' => 'Pre-Test: Pemetaan Kompetensi Awal Tata Kelola SPBE & Transformasi Digital',
                    'content' => 'Uji pemahaman dasar regulasi SPBE dan kesiapan transformasi digital perangkat daerah.',
                ]);
                Lesson::where('course_id', 1)->where('order', 2)->update([
                    'title' => 'Video Pembelajaran: Kerangka Arsitektur SPBE Nasional & Mandat Perpres 95/2018',
                    'content' => 'Paparan komprehensif mengenai 6 domain SPBE, domain tata kelola, dan domain layanan publik terpadu.',
                ]);
                Lesson::where('course_id', 1)->where('order', 3)->update([
                    'title' => 'Modul PDF: Buku Pedoman Interoperabilitas Satu Data & Integrasi Layanan Publik',
                    'content' => 'Panduan teknis penyelarasan metadata statistik sektoral dan API gateway pertukaran data antar-OPD.',
                ]);
                Lesson::where('course_id', 1)->where('order', 4)->update([
                    'title' => 'Tugas Praktik: Penyusunan Peta Rencana TIK OPD & Identifikasi Kebutuhan Sistem',
                    'content' => 'Susun ringkasan analisis kebutuhan sistem informasi dan pemetaan proses bisnis pada instansi/OPD Anda.',
                ]);
                Lesson::where('course_id', 1)->where('order', 5)->update([
                    'title' => 'Post-Test Kelulusan: Evaluasi Komprehensif Sertifikasi Tata Kelola SPBE',
                    'content' => 'Uji akhir evaluasi pemahaman arsitektur SPBE untuk verifikasi kelulusan dan penerbitan sertifikat.',
                ]);
            }

            // Course 2: AI & Otomasi Kerja Pegawai
            $c2 = Course::find(2);
            if ($c2) {
                $c2->update([
                    'category_id' => 30, // Kompetensi Teknis Digital & SPBE
                    'sub_category_id' => 33, // Literasi Digital Praktis
                    'sub_category' => 'Literasi Digital Praktis',
                    'tags' => 'Etika Komunikasi ASN, Pemanfaatan AI Generatif / ChatGPT ASN, Pengelolaan Cloud Storage, Desain Paparan Visual, Kolaborasi Digital',
                    'thumbnail' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?auto=format&fit=crop&w=800&q=80',
                ]);
            }

            // Course 3: Cybersecurity Awareness
            $c3 = Course::find(3);
            if ($c3) {
                $c3->update([
                    'subtitle' => 'Langkah praktis mengenali ancaman social engineering, manajemen kredensial kuat, dan kepatuhan UU Pelindungan Data Pribadi (UU PDP) di lingkungan Pemkab Berau.',
                    'category_id' => 30, // Kompetensi Teknis Digital & SPBE
                    'sub_category_id' => 31, // Keamanan Informasi & Siber
                    'sub_category' => 'Keamanan Informasi & Siber',
                    'tags' => 'Sertifikasi Keamanan, ISO 27001, CSIRT Pemda, Penanganan Insiden, Vulnerability Assessment, Kriptografi & TTE, Perlindungan Data Pribadi',
                    'thumbnail' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=800&q=80',
                ]);
            }

            // Course 4: Manajemen Kinerja & Kepemimpinan Adaptif ASN
            $c4 = Course::find(4);
            if ($c4) {
                $c4->update([
                    'category_id' => 11, // Kompetensi Manajerial
                    'sub_category_id' => 18, // Mengelola Perubahan
                    'sub_category' => 'Mengelola Perubahan',
                    'tags' => 'Target SMART, Efisiensi Anggaran, Manajemen Waktu Kerja, Monitoring Evaluasi, Kepemimpinan Kinerja, Inovasi Birokrasi',
                    'thumbnail' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=800&q=80',
                ]);
            }

            // Course 5: Kamus Kompetensi ASN
            $c5 = Course::find(5);
            if ($c5) {
                $c5->update([
                    'category_id' => 24, // Kompetensi Teknis Umum
                    'sub_category_id' => 29, // Kepegawaian & Tata Laksana
                    'sub_category' => 'Kepegawaian & Tata Laksana',
                    'tags' => 'Analisis Jabatan (Anjab) & ABK, Penyusunan SKP & E-Kinerja BKN, Manajemen Talenta ASN, Standar Operasional Prosedur (SOP), Evaluasi Kelembagaan',
                    'thumbnail' => '/storage/thumbnails/kamus-kompetensi-asn.jpg',
                ]);
            }

            // Course 6: Bimtek Klasikal SIPD RI
            $c6 = Course::find(6);
            if ($c6) {
                $c6->update([
                    'category_id' => 24, // Kompetensi Teknis Umum
                    'sub_category_id' => 26, // Keuangan & Aset Daerah
                    'sub_category' => 'Keuangan & Aset Daerah',
                    'tags' => 'SIPD-RI Penatausahaan, Pengelolaan BMD (Barang Milik Daerah), Akuntansi Pemerintah Daerah, Standar Harga Satuan, Bendahara Pengeluaran',
                    'thumbnail' => 'https://images.unsplash.com/photo-1544531586-fde5298cdd40?auto=format&fit=crop&w=800&q=80',
                ]);
            }

            // Course 7: Workshop Standarisasi Mutu Pelayanan Publik (Replaces healthcare FKTP)
            $c7 = Course::find(7);
            if ($c7) {
                $c7->update([
                    'title' => 'Workshop Tatap Muka: Standarisasi Mutu Pelayanan Publik & Manajemen Pengaduan Terpadu',
                    'slug' => 'workshop-standarisasi-mutu-pelayanan-publik',
                    'subtitle' => 'Peningkatan standar pelayanan prima, pengelolaan aduan SP4N-LAPOR!, dan survei kepuasan masyarakat aparatur Pemerintah Kabupaten Berau.',
                    'description' => 'Workshop aplikatif tatap muka bagi seluruh petugas front office, administrator layanan, dan perangkat daerah di lingkungan Pemkab Berau untuk mengimplementasikan Undang-Undang No. 25 Tahun 2009 tentang Pelayanan Publik. Pembelajaran mencakup teknik hospitality aparatur, penanganan komplain cepat tanggap, digitalisasi kanal pengaduan SP4N-LAPOR!, serta tata cara penyusunan Maklumat Pelayanan dan SOP teknis.',
                    'category_id' => 11, // Kompetensi Manajerial
                    'sub_category_id' => 16, // Pelayanan Publik
                    'sub_category' => 'Pelayanan Publik',
                    'tags' => 'Standar Pelayanan Publik, SP4N LAPOR, Pelayanan Prima, Kepuasan Masyarakat, Survei Kepuasan Masyarakat (SKM)',
                    'thumbnail' => 'https://images.unsplash.com/photo-1521737711867-e3b97375f902?auto=format&fit=crop&w=800&q=80',
                ]);
            }

            // Course 8: Pelatihan Hybrid SPIP Terintegrasi
            $c8 = Course::find(8);
            if ($c8) {
                $c8->update([
                    'category_id' => 24, // Kompetensi Teknis Umum
                    'sub_category_id' => 27, // Perencanaan & Akuntabilitas Kinerja
                    'sub_category' => 'Perencanaan & Akuntabilitas Kinerja',
                    'tags' => 'Penyusunan Renstra & Renja, SAKIP & LAKIP, Indikator Kinerja Utama (IKU), Cascading Kinerja, Evaluasi Kebijakan Daerah, Mitigasi Risiko',
                    'thumbnail' => 'https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&w=800&q=80',
                ]);
            }

            // =========================================================================
            // 2. DELETE OLD DUMMY CATEGORIES (IDs 1, 2, 3, 4)
            // =========================================================================
            $dummyCategoryIds = [1, 2, 3, 4];
            Category::whereIn('id', $dummyCategoryIds)->delete();

            // =========================================================================
            // 3. CLEAN UP SLIDERS & ALIGN WITH PEMKAB BERAU
            // =========================================================================
            $s1 = Slider::find(1);
            if ($s1) {
                $s1->update([
                    'course_id' => 1,
                    'title' => 'Arsitektur SPBE & Integrasi Layanan Publik Berau',
                    'subtitle' => 'Transformasi digital terpadu menuju tata kelola pemerintahan yang efektif, transparan, dan akuntabel di Kabupaten Berau.',
                    'badge_text' => 'PROGRAM UNGGULAN SPBE',
                    'image_url' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=1200&q=80',
                ]);
            }

            $s3 = Slider::find(3);
            if ($s3) {
                $s3->update([
                    'course_id' => 3,
                    'title' => 'Cybersecurity Awareness & Proteksi Data ASN',
                    'subtitle' => 'Tingkatkan ketahanan sistem informasi dan kepatuhan perlindungan data pribadi di seluruh perangkat daerah.',
                    'badge_text' => 'KEAMANAN INFORMASI DAERAH',
                    'image_url' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=1200&q=80',
                ]);
            }

            // Remove test slider 16 if exists
            Slider::where('id', 16)->delete();

            // =========================================================================
            // 4. CLEAN UP WEBINARS (Remove duplicate FHIR testing entries 4-13)
            // =========================================================================
            Webinar::whereIn('id', [4, 5, 6, 7, 8, 9, 10, 11, 12, 13])->delete();

            // Update remaining webinars to match Pemkab Berau
            $w1 = Webinar::find(1);
            if ($w1) {
                $w1->update([
                    'title' => 'Webinar Nasional: Akselerasi Kematangan SPBE & Pengelolaan Satu Data Kabupaten Berau',
                    'description' => 'Pendalaman teknis penyusunan peta rencana SPBE, integrasi interoperabilitas portal layanan publik daerah, dan standarisasi metadata Satu Data Indonesia bersama narasumber Kementerian PAN-RB & Diskominfo.',
                    'speaker_institution' => 'Kementerian PAN-RB / Diskominfo',
                    'cover_image' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=800&q=80',
                    'course_id' => 1,
                ]);
            }

            $w2 = Webinar::find(2);
            if ($w2) {
                $w2->update([
                    'title' => 'Lokakarya Digital: Pemanfaatan Artificial Intelligence (AI) untuk Efisiensi Administrasi OPD',
                    'description' => 'Strategi praktis pemanfaatan kecerdasan buatan generatif dalam menyusun draf naskah dinas, riset kebijakan daerah, dan otomatisasi alur kerja harian aparatur sipil negara.',
                    'speaker_institution' => 'Pusat Inovasi & Literasi Digital ASN',
                    'cover_image' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?auto=format&fit=crop&w=800&q=80',
                    'course_id' => 2,
                ]);
            }

            $w3 = Webinar::find(3);
            if ($w3) {
                $w3->update([
                    'title' => 'Sosialisasi Kepatuhan UU Perlindungan Data Pribadi (UU PDP) & Mitigasi Risiko Siber Pemda',
                    'description' => 'Kupas tuntas tata kelola data sensitif masyarakat, hak subjek data, pembentukan tim tanggap insiden siber (CSIRT), dan langkah kepatuhan UU PDP di seluruh perangkat daerah.',
                    'speaker_institution' => 'Badan Siber dan Sandi Negara (BSSN)',
                    'cover_image' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=800&q=80',
                    'course_id' => 3,
                ]);
            }

            DB::commit();
            echo "SUCCESS: Dummy categories deleted, all courses aligned with new taxonomy and high-res thumbnails!\n";
        } catch (\Exception $e) {
            DB::rollBack();
            echo "ERROR: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}
