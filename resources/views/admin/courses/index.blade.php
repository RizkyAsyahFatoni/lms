@extends('layouts.admin')

@section('page_title', 'Kelola Kursus & Kurikulum')

@section('content')
<div class="space-y-6">

    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Manajemen Kursus & Kurikulum</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola data pelatihan, bab kurikulum, modul materi (video, PDF, tugas praktik, kuis).</p>
        </div>

        <a href="{{ route('admin.courses.create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>+ Tambah Kursus Baru</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-4">Pelatihan</th>
                        <th class="p-4">Kategori</th>
                        <th class="p-4">Instruktur</th>
                        <th class="p-4">Level</th>
                        <th class="p-4">Investasi</th>
                        <th class="p-4">Peserta</th>
                        <th class="p-4">Total Materi</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @foreach($courses as $c)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <p class="font-bold text-slate-900">{{ $c->title }}</p>
                                    @switch($c->course_type ?? 'e-course')
                                        @case('live-webinar')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">📹 Live Webinar</span>
                                            @break
                                        @case('bimtek')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">🏫 Bimtek</span>
                                            @break
                                        @case('workshop')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">🔧 Workshop</span>
                                            @break
                                        @case('hybrid')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-50 text-cyan-700 border border-cyan-200">🔀 Hybrid</span>
                                            @break
                                        @default
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">📚 E-Course</span>
                                    @endswitch

                                    @if(($c->approval_status ?? 'approved') === 'pending')
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-500 text-white animate-pulse">⏳ Menunggu Review</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 mt-0.5 text-[11px] text-slate-400">
                                    <span>{{ $c->jp ?? 2 }} JP</span>
                                    <span>•</span>
                                    <span>Target: {{ $c->target_audience_type === 'asn-opd' ? 'ASN OPD' : ($c->target_audience_type === 'jabatan-tertentu' ? 'Jabatan Tertentu' : 'Umum') }}</span>
                                    @if($c->sub_category)
                                        <span>•</span>
                                        <span class="text-teal-600 font-semibold">{{ $c->sub_category }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-brand-50 text-brand-700">
                                    {{ $c->category->name }}
                                </span>
                            </td>
                            <td class="p-4 text-slate-600">{{ $c->instructor->name }}</td>
                            <td class="p-4">{{ $c->level }}</td>
                            <td class="p-4 font-bold {{ $c->price <= 0 ? 'text-emerald-600' : 'text-slate-900' }}">
                                {{ $c->formatted_price }}
                            </td>
                            <td class="p-4 font-semibold">{{ number_format($c->students_count) }}</td>
                            <td class="p-4 font-semibold text-brand-700">{{ $c->lessons->count() }} Modul</td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if(($c->approval_status ?? 'approved') === 'pending')
                                        <form action="{{ route('admin.courses.approve', $c->id) }}" method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menyetujui materi ini agar tayang di LMS?');">
                                            @csrf
                                            <button type="submit" 
                                                    class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-2xs">
                                                ✓ Setujui
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Tombol Edit (Membuka semua informasi materi, silabus, dan sertifikat) -->
                                    <a href="{{ route('admin.courses.edit', $c->id) }}" 
                                       title="Edit Informasi Lengkap, Silabus & Sertifikat"
                                       class="inline-flex items-center gap-1 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-lg border border-slate-200/80 transition">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span>Edit</span>
                                    </a>

                                    <!-- Tombol Preview -->
                                    <a href="{{ route('courses.show', $c->slug) }}" target="_blank"
                                       title="Lihat Pratinjau Publik"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold text-brand-700 bg-brand-50 hover:bg-brand-100 border border-brand-200/80 transition">
                                        <svg class="w-3.5 h-3.5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Preview</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $courses->links() }}
        </div>
    </div>
</div>
@endsection
