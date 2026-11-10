<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrainerController extends Controller
{
    /**
     * Display list of trainer applicants and active instructors.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $query = User::where(function ($q) {
            $q->where('trainer_status', '!=', 'none')
              ->orWhere('role', 'instructor');
        });

        if ($status) {
            $query->where('trainer_status', $status);
        }

        $trainers = $query->withCount('createdCourses')
            ->orderByRaw("CASE WHEN trainer_status = 'pending' THEN 1 ELSE 2 END")
            ->orderBy('trainer_applied_at', 'desc')
            ->paginate(15);

        $pendingCount = User::where('trainer_status', 'pending')->count();
        $approvedCount = User::where('trainer_status', 'approved')->orWhere('role', 'instructor')->count();

        return view('admin.trainers.index', compact('trainers', 'pendingCount', 'approvedCount', 'status'));
    }

    /**
     * Approve trainer applicant.
     */
    public function approve(User $user): RedirectResponse
    {
        $user->update([
            'trainer_status' => 'approved',
            'role' => 'instructor',
        ]);

        return redirect()->back()
            ->with('success', "Akun trainer {$user->name} berhasil disetujui! Instruktur ini sekarang dapat membuat materi kursus, webinar, workshop, dan bimtek.");
    }

    /**
     * Reject trainer applicant.
     */
    public function reject(User $user): RedirectResponse
    {
        $user->update([
            'trainer_status' => 'rejected',
        ]);

        return redirect()->back()
            ->with('info', "Pengajuan trainer untuk {$user->name} telah ditolak.");
    }
}
