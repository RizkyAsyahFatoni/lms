<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class TrainerRegistrationController extends Controller
{
    /**
     * Show trainer registration page or current application status.
     */
    public function show(): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('info', 'Silakan masuk atau buat akun terlebih dahulu untuk mendaftar sebagai Trainer/Instruktur.');
        }

        $user = Auth::user();

        return view('trainer.register', compact('user'));
    }

    /**
     * Handle trainer application submission.
     */
    public function submit(Request $request): RedirectResponse
    {
        $request->validate([
            'trainer_expertise' => 'required|string|max:150',
            'trainer_bio' => 'required|string|min:40|max:2000',
            'headline' => 'nullable|string|max:120',
            'trainer_cv' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ], [
            'trainer_expertise.required' => 'Bidang keahlian wajib diisi.',
            'trainer_bio.required' => 'Deskripsi pengalaman & biografi wajib diisi.',
            'trainer_bio.min' => 'Deskripsi pengalaman minimal 40 karakter.',
            'trainer_cv.max' => 'Ukuran file CV/Sertifikat maksimal 5MB.',
        ]);

        $user = Auth::user();

        $data = [
            'trainer_status' => 'pending',
            'trainer_expertise' => $request->trainer_expertise,
            'trainer_bio' => $request->trainer_bio,
            'trainer_applied_at' => now(),
        ];

        if ($request->filled('headline')) {
            $data['headline'] = $request->headline;
        }

        if ($request->hasFile('trainer_cv')) {
            $file = $request->file('trainer_cv');
            $filename = \App\Services\SecurityService::validateAndSanitizeUpload(
                $file,
                'trainer_cv',
                ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'],
                5242880
            );
            $destination = public_path('uploads/trainers');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);
            $data['trainer_cv_path'] = 'uploads/trainers/' . $filename;
        }

        $user->update($data);

        return redirect()->route('trainer.register')
            ->with('success', 'Pengajuan pendaftaran sebagai Trainer berhasil dikirimkan! Tim Admin LMS KAB Berau akan segera meninjau permohonan Anda.');
    }
}
