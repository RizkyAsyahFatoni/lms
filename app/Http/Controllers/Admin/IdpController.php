<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IdpPlan;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;

class IdpController extends Controller
{
    /**
     * Tampilkan halaman manajemen IDP.
     * Hanya admin yang boleh mengakses.
     */
    public function index()
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $totalNip    = IdpPlan::distinct('nip')->count('nip');
        $totalMateri = IdpPlan::count();
        $batches     = IdpPlan::select('batch_id', 'uploaded_by', \DB::raw('COUNT(*) as total_rows'), \DB::raw('MAX(created_at) as uploaded_at'))
            ->groupBy('batch_id', 'uploaded_by')
            ->with('uploader:id,name')
            ->latest('uploaded_at')
            ->get();

        // Preview data: tampilkan 100 baris terakhir diurutkan per NIP & Tahun
        $plans = IdpPlan::orderBy('nip')->orderBy('tahun')->limit(200)->get();

        return view('admin.idp.index', compact('totalNip', 'totalMateri', 'batches', 'plans'));
    }

    /**
     * Upload dan proses file Excel IDP.
     * Format: Kolom A=NIP, B=Nama, C=Tahun (1-5), D=Judul Materi
     */
    public function store(Request $request)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $request->validate([
            'idp_file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
            'upload_mode' => ['required', 'in:replace,append'],
        ], [
            'idp_file.required' => 'File IDP wajib diunggah.',
            'idp_file.mimes'    => 'Format file harus .xlsx, .xls, atau .csv.',
            'idp_file.max'      => 'Ukuran file maksimal 10 MB.',
        ]);

        $file      = $request->file('idp_file');
        $batchId   = 'IDP-' . now()->format('Ymd-His') . '-' . strtoupper(Str::random(4));
        $adminId   = Auth::id();
        $mode      = $request->input('upload_mode', 'replace');

        // Baca file Excel menggunakan Maatwebsite Excel (collection mode)
        $rows = Excel::toCollection(null, $file)->first(); // Sheet pertama

        if (!$rows || $rows->isEmpty()) {
            return back()->withErrors(['idp_file' => 'File Excel kosong atau format tidak sesuai.'])->withInput();
        }

        // Skip baris header (baris pertama)
        $dataRows = $rows->skip(1)->values();

        $inserted    = 0;
        $skipped     = 0;
        $toInsert    = [];
        $nipsSeen    = collect();

        foreach ($dataRows as $row) {
            $nip        = trim((string) ($row[0] ?? ''));
            $nama       = trim((string) ($row[1] ?? ''));
            $tahun      = (int) ($row[2] ?? 1);
            $judulMateri = trim((string) ($row[3] ?? ''));

            // Skip baris kosong atau tidak valid
            if (empty($nip) || empty($judulMateri)) {
                $skipped++;
                continue;
            }

            // Normalisasi tahun: pastikan antara 1-5
            $tahun = max(1, min(5, $tahun));

            $toInsert[] = [
                'nip'         => $nip,
                'nama_asn'    => $nama ?: null,
                'tahun'       => $tahun,
                'judul_materi'=> $judulMateri,
                'uploaded_by' => $adminId,
                'batch_id'    => $batchId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ];

            $nipsSeen->push($nip);
            $inserted++;
        }

        if (empty($toInsert)) {
            return back()->withErrors(['idp_file' => 'Tidak ada data valid dalam file. Pastikan format kolom: NIP | Nama | Tahun | Judul Materi.'])->withInput();
        }

        // Mode replace: hapus data lama untuk NIP-NIP yang ada di file ini
        if ($mode === 'replace') {
            $uniqueNips = $nipsSeen->unique()->values()->toArray();
            IdpPlan::whereIn('nip', $uniqueNips)->delete();
        }

        // Simpan data baru dalam chunk
        foreach (array_chunk($toInsert, 500) as $chunk) {
            IdpPlan::insert($chunk);
        }

        return back()->with('success', "✅ Berhasil mengimport {$inserted} materi IDP dari " . $nipsSeen->unique()->count() . " NIP. Batch ID: {$batchId}" . ($skipped > 0 ? " ({$skipped} baris dilewati)" : ''));
    }

    /**
     * Hapus seluruh data IDP berdasarkan batch_id.
     */
    public function destroyBatch(string $batchId)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $deleted = IdpPlan::where('batch_id', $batchId)->delete();

        return back()->with('success', "🗑️ Berhasil menghapus {$deleted} baris IDP dari batch {$batchId}.");
    }

    /**
     * Hapus semua data IDP (reset total).
     */
    public function destroyAll()
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $deleted = IdpPlan::truncate();

        return back()->with('success', '🗑️ Semua data IDP berhasil direset.');
    }
}
