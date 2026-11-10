@extends('layouts.admin')

@section('page_title', 'Pengaturan Hak Akses (Permissions Matrix)')

@section('content')
<div class="space-y-6">

    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Matriks Pengaturan Hak Akses</h1>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-brand-50 text-brand-700 border border-brand-200">
                    Granular Access Control
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Konfigurasi izin akses (permissions) untuk masing-masing dari 6 peran pengguna sistem.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.roles.index') }}" 
               class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs flex items-center gap-1.5">
                <span>🛡️</span>
                <span>Daftar Role</span>
            </a>
            <a href="{{ route('admin.users.index') }}" 
               class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs flex items-center gap-1.5">
                <span>👥</span>
                <span>Data Pengguna</span>
            </a>
        </div>
    </div>

    <!-- Info Banner -->
    <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200/80 shadow-xs flex items-start gap-3">
        <div class="w-8 h-8 rounded-xl bg-blue-500 text-white flex items-center justify-center text-sm font-black shadow-sm shrink-0">
            ℹ️
        </div>
        <div class="text-xs text-blue-900 leading-relaxed">
            <p class="font-bold">Informasi Kebijakan Keamanan:</p>
            <p class="mt-0.5 text-blue-800">
                Peran <strong>Super Admin / BKPSDM</strong> memiliki hak akses tak terbatas secara sistem (Full Control). Untuk 5 peran lainnya, centang izin yang diizinkan lalu klik tombol <strong>Simpan Perubahan Matriks</strong> di bagian bawah halaman.
            </p>
        </div>
    </div>

    <!-- Form Matriks Hak Akses -->
    <form action="{{ route('admin.permissions.update') }}" method="POST">
        @csrf

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Matriks Izin Modul LMS</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Atur hak baca, tulis, eksekusi, persetujuan, dan unduh laporan per peran pengguna.</p>
                </div>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm flex items-center gap-1.5">
                    <span>💾 Simpan Perubahan</span>
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/80 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                            <th class="py-3 px-4 w-72">Modul & Hak Akses</th>
                            @foreach($rolesDefinition as $rKey => $rDef)
                                <th class="py-3 px-3 text-center {{ $rKey === 'super_admin' ? 'bg-purple-50/60 text-purple-900' : '' }}">
                                    <div class="text-base">{{ $rDef['icon'] }}</div>
                                    <div class="font-black text-[10px] mt-0.5 leading-tight">{{ $rDef['name'] }}</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @foreach($permissionsByGroup as $groupName => $perms)
                            <!-- Group Header Row -->
                            <tr class="bg-slate-100/60 font-bold text-slate-800">
                                <td colspan="{{ count($rolesDefinition) + 1 }}" class="py-2 px-4 text-xs font-mono uppercase tracking-wider text-slate-700">
                                    📁 {{ $groupName }}
                                </td>
                            </tr>

                            <!-- Permission Items -->
                            @foreach($perms as $pKey => $pLabel)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <!-- Permission Label -->
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-slate-900">{{ $pLabel }}</div>
                                        <div class="text-[10px] font-mono text-slate-400 mt-0.5">{{ $pKey }}</div>
                                    </td>

                                    <!-- Role Checkboxes -->
                                    @foreach($rolesDefinition as $rKey => $rDef)
                                        <td class="py-3 px-3 text-center {{ $rKey === 'super_admin' ? 'bg-purple-50/30' : '' }}">
                                            @if($rKey === 'super_admin')
                                                <!-- Super Admin always true -->
                                                <input type="checkbox" checked disabled 
                                                       class="w-4 h-4 rounded text-purple-600 bg-purple-100 border-purple-300 cursor-not-allowed"
                                                       title="Super Admin selalu memiliki akses penuh">
                                            @else
                                                @php
                                                    $isGranted = isset($matrix[$rKey][$pKey]) ? (bool)$matrix[$rKey][$pKey] : false;
                                                @endphp
                                                <label class="inline-flex items-center justify-center cursor-pointer p-1">
                                                    <input type="checkbox" 
                                                           name="permissions[{{ $rKey }}][{{ $pKey }}]" 
                                                           value="1" 
                                                           {{ $isGranted ? 'checked' : '' }}
                                                           class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300 cursor-pointer">
                                                </label>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Table Footer -->
            <div class="p-4 sm:p-5 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3">
                <span class="text-xs text-slate-500">
                    Pastikan untuk meninjau kembali perubahan sebelum menyimpan pengaturan hak akses.
                </span>
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-brand-700 transition shadow-sm flex items-center justify-center gap-1.5">
                    <span>💾 Simpan Perubahan Matriks</span>
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
