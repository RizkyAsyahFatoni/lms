<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function show(string $number): View
    {
        $certificate = Certificate::with(['user', 'course.instructor', 'course.category'])
            ->where('certificate_number', $number)
            ->orWhere('verification_hash', $number)
            ->firstOrFail();

        return view('certificates.show', compact('certificate'));
    }
}
