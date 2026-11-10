<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\ArticleAttachment;
use App\Models\User;

class ArticleKnowledgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superadmin = User::where('role', 'super_admin')->first() ?: User::first();
        $adminId = $superadmin ? $superadmin->id : null;

        // 1. Regulasi: Peraturan Bupati Berau tentang Corpu
        $art1 = Article::updateOrCreate(
            ['slug' => 'peraturan-bupati-berau-nomor-24-tahun-2024-tata-kelola-corporate-university'],
            [
                'user_id'      => $adminId,
                'title'        => 'Peraturan Bupati Berau No. 24 Tahun 2024 tentang Tata Kelola Corporate University & Pengembangan Kompetensi ASN',
                'category'     => 'regulasi',
                'opd_name'     => 'BKPSDM Kabupaten Berau',
                'author_name'  => 'Bagian Hukum & BKPSDM Berau',
                'excerpt'      => 'Regulasi landasan hukum penerapan Berau Corporate University, kewajiban pemenuhan 20 Jam Pelajaran (JP) per tahun bagi setiap ASN, dan mekanisme pengajuan pelatihan antar OPD.',
                'content'      => "Pemerintah Kabupaten Berau secara resmi telah menerbitkan Peraturan Bupati (Perbup) Nomor 24 Tahun 2024 tentang Tata Kelola Berau Corporate University dan Pengembangan Kompetensi Terintegrasi bagi Pegawai Aparatur Sipil Negara.\n\nPeraturan ini menetapkan arah kebijakan strategis dalam mempercepat transformasi kompetensi digital ASN di lingkungan Pemerintah Kabupaten Berau.\n\nPOKOK-POKOK PENGATURAN DALAM PERBUP:\n1. Kewajiban Pengembangan Kompetensi Minimal 20 Jam Pelajaran (JP) per tahun kalender untuk setiap PNS dan PPPK.\n2. Kedudukan BKPSDM sebagai Pengelola Utama (Super Administrator & Akreditasi Internal Diklat).\n3. Kedudukan OPD sebagai Learning Provider yang dapat menyelenggarakan workshop teknis setelah diverifikasi BKPSDM.\n4. Integrasi Learning Record ke dalam Sistem Manajemen Talenta ASN Nasional dan SIASN BKN.\n5. Tata cara pengakuan Jam Pelajaran mandiri melalui pembelajaran digital (e-Learning, Webinar, dan Pelatihan Eksternal).",
                'is_published' => true,
                'views_count'  => 142,
                'published_at' => now()->subDays(15),
            ]
        );

        ArticleAttachment::updateOrCreate(
            ['article_id' => $art1->id, 'title' => 'Salinan Naskah Perbup Berau No 24 Tahun 2024 (PDF)'],
            [
                'attachment_type' => 'regulasi',
                'file_name'       => 'Perbup_Berau_No_24_Tahun_2024_Corpu.pdf',
                'file_size'       => 2840120, // ~2.8MB
                'file_extension'  => 'pdf',
                'external_url'    => null,
                'downloads_count' => 67,
            ]
        );

        ArticleAttachment::updateOrCreate(
            ['article_id' => $art1->id, 'title' => 'Matriks Ringkasan Kewajiban 20 JP ASN per OPD'],
            [
                'attachment_type' => 'dokumen',
                'file_name'       => 'Matriks_Kewajiban_20_JP_ASN.pdf',
                'file_size'       => 1150400,
                'file_extension'  => 'pdf',
                'external_url'    => null,
                'downloads_count' => 34,
            ]
        );

        // 2. Panduan & SOP: Petunjuk Pengajuan Pelatihan Mandiri & Klaim JP Eksternal
        $art2 = Article::updateOrCreate(
            ['slug' => 'panduan-teknis-sop-pengajuan-klaim-jp-eksternal-dan-pembelajaran-mandiri'],
            [
                'user_id'      => $adminId,
                'title'        => 'Panduan Teknis (SOP) Pengajuan Klaim Jam Pelajaran (JP) Eksternal dan Pelatihan Mandiri',
                'category'     => 'panduan',
                'opd_name'     => 'Bidang Pengembangan SDM BKPSDM Berau',
                'author_name'  => 'Tim Verifikator Diklat BKPSDM',
                'excerpt'      => 'Petunjuk operasional standar mengenai alur unggah bukti sertifikat pelatihan luar, surat tugas diklat, verifikasi admin BKPSDM, dan sinkronisasi otomatis ke Learning Record ASN.',
                'content'      => "Bagi seluruh pegawai ASN di lingkungan Pemerintah Kabupaten Berau yang telah menyelesaikan kegiatan bimbingan teknis, workshop, seminar, atau pelatihan mandiri di luar platform resmi LMS Berau, dapat mengajukan rekognisi Jam Pelajaran melalui menu 'JP Eksternal'.\n\nALUR PENGAJUAN:\n1. Masuk ke akun LMS ASN Berau menggunakan NIP dan password.\n2. Pilih menu JP Eksternal di bilah navigasi profil.\n3. Isi formulir judul pelatihan, nama lembaga penyelenggara resmi, tanggal pelaksanaan, serta jumlah jam pelajaran (JP).\n4. Lampirkan dokumen digital: e-Sertifikat dan Surat Perintah Tugas (SPT) dari pimpinan OPD.\n5. Kirim pengajuan untuk diperiksa Tim Verifikator BKPSDM dalam waktu 1x24 jam kerja.\n6. Setelah disetujui (Approved), akumulasi JP akan langsung tercatat di Dashboard Portofolio Pembelajaran ASN.",
                'is_published' => true,
                'views_count'  => 285,
                'published_at' => now()->subDays(10),
            ]
        );

        ArticleAttachment::updateOrCreate(
            ['article_id' => $art2->id, 'title' => 'Buku Petunjuk SOP Pengajuan JP Eksternal (PDF)'],
            [
                'attachment_type' => 'panduan',
                'file_name'       => 'SOP_Pengajuan_JP_Eksternal_ASN_Berau.pdf',
                'file_size'       => 1850300,
                'file_extension'  => 'pdf',
                'external_url'    => null,
                'downloads_count' => 112,
            ]
        );

        // 3. Materi Presentasi: Paparan Sosialisasi Manajemen Talenta & Merit Sistem 2026
        $art3 = Article::updateOrCreate(
            ['slug' => 'materi-paparan-sosialisasi-manajemen-talenta-dan-sistem-merit-asn-kabupaten-berau'],
            [
                'user_id'      => $adminId,
                'title'        => 'Materi Paparan Sosialisasi Manajemen Talenta dan Penerapan Sistem Merit ASN Berau 2026',
                'category'     => 'presentasi',
                'opd_name'     => 'BKPSDM Kabupaten Berau',
                'author_name'  => 'Narasumber BKN & BKPSDM',
                'excerpt'      => 'Bahan presentasi narasumber sosialisasi manajemen talenta, matriks 9 kotak (nine-box grid), pemetaan gap kompetensi, dan rencana pengembangan individu (IDP).',
                'content'      => "Materi presentasi komprehensif ini disampaikan dalam rangkaian Workshop Penguatan Sistem Merit bagi Pejabat Pimpinan Tinggi Pratama, Administrator, dan Pengawas di Lingkungan Pemkab Berau.\n\nCakupan materi meliputi:\n- Kebijakan UU No. 20 Tahun 2023 tentang ASN dan Manajemen Kinerja Berbasis Bukti.\n- Implementasi 9-Box Matrix dalam Talent Pool Kabupaten Berau.\n- Keterkaitan antara akumulasi JP di LMS dengan kelayakan promosi jabatan serta seleksi terbuka.\n- Strategi penyusunan Individual Development Plan (IDP) 5 tahunan bagi tiap ASN.",
                'is_published' => true,
                'views_count'  => 318,
                'published_at' => now()->subDays(7),
            ]
        );

        ArticleAttachment::updateOrCreate(
            ['article_id' => $art3->id, 'title' => 'Slide Paparan Sosialisasi Sistem Merit Berau 2026 (PPTX)'],
            [
                'attachment_type' => 'presentasi',
                'file_name'       => 'Paparan_Manajemen_Talenta_Berau_2026.pptx',
                'file_size'       => 5630000, // 5.6MB
                'file_extension'  => 'pptx',
                'external_url'    => null,
                'downloads_count' => 154,
            ]
        );

        ArticleAttachment::updateOrCreate(
            ['article_id' => $art3->id, 'title' => 'Versi Ringkas Paparan Naskah Format Handout (PDF)'],
            [
                'attachment_type' => 'dokumen',
                'file_name'       => 'Handout_Paparan_Talenta_ASN.pdf',
                'file_size'       => 2120000,
                'file_extension'  => 'pdf',
                'external_url'    => null,
                'downloads_count' => 89,
            ]
        );

        // 4. Best Practice: Inovasi SPBE & Pelayanan Terpadu
        $art4 = Article::updateOrCreate(
            ['slug' => 'best-practice-inovasi-tata-kelola-spbe-dan-pelayanan-publik-digital-kabupaten-berau'],
            [
                'user_id'      => $adminId,
                'title'        => 'Best Practice Inovasi Tata Kelola SPBE & Transformasi Pelayanan Publik Digital Daerah',
                'category'     => 'best_practice',
                'opd_name'     => 'Dinas Komunikasi dan Informatika Kab. Berau',
                'author_name'  => 'Tim SPBE Diskominfo Berau',
                'excerpt'      => 'Dokumentasi inovasi dan pembelajaran praktik baik penerapan arsitektur SPBE, integrasi data sektoral, serta standar keamanan siber informasi daerah.',
                'content'      => "Studi kasus dan dokumentasi praktik baik (Best Practice) integrasi layanan Sistem Pemerintahan Berbasis Elektronik (SPBE) Kabupaten Berau.\n\nModul ini menyajikan langkah konkret optimalisasi interkoneksi sistem antar perangkat daerah, standarisasi antarmuka pemrograman aplikasi (API), serta perlindungan data pribadi ASN sesuai regulasi SPBE nasional.",
                'is_published' => true,
                'views_count'  => 194,
                'published_at' => now()->subDays(3),
            ]
        );

        ArticleAttachment::updateOrCreate(
            ['article_id' => $art4->id, 'title' => 'Modul Dokumentasi Inovasi SPBE Berau (PDF)'],
            [
                'attachment_type' => 'modul',
                'file_name'       => 'Modul_Inovasi_SPBE_Berau.pdf',
                'file_size'       => 3420000,
                'file_extension'  => 'pdf',
                'external_url'    => null,
                'downloads_count' => 45,
            ]
        );
    }
}
