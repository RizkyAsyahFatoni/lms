@extends('layouts.admin')

@section('page_title', 'Pencatatan Aktivitas Pengguna (Audit Trail)')

@section('content')
<div class="space-y-6">

    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Audit Trail & Log Aktivitas Pengguna</h1>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-brand-50 text-brand-700 border border-brand-200">
                    {{ $logs->total() }} Catatan
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Rekam jejak setiap aksi krusial pengguna seperti login, perubahan profil, pengelolaan hak akses, dan partisipasi diklat.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.users.index') }}" 
               class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs flex items-center gap-1.5">
                <span>👥</span>
                <span>Data Pengguna</span>
            </a>
            <a href="{{ route('admin.permissions.index') }}" 
               class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs flex items-center gap-1.5">
                <span>🔑</span>
                <span>Matriks Hak Akses</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
        <form action="{{ route('admin.activity-logs.index') }}" method="GET" class="flex flex-col lg:flex-row gap-3">
            <div class="flex-1 relative">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari deskripsi aktivitas atau alamat IP..."
                       class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                <!-- Action Filter -->
                <select name="action" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none font-medium text-slate-700">
                    <option value="">Semua Jenis Aksi</option>
                    @foreach($actionTypes as $aType)
                        <option value="{{ $aType }}" {{ request('action') === $aType ? 'selected' : '' }}>
                            {{ strtoupper(str_replace('_', ' ', $aType)) }}
                        </option>
                    @endforeach
                </select>

                <!-- Date Filter -->
                <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()"
                       class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none font-medium text-slate-700">

                <button type="submit" class="px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 transition">
                    Filter
                </button>
                @if(request('q') || request('action') || request('user_id') || request('date'))
                    <a href="{{ route('admin.activity-logs.index') }}" class="px-3 py-2 rounded-xl text-xs font-semibold text-slate-500 hover:bg-slate-100 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table of Activity Logs -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-sm">Riwayat Catatan Aktivitas Terkini</h3>
            <span class="text-xs text-slate-500 font-mono">Total {{ $logs->total() }} Log</span>
        </div>

        @if($logs->isEmpty())
            <div class="p-12 text-center text-xs text-slate-400">
                <div class="text-3xl mb-2">📜</div>
                <p class="font-bold text-slate-600">Belum ada riwayat aktivitas tercatat sesuai filter.</p>
                <p class="text-slate-400 mt-1">Aktivitas baru akan otomatis terekam saat pengguna berinteraksi di sistem.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-4">Waktu</th>
                            <th class="py-3 px-4">Pengguna (Aktor)</th>
                            <th class="py-3 px-4 text-center">Jenis Aksi</th>
                            <th class="py-3 px-4">Deskripsi Aktivitas</th>
                            <th class="py-3 px-4">IP Address & Browser</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @foreach($logs as $log)
                            <tr class="hover:bg-slate-50/70 transition">
                                <!-- Timestamp -->
                                <td class="py-3.5 px-4 text-slate-500 whitespace-nowrap">
                                    <div class="font-bold text-slate-800">{{ $log->created_at->format('d/m/Y') }}</div>
                                    <div class="text-[10px] font-mono text-slate-400">{{ $log->created_at->format('H:i:s') }} WIB</div>
                                </td>

                                <!-- User Info -->
                                <td class="py-3.5 px-4">
                                    @if($log->user)
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-[11px] shrink-0">
                                                {{ substr($log->user->name, 0, 2) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.users.show', $log->user) }}" class="font-bold text-slate-900 hover:text-brand-700 transition">
                                                    {{ $log->user->name }}
                                                </a>
                                                <div class="text-[10px] text-slate-400 font-mono">{{ $log->user->email }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic">Sistem / Tamu</span>
                                    @endif
                                </td>

                                <!-- Action Badge -->
                                <td class="py-3.5 px-4 text-center">
                                    {!! $log->action_badge !!}
                                </td>

                                <!-- Description -->
                                <td class="py-3.5 px-4 text-slate-700 leading-relaxed">
                                    <div class="font-medium">{{ $log->description }}</div>
                                    @if(!empty($log->properties))
                                        <div class="mt-1 text-[10px] font-mono text-slate-500 bg-slate-50 p-1.5 rounded border border-slate-100 max-w-md truncate">
                                            {{ json_encode($log->properties, JSON_UNESCAPED_UNICODE) }}
                                        </div>
                                    @endif
                                </td>

                                <!-- IP & Agent -->
                                <td class="py-3.5 px-4 text-slate-500 font-mono text-[11px]">
                                    <div class="font-bold text-slate-700">{{ $log->ip_address ?: '-' }}</div>
                                    <div class="text-[10px] text-slate-400 truncate max-w-xs" title="{{ $log->user_agent }}">
                                        {{ Str::limit($log->user_agent, 30) }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
