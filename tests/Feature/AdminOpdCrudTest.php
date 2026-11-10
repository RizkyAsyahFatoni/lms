<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\OpdActivityRequest;
use App\Models\ProfileChangeRequest;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AdminOpdCrudTest extends TestCase
{
    protected $adminOpd;
    protected $superAdmin;
    protected $pegawaiOpd;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Dapatkan / Buat Admin OPD
        $this->adminOpd = User::where('role', 'admin_opd')->first();
        if (!$this->adminOpd) {
            $this->adminOpd = User::create([
                'name' => 'Hendra Wijaya, S.Kom',
                'email' => 'admin.opd@berau.go.id',
                'password' => Hash::make('password'),
                'role' => 'admin_opd',
                'nip' => '198507202010011012',
                'nik' => '6403012007850002',
                'jabatan' => 'Pranata Komputer Ahli Muda',
                'opd_name' => 'Dinas Komunikasi dan Informatika',
                'phone' => '081254332211',
                'is_active' => true,
            ]);
        }

        // 2. Dapatkan Super Admin
        $this->superAdmin = User::whereIn('role', ['admin', 'super_admin'])->first();

        // 3. Dapatkan / Buat Pegawai di OPD Diskominfo
        $this->pegawaiOpd = User::where('opd_name', 'Dinas Komunikasi dan Informatika')
            ->whereIn('role', ['student', 'asn_learner'])
            ->first();

        if (!$this->pegawaiOpd) {
            $this->pegawaiOpd = User::create([
                'name' => 'Ahmad Fauzi, A.Md',
                'email' => 'ahmad.fauzi@berau.go.id',
                'password' => Hash::make('password'),
                'role' => 'asn_learner',
                'nip' => '199204122019031005',
                'nik' => '6403011204920001',
                'jabatan' => 'Pengelola Situs Web',
                'opd_name' => 'Dinas Komunikasi dan Informatika',
                'phone' => '081344556677',
                'is_active' => true,
            ]);
        }
    }

    /**
     * 1. Uji Akses Dashboard Mandiri Admin OPD
     */
    public function test_admin_opd_can_access_opd_dashboard()
    {
        $response = $this->actingAs($this->adminOpd)->get('/dashboard/opd');
        $response->assertStatus(200);
        $response->assertSee('Dinas Komunikasi dan Informatika');
    }

    /**
     * 2. Uji CRUD & Siklus Hidup Alur Usulan Kegiatan Diklat OPD (Create, Update, Resubmit, Approve, Delete)
     */
    public function test_admin_opd_crud_and_lifecycle_activity_request()
    {
        // A. CREATE: Admin OPD mengajukan kegiatan pelatihan baru
        $title = 'Bimtek Tata Kelola Keamanan Informasi ' . Str::random(5);
        $createResponse = $this->actingAs($this->adminOpd)->post('/admin/opd-requests', [
            'title' => $title,
            'description' => 'Pelatihan kepatuhan ISO 27001 dan keamanan data siber bagi pengelola TIK OPD.',
            'learning_method' => 'Workshop / Praktik Langsung',
            'course_type' => 'workshop',
            'jp' => 16,
            'target_audience' => 'Pengelola TIK dan Pranata Komputer Dinas Kominfo',
            'objectives' => 'Meningkatkan indeks keamanan informasi instansi pemerintah.',
            'speaker_name' => 'Pakar Keamanan Siber BSSN',
            'proposed_start_date' => now()->addDays(14)->toDateString(),
            'proposed_end_date' => now()->addDays(16)->toDateString(),
            'proposed_location' => 'Laboratorium Komputer Diskominfo',
            'estimated_participants' => 30,
            'budget_source' => 'DPA Diskominfo TA ' . now()->year,
            'supporting_notes' => 'Telah dialokasikan dalam rencana kerja tahunan.',
        ]);

        $createResponse->assertSessionHasNoErrors();
        $createResponse->assertRedirect();
        $this->assertDatabaseHas('opd_activity_requests', [
            'title' => $title,
            'submitted_by' => $this->adminOpd->id,
            'status' => 'pending',
        ]);

        $request = OpdActivityRequest::where('title', $title)->first();

        // B. READ: Admin OPD melihat detail pengajuannya
        $detailResponse = $this->actingAs($this->adminOpd)->get("/admin/opd-requests/{$request->id}");
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee($title);

        // C. REVIEW (BKPSDM / Super Admin): Minta revisi silabus/anggaran
        $revisionResponse = $this->actingAs($this->superAdmin)->post("/admin/opd-requests/{$request->id}/revision", [
            'review_notes' => 'Perlu penyesuaian detail silabus dan kriteria peserta.',
            'revision_request' => 'Mohon lampirkan rincian modul per hari kegiatan.',
        ]);
        $revisionResponse->assertSessionHasNoErrors();
        $request->refresh();
        $this->assertEquals('revision_needed', $request->status);

        // D. UPDATE: Admin OPD mengedit revisi dan mengirim ulang (Resubmit)
        $updatedTitle = $title . ' (Silabus Lengkap)';
        $updateResponse = $this->actingAs($this->adminOpd)->post("/admin/opd-requests/{$request->id}", [
            'title' => $updatedTitle,
            'description' => 'Pelatihan kepatuhan ISO 27001 dilengkapi silabus 3 hari modul terpadu.',
            'learning_method' => 'Workshop / Praktik Langsung',
            'jp' => 18,
            'target_audience' => 'Pengelola TIK dan Pranata Komputer',
            'objectives' => 'Peningkatan indeks KAMI dan CSIRT daerah.',
            'speaker_name' => 'Pakar Keamanan Siber BSSN',
            'proposed_start_date' => now()->addDays(20)->toDateString(),
            'proposed_end_date' => now()->addDays(22)->toDateString(),
            'proposed_location' => 'Laboratorium Komputer Diskominfo',
            'estimated_participants' => 35,
        ]);

        $updateResponse->assertSessionHasNoErrors();
        $request->refresh();
        $this->assertEquals($updatedTitle, $request->title);
        $this->assertEquals('pending', $request->status); // Status kembali pending setelah diupdate

        // E. APPROVE: BKPSDM menyetujui pengajuan -> Kursus otomatis terbit
        $approveResponse = $this->actingAs($this->superAdmin)->post("/admin/opd-requests/{$request->id}/approve", [
            'review_notes' => 'Silabus dan kesiapan tempat disetujui.',
            'approval_notes' => 'Silabus dan kesiapan tempat disetujui.',
        ]);
        $approveResponse->assertSessionHasNoErrors();
        $request->refresh();
        $this->assertEquals('approved', $request->status);
        $this->assertNotNull($request->course_id);

        // Verifikasi Kursus terbit di database
        $this->assertDatabaseHas('courses', [
            'id' => $request->course_id,
            'approval_status' => 'approved',
        ]);

        // F. DELETE: Admin OPD membatalkan & menghapus usulan lain yang berstatus draft/pending
        $draftTitle = 'Usulan Uji Hapus ' . Str::random(5);
        $draft = OpdActivityRequest::create([
            'submitted_by' => $this->adminOpd->id,
            'title' => $draftTitle,
            'opd_name' => $this->adminOpd->opd_name,
            'learning_method' => 'Seminar',
            'status' => 'pending',
            'jp' => 4,
        ]);

        $deleteResponse = $this->actingAs($this->adminOpd)->delete("/admin/opd-requests/{$draft->id}");
        $deleteResponse->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('opd_activity_requests', ['id' => $draft->id]);

        // Clean up course & request
        if ($request->course) {
            $request->course->delete();
        }
        $request->delete();
    }

    /**
     * 3. Uji Pemantauan Kepatuhan 20 JP Pegawai di Lingkup OPD
     */
    public function test_admin_opd_monitoring_jp_compliance()
    {
        // A. Akses Dashboard JP (Otomatis difilter ke OPD Admin)
        $responseJp = $this->actingAs($this->adminOpd)->get('/admin/jp');
        $responseJp->assertStatus(200);
        $responseJp->assertSee('Rekapitulasi Jam Pelajaran');

        // B. Akses Rekapitulasi JP per Pegawai ASN OPD
        $responseAsn = $this->actingAs($this->adminOpd)->get('/admin/jp/asn');
        $responseAsn->assertStatus(200);
        $responseAsn->assertSee('Rekapitulasi Jam Pelajaran (JP) per ASN');

        // C. Akses Peringkat Kepatuhan OPD
        $responseOpd = $this->actingAs($this->adminOpd)->get('/admin/jp/opd');
        $responseOpd->assertStatus(200);
        $responseOpd->assertSee('Dinas Komunikasi dan Informatika');

        // D. Ekspor Rekapitulasi JP Pegawai OPD ke CSV / Excel
        $exportResponse = $this->actingAs($this->adminOpd)->get('/admin/jp/export/asn');
        $exportResponse->assertStatus(200);
        $this->assertTrue(
            str_contains($exportResponse->headers->get('content-type', ''), 'text/csv') ||
            str_contains($exportResponse->headers->get('content-disposition', ''), 'attachment')
        );
    }

    /**
     * 4. Uji Alur Validasi Perubahan Data Pegawai OPD
     */
    public function test_validation_of_opd_pegawai_profile_change()
    {
        // Pegawai Diskominfo mengajukan pembaruan data jabatan/kontak
        $changeRequest = ProfileChangeRequest::create([
            'user_id' => $this->pegawaiOpd->id,
            'proposed_changes' => [
                'name' => $this->pegawaiOpd->name,
                'opd_name' => 'Dinas Komunikasi dan Informatika',
                'bagian' => 'Bidang Aplikasi Informatika (Aptika)',
                'jabatan' => 'Pranata Komputer Mahir',
                'pangkat_golongan' => 'Penata Muda (III/a)',
            ],
            'reason' => 'Kenaikan pangkat dan mutasi ke Bidang Aptika.',
            'status' => 'pending',
        ]);

        // Verifikator / Super Admin menyetujui pengajuan validasi data
        $approveResponse = $this->actingAs($this->superAdmin)
            ->post("/admin/users/verifications/{$changeRequest->id}/approve");

        $approveResponse->assertSessionHasNoErrors();
        $changeRequest->refresh();
        $this->assertEquals('approved', $changeRequest->status);

        // Pastikan profil pegawai ter-update sesuai data yang divalidasi
        $this->pegawaiOpd->refresh();
        $this->assertEquals('Pranata Komputer Mahir', $this->pegawaiOpd->jabatan);
        $this->assertEquals('Bidang Aplikasi Informatika (Aptika)', $this->pegawaiOpd->bagian);

        // Clean up
        $changeRequest->delete();
    }
}
