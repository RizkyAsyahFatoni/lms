@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50/60 py-10">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Header Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-2xl">🔔</span>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Pusat Notifikasi</h1>
                    @if($unreadCount > 0)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-teal-100 text-teal-800 border border-teal-200">
                            {{ $unreadCount }} Belum Dibaca
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Riwayat terpadu pemberitahuan kursus, webinar, persetujuan diklat, dan penerbitan e-sertifikat Anda.
                </p>
            </div>

            @if($unreadCount > 0)
                <form action="{{ route('notifications.markAllRead') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 shadow-2xs transition">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Tandai Semua Dibaca</span>
                    </button>
                </form>
            @endif
        </div>

        <!-- Filter Tabs -->
        <div class="flex items-center gap-2">
            <a href="{{ route('notifications.index', ['filter' => 'all']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $filter === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                <span>Semua</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono {{ $filter === 'all' ? 'bg-slate-800 text-slate-200' : 'bg-slate-100 text-slate-600' }}">
                    {{ $totalCount }}
                </span>
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $filter === 'unread' ? 'bg-teal-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                <span>Belum Dibaca</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono {{ $filter === 'unread' ? 'bg-teal-700 text-teal-100' : 'bg-slate-100 text-slate-600' }}">
                    {{ $unreadCount }}
                </span>
            </a>
        </div>

        <!-- Notification List -->
        @if($notifications->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-200/90 p-12 text-center shadow-sm">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-3xl mb-3">
                    🔕
                </div>
                <h3 class="text-base font-bold text-slate-900">Belum ada notifikasi</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    {{ $filter === 'unread' ? 'Semua notifikasi Anda telah ditandai dibaca.' : 'Saat ada pembaruan kegiatan pembelajaran atau pengumuman resmi, infonya akan tercatat di sini.' }}
                </p>
                @if($filter === 'unread')
                    <div class="mt-4">
                        <a href="{{ route('notifications.index', ['filter' => 'all']) }}" 
                           class="inline-flex items-center gap-1 text-xs font-bold text-brand-600 hover:underline">
                            Lihat semua riwayat notifikasi &rarr;
                        </a>
                    </div>
                @endif
            </div>
        @else
            <div class="space-y-3">
                @foreach($notifications as $notification)
                    @php
                        $data = $notification->data;
                        $cat = $data['category'] ?? 'system';
                        $isUnread = is_null($notification->read_at);

                        $catConfig = match($cat) {
                            'certificate' => ['label' => 'E-Sertifikat', 'icon' => '📜', 'bg' => 'bg-emerald-50 text-emerald-800 border-emerald-200'],
                            'course'      => ['label' => 'Kursus LMS', 'icon' => '🎓', 'bg' => 'bg-blue-50 text-blue-800 border-blue-200'],
                            'webinar'     => ['label' => 'Webinar / In-Class', 'icon' => '🎥', 'bg' => 'bg-teal-50 text-teal-800 border-teal-200'],
                            'evaluation'  => ['label' => 'Evaluasi Pembelajaran', 'icon' => '📝', 'bg' => 'bg-amber-50 text-amber-800 border-amber-200'],
                            'approval'    => ['label' => 'Persetujuan / Usulan', 'icon' => '🏛️', 'bg' => 'bg-purple-50 text-purple-800 border-purple-200'],
                            'profile'     => ['label' => 'Data Profil ASN', 'icon' => '👤', 'bg' => 'bg-sky-50 text-sky-800 border-sky-200'],
                            default       => ['label' => 'Sistem & Pengumuman', 'icon' => '🔔', 'bg' => 'bg-slate-100 text-slate-800 border-slate-200'],
                        };
                    @endphp

                    <div class="bg-white rounded-2xl border p-4 sm:p-5 shadow-sm transition hover:shadow-md flex items-start gap-4 {{ $isUnread ? 'border-teal-300 ring-1 ring-teal-200/50 bg-teal-50/20' : 'border-slate-200/90' }}">
                        
                        <!-- Category Badge Icon -->
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 text-lg border {{ $catConfig['bg'] }}">
                            {{ $catConfig['icon'] }}
                        </div>

                        <!-- Content Details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider border {{ $catConfig['bg'] }}">
                                        {{ $catConfig['label'] }}
                                    </span>
                                    @if($isUnread)
                                        <span class="px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-teal-600 text-white uppercase tracking-wider">
                                            BARU
                                        </span>
                                    @endif
                                </div>
                                <span class="text-[11px] text-slate-400 font-mono">
                                    {{ $notification->created_at->format('d M Y, H:i') }} ({{ $notification->created_at->diffForHumans() }})
                                </span>
                            </div>

                            <h3 class="text-sm font-bold text-slate-900 mt-1.5 leading-snug">
                                {{ $data['title'] ?? 'Pemberitahuan' }}
                            </h3>

                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                {{ $data['message'] ?? '' }}
                            </p>

                            <!-- Actions Row -->
                            <div class="flex items-center justify-between pt-3 mt-3 border-t border-slate-100">
                                <div>
                                    @if(!empty($data['url']) && $data['url'] !== '#')
                                        <a href="{{ route('notifications.read', $notification->id) }}" 
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-brand-600 text-white text-xs font-bold transition shadow-2xs">
                                            <span>Buka Detail / Tautan</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </a>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2">
                                    @if($isUnread)
                                        <a href="{{ route('notifications.read', $notification->id) }}" 
                                           class="text-[11px] font-semibold text-teal-700 hover:underline">
                                            Tandai dibaca
                                        </a>
                                    @endif

                                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('Hapus notifikasi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-[11px] text-slate-400 hover:text-rose-600 transition" 
                                                title="Hapus notifikasi">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div>

                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pt-4">
                {{ $notifications->links() }}
            </div>
        @endif

    </div>
</div>
@endsection
