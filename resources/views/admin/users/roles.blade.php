@extends('layouts.admin')

@section('page_title', 'Pengelolaan Role Pengguna')

@section('content')
<div class="space-y-6">

    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Peran & Hak Akses Pengguna</h1>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-brand-50 text-brand-700 border border-brand-200">
                    User Privileges & Roles
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Struktur peran, tanggung jawab, dan batasan hak akses operasional pada platform LMS.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.permissions.index') }}" 
               class="px-3.5 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-brand-700 transition shadow-sm flex items-center gap-1.5">
                <span>🔑</span>
                <span>Buka Matriks Hak Akses</span>
            </a>
            <a href="{{ route('admin.users.index') }}" 
               class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs flex items-center gap-1.5">
                <span>👥</span>
                <span>Data Pengguna</span>
            </a>
        </div>
    </div>

    <!-- Role Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($rolesDefinition as $roleKey => $role)
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 flex flex-col justify-between hover:shadow-md transition">
                <div>
                    <!-- Header -->
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-2xl shadow-2xs">
                            {{ $role['icon'] }}
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase bg-slate-100 text-slate-700">
                            {{ $roleCounts[$roleKey] ?? 0 }} Pengguna
                        </span>
                    </div>

                    <!-- Role Name -->
                    <h3 class="text-base font-extrabold text-slate-900 leading-snug">{{ $role['name'] }}</h3>
                    <div class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-600">
                        role_key: {{ $roleKey }}
                    </div>

                    <!-- Function / Description -->
                    <div class="mt-4 p-3 rounded-xl bg-slate-50/80 border border-slate-100 space-y-1">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 font-mono">Fungsi & Kewenangan:</p>
                        <p class="text-xs font-semibold text-slate-800 leading-relaxed">{{ $role['function'] ?? ($role['description'] ?? '-') }}</p>
                    </div>

                    <p class="text-xs text-slate-500 mt-3 leading-relaxed">{{ $role['description'] }}</p>
                </div>

                <!-- Footer Action -->
                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                    <a href="{{ route('admin.users.index', ['role' => $roleKey]) }}" 
                       class="font-bold text-brand-600 hover:text-brand-800 transition flex items-center gap-1">
                        <span>Lihat {{ $roleCounts[$roleKey] ?? 0 }} Pengguna</span>
                        <span>&rarr;</span>
                    </a>

                    <a href="{{ route('admin.permissions.index', ['role' => $roleKey]) }}" 
                       class="text-slate-500 hover:text-slate-800 font-semibold flex items-center gap-1">
                        <span>Hak Akses</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Information Card -->
    <div class="bg-gradient-to-r from-slate-900 to-brand-950 text-white p-6 rounded-2xl shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="space-y-1">
            <h4 class="font-extrabold text-sm flex items-center gap-2">
                <span>🔐</span>
                <span>Tata Kelola Hak Akses Berbasis Granular Permission</span>
            </h4>
            <p class="text-xs text-slate-300 max-w-2xl leading-relaxed">
                Setiap peran di atas dipetakan ke dalam matriks izin (permissions). Super Admin / BKPSDM memiliki wewenang penuh (full access), sementara peran OPD, Narasumber, dan Pimpinan dibatasi sesuai lingkup tugas masing-masing.
            </p>
        </div>
        <a href="{{ route('admin.permissions.index') }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold text-slate-900 bg-white hover:bg-slate-100 transition whitespace-nowrap">
            Konfigurasi Matriks Hak Akses &rarr;
        </a>
    </div>

</div>
@endsection
