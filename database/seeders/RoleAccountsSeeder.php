<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\Webinar;
use App\Models\OpdActivityRequest;
use App\Models\IdpPlan;
use Illuminate\Support\Facades\Hash;

class RoleAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPassword = Hash::make('password');

        // 1. Pimpinan (Sekda / Bupati / Kepala Daerah)
        $pimpinan = User::updateOrCreate(
            ['email' => 'pimpinan@berau.go.id'],
            [
                'name' => 'Drs. H. Muhammad Gazali, S.IP, M.Si',
                'password' => $defaultPassword,
                'role' => 'pimpinan',
                'nip' => '196805121994031005',
                'nik' => '6403011205680001',
                'jabatan' => 'Sekretaris Daerah Kabupaten Berau',
                'pangkat_golongan' => 'Pembina Utama Madya (IV/d)',
                'opd_name' => 'Sekretariat Daerah',
                'phone' => '08115500112',
                'headline' => 'Sekretaris Daerah Kabupaten Berau',
                'bio' => 'Pemimpin eksekutif pengarah kebijakan Corporate University Kabupaten Berau.',
                'is_active' => true,
                'jp_target_per_year' => 20,
            ]
        );

        // 2. Admin OPD (Diskominfo)
        $adminOpd = User::updateOrCreate(
            ['email' => 'admin.opd@berau.go.id'],
            [
                'name' => 'Hendra Wijaya, S.Kom',
                'password' => $defaultPassword,
                'role' => 'admin_opd',
                'nip' => '198507202010011012',
                'nik' => '6403012007850002',
                'jabatan' => 'Pranata Komputer Ahli Muda',
                'pangkat_golongan' => 'Penata Tk. I (III/d)',
                'opd_name' => 'Dinas Komunikasi dan Informatika',
                'phone' => '081254332211',
                'headline' => 'Admin Pengelola Corpu Diskominfo Berau',
                'bio' => 'Koordinator pelatihan dan usulan program diklat Dinas Komunikasi dan Informatika.',
                'is_active' => true,
                'jp_target_per_year' => 20,
            ]
        );

        // 3. Narasumber / Instruktur
        $narasumber = User::updateOrCreate(
            ['email' => 'narasumber@berau.go.id'],
            [
                'name' => 'Fajar Hidayat, M.Kom',
                'password' => $defaultPassword,
                'role' => 'narasumber',
                'nip' => '197903152005011008',
                'nik' => '6403011503790003',
                'jabatan' => 'Widyaiswara Ahli Madya',
                'pangkat_golongan' => 'Pembina (IV/a)',
                'opd_name' => 'Badan Kepegawaian dan Pengembangan SDM',
                'phone' => '081399887766',
                'headline' => 'Widyaiswara Spesialisasi Transformasi Digital & Keamanan SPBE',
                'bio' => 'Fasilitator Corpu Berau untuk kompetensi digital aparatur dan tata kelola sistem pemerintahan berbasis elektronik.',
                'trainer_status' => 'approved',
                'is_active' => true,
                'jp_target_per_year' => 20,
            ]
        );

        // 4. Reviewer Talenta / Admin Talent (BKPSDM)
        $reviewerTalent = User::updateOrCreate(
            ['email' => 'reviewer.talent@berau.go.id'],
            [
                'name' => 'Sarah Wulandari, S.STP, M.P.A',
                'password' => $defaultPassword,
                'role' => 'reviewer_talent',
                'nip' => '198811052011012003',
                'nik' => '6403010511880004',
                'jabatan' => 'Analis SDM Aparatur Ahli Muda',
                'pangkat_golongan' => 'Penata Tk. I (III/d)',
                'opd_name' => 'Badan Kepegawaian dan Pengembangan SDM',
                'phone' => '082144332211',
                'headline' => 'Sub Koordinator Pengembangan Karier & Manajemen Talenta ASN',
                'bio' => 'Pengelola sistem Individual Development Plan (IDP) dan pemetaan talenta ASN Berau.',
                'is_active' => true,
                'jp_target_per_year' => 20,
            ]
        );

        // 5. Hubungkan beberapa kursus ke narasumber
        $courses = Course::take(2)->get();
        foreach ($courses as $c) {
            $c->update(['instructor_id' => $narasumber->id]);
        }

        // Hubungkan webinar ke narasumber
        $webinar = Webinar::first();
        if ($webinar) {
            $webinar->update([
                'speaker_name' => $narasumber->name,
                'speaker_title' => $narasumber->jabatan,
                'speaker_institution' => 'BKPSDM Kabupaten Berau',
            ]);
        }

        // 6. Buat contoh Usulan Kegiatan OPD (Diskominfo) jika belum ada
        if (OpdActivityRequest::where('opd_name', 'Dinas Komunikasi dan Informatika')->count() === 0) {
            OpdActivityRequest::create([
                'title' => 'Bimtek Penguatan Keamanan Siber dan Tata Kelola CSIRT OPD',
                'description' => 'Bimbingan teknis peningkatan kapabilitas penanganan insiden siber pada perangkat daerah di lingkungan Pemkab Berau.',
                'opd_name' => 'Dinas Komunikasi dan Informatika',
                'submitted_by' => $adminOpd->id,
                'learning_method' => 'in_class',
                'course_type' => 'in_class',
                'jp' => 16,
                'target_audience' => 'Pengelola IT dan Admin Website Seluruh OPD Berau',
                'estimated_participants' => 45,
                'proposed_start_date' => now()->addDays(14)->toDateString(),
                'proposed_end_date' => now()->addDays(15)->toDateString(),
                'proposed_location' => 'Aula Sasana Praja Pemkab Berau',
                'speaker_name' => 'BSSN & Tim Keamanan Informasi Diskominfo',
                'objectives' => 'Meningkatkan kesiapsiagaan aparatur dalam mitigasi kebocoran data dan insiden keamanan siber.',
                'status' => 'pending',
                'supporting_notes' => 'Telah disetujui internal Kepala Dinas Kominfo, mohon persetujuan BKPSDM untuk penetapan kalender Corpu.',
                'budget_source' => 'APBD Perubahan Diskominfo TA 2026',
            ]);

            OpdActivityRequest::create([
                'title' => 'Workshop Penulisan Berita dan Komunikasi Publik Pemda Berau',
                'description' => 'Pelatihan penulisan konten informatif, press release, dan manajemen komunikasi krisis media sosial OPD.',
                'opd_name' => 'Dinas Komunikasi dan Informatika',
                'submitted_by' => $adminOpd->id,
                'learning_method' => 'hybrid',
                'course_type' => 'hybrid',
                'jp' => 12,
                'target_audience' => 'Pranata Humas dan Pengelola Media Sosial OPD',
                'estimated_participants' => 60,
                'proposed_start_date' => now()->subDays(10)->toDateString(),
                'proposed_end_date' => now()->subDays(9)->toDateString(),
                'proposed_location' => 'Ruang Rapat Diskominfo & Zoom Meeting',
                'speaker_name' => 'Redaktur Berau Post & Praktisi PR Digital',
                'objectives' => 'Standarisasi narasi tunggal program prioritas pembangunan Kabupaten Berau.',
                'status' => 'approved',
                'review_notes' => 'Disetujui untuk dilaksanakan secara hybrid. Kuota 60 peserta dialokasikan.',
                'reviewed_by' => 1,
                'reviewed_at' => now()->subDays(15),
                'budget_source' => 'DPA Diskominfo 2026',
            ]);
        }

        // 7. Buat contoh IDP Plan jika belum ada
        if (IdpPlan::count() === 0) {
            IdpPlan::create([
                'nip' => '198507202010011012',
                'nama_asn' => 'Hendra Wijaya, S.Kom',
                'tahun' => 2026,
                'judul_materi' => 'Pelatihan Keamanan Jaringan dan Analisis Kerentanan Website',
                'uploaded_by' => $reviewerTalent->id,
                'batch_id' => 'IDP-2026-BATCH-01',
            ]);

            IdpPlan::create([
                'nip' => '197903152005011008',
                'nama_asn' => 'Fajar Hidayat, M.Kom',
                'tahun' => 2026,
                'judul_materi' => 'Sertifikasi Asesor Kompetensi Bidang Komunikasi dan Informatika',
                'uploaded_by' => $reviewerTalent->id,
                'batch_id' => 'IDP-2026-BATCH-01',
            ]);

            IdpPlan::create([
                'nip' => '198805142014021003',
                'nama_asn' => 'Budi Santoso, S.Kom',
                'tahun' => 2026,
                'judul_materi' => 'Kecerdasan Buatan (AI) & Otomasi Alur Kerja untuk Produktivitas Pegawai',
                'uploaded_by' => $reviewerTalent->id,
                'batch_id' => 'IDP-2026-BERAU-01',
            ]);

            IdpPlan::create([
                'nip' => '198805142014021003',
                'nama_asn' => 'Budi Santoso, S.Kom',
                'tahun' => 2026,
                'judul_materi' => 'Manajemen Kinerja & Kepemimpinan Adaptif Era Digital ASN',
                'uploaded_by' => $reviewerTalent->id,
                'batch_id' => 'IDP-2026-BERAU-01',
            ]);

            IdpPlan::create([
                'nip' => '198805142014021003',
                'nama_asn' => 'Budi Santoso, S.Kom',
                'tahun' => 2027,
                'judul_materi' => 'Pelatihan Hybrid: Manajemen Risiko & Pengendalian Intern Pemerintah (SPIP Terintegrasi)',
                'uploaded_by' => $reviewerTalent->id,
                'batch_id' => 'IDP-2027-BERAU-01',
            ]);

            IdpPlan::create([
                'nip' => '197501012000031001',
                'nama_asn' => 'Administrator LMS',
                'tahun' => 2026,
                'judul_materi' => 'Kamus Kompetensi ASN: Panduan Lengkap Standar Kompetensi Aparatur Sipil Negara',
                'uploaded_by' => $reviewerTalent->id,
                'batch_id' => 'IDP-2026-BERAU-01',
            ]);

            IdpPlan::create([
                'nip' => '197501012000031001',
                'nama_asn' => 'Administrator LMS',
                'tahun' => 2026,
                'judul_materi' => 'Kecerdasan Buatan (AI) & Otomasi Alur Kerja untuk Produktivitas Pegawai',
                'uploaded_by' => $reviewerTalent->id,
                'batch_id' => 'IDP-2026-BERAU-01',
            ]);
        }
    }
}
