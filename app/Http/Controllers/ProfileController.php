<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use App\Models\ProfileChangeRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Tampilan Pengelolaan Profil Pengguna (KAK: pengelolaan profil)
     */
    public function edit(): View
    {
        $user = Auth::user();
        $recentLogs = ActivityLog::where('user_id', $user->id)->latest()->take(10)->get();
        $pendingRequest = ProfileChangeRequest::where('user_id', $user->id)->where('status', 'pending')->latest()->first();

        return view('profile.edit', compact('user', 'recentLogs', 'pendingRequest'));
    }

    /**
     * Update Data Profil Pengguna
     * Jika peserta ASN mengubah data resmi (OPD, Bagian, Jabatan, Pangkat, NIP, Nama),
     * perubahannya akan berstatus pending dan menunggu verifikasi Admin BKPSDM.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $isAdmin = in_array($user->role, ['admin', 'super_admin', 'bkpsdm']);

        $request->validate([
            'name'             => 'required|string|max:150',
            'email'            => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'nip'              => 'nullable|string|max:30',
            'opd_name'         => 'nullable|string|max:255',
            'bagian'           => 'nullable|string|max:255',
            'jabatan'          => 'nullable|string|max:255',
            'pangkat_golongan' => 'nullable|string|max:100',
            'phone'            => 'nullable|string|max:30',
            'change_reason'    => 'nullable|string|max:500',
            'headline'         => 'nullable|string|max:255',
            'bio'              => 'nullable|string|max:1000',
            'avatar'           => 'nullable|url',
        ], [
            'name.required'  => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email resmi wajib diisi.',
            'email.unique'   => 'Email ini sudah digunakan oleh akun lain.',
        ]);

        $cleanNip = $request->filled('nip') ? preg_replace('/[^0-9]/', '', $request->nip) : null;

        // 1. Jika pengguna adalah Admin / BKPSDM: update langsung seluruh data seketika
        if ($isAdmin) {
            $user->update([
                'name'             => strip_tags(trim($request->name)),
                'email'            => strtolower(trim($request->email)),
                'nip'              => $cleanNip,
                'opd_name'         => $request->opd_name,
                'bagian'           => $request->bagian,
                'jabatan'          => $request->jabatan,
                'pangkat_golongan' => $request->pangkat_golongan,
                'phone'            => $request->phone,
                'headline'         => $request->headline ?: ($request->jabatan ? "{$request->jabatan} di {$request->opd_name}" : null),
                'bio'              => $request->bio,
                'avatar'           => $request->avatar ?: $user->avatar,
            ]);

            ActivityLog::record('update_profile', 'Memperbarui profil mandiri (Administrator)', $user->id);
            return back()->with('success', 'Profil dan data kepegawaian Anda berhasil diperbarui!');
        }

        // 2. Jika peserta ASN biasa: Deteksi apakah ada perubahan pada data resmi kepegawaian
        $kepegawaianChanges = [];
        $oldKepegawaian = [];

        if (strip_tags(trim($request->name)) !== $user->name) {
            $kepegawaianChanges['name'] = strip_tags(trim($request->name));
            $oldKepegawaian['name'] = $user->name;
        }
        if ($cleanNip !== $user->nip) {
            $kepegawaianChanges['nip'] = $cleanNip;
            $oldKepegawaian['nip'] = $user->nip;
        }
        if ($request->opd_name !== $user->opd_name) {
            $kepegawaianChanges['opd_name'] = $request->opd_name;
            $oldKepegawaian['opd_name'] = $user->opd_name;
        }
        if ($request->bagian !== $user->bagian) {
            $kepegawaianChanges['bagian'] = $request->bagian;
            $oldKepegawaian['bagian'] = $user->bagian;
        }
        if ($request->jabatan !== $user->jabatan) {
            $kepegawaianChanges['jabatan'] = $request->jabatan;
            $oldKepegawaian['jabatan'] = $user->jabatan;
        }
        if ($request->pangkat_golongan !== $user->pangkat_golongan) {
            $kepegawaianChanges['pangkat_golongan'] = $request->pangkat_golongan;
            $oldKepegawaian['pangkat_golongan'] = $user->pangkat_golongan;
        }

        // Update data kontak & bio langsung
        $user->update([
            'email'    => strtolower(trim($request->email)),
            'phone'    => $request->phone,
            'headline' => $request->headline ?: $user->headline,
            'bio'      => $request->bio,
            'avatar'   => $request->avatar ?: $user->avatar,
        ]);

        // Jika terdapat perubahan pada OPD, Bagian, Jabatan, Pangkat, NIP, atau Nama:
        if (!empty($kepegawaianChanges)) {
            ProfileChangeRequest::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'status'  => 'pending',
                ],
                [
                    'proposed_changes' => $kepegawaianChanges,
                    'old_values'       => $oldKepegawaian,
                    'reason'           => $request->input('change_reason', 'Pengkinian mandiri data kepegawaian oleh ASN'),
                ]
            );

            ActivityLog::record(
                'request_profile_change',
                'Mengajukan permohonan perubahan data kepegawaian (menunggu verifikasi BKPSDM)',
                $user->id,
                ['proposed' => $kepegawaianChanges]
            );

            return back()->with('success', 'Data kontak Anda berhasil disimpan! Permohonan perubahan data kepegawaian resmi (OPD/Bagian/Jabatan) telah dicatat dan sedang MENUNGGU VERIFIKASI dari Admin BKPSDM.');
        }

        ActivityLog::record('update_profile', 'Memperbarui data profil kontak & bio mandiri', $user->id);
        return back()->with('success', 'Profil Anda berhasil diperbarui!');
    }

    /**
     * Ganti Kata Sandi Mandiri (KAK: reset password)
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required'         => 'Kata sandi baru wajib diisi.',
            'password.confirmed'        => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Kata sandi saat ini tidak cocok dengan data kami.',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        ActivityLog::record('change_password', 'Mengubah kata sandi akun mandiri', $user->id);

        return back()->with('success', 'Kata sandi Anda berhasil diperbarui!');
    }

    /**
     * Upload / Update Foto Profil Pengguna
     */
    public function updatePhoto(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'photo.required' => 'Pilih file foto terlebih dahulu.',
            'photo.image'    => 'File harus berupa gambar.',
            'photo.mimes'    => 'Format foto harus JPG, PNG, atau WebP.',
            'photo.max'      => 'Ukuran foto maksimal 2MB.',
        ]);

        // Hapus foto lama jika tersimpan di storage lokal
        if ($user->avatar && str_contains($user->avatar, '/storage/avatars/')) {
            // Ekstrak nama file dari URL dan hapus dari disk
            $filename = basename(parse_url($user->avatar, PHP_URL_PATH));
            Storage::delete('public/avatars/' . $filename);
        }

        // Simpan foto baru ke disk public (accessible via /storage/avatars/...)
        $file     = $request->file('photo');
        $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/avatars', $filename);

        // Buat URL yang bisa diakses publik
        $url = asset('storage/avatars/' . $filename);

        $user->update(['avatar' => $url]);

        ActivityLog::record('update_photo', 'Memperbarui foto profil', $user->id);

        return back()->with('success', 'Foto profil berhasil diperbarui! Muat ulang halaman jika foto belum muncul.');
    }
}
