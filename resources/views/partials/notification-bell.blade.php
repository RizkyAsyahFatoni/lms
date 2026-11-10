@auth
    @php
        $authUser = auth()->user();
        $unreadCount = $authUser->unreadNotifications()->count();
        $latestNotifications = $authUser->notifications()->latest()->take(5)->get();
    @endphp

    <div class="relative" x-data="{ 
        notifOpen: false,
        unreadCount: {{ $unreadCount }},
        markAllRead() {
            fetch('{{ route('notifications.markAllRead') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            }).then(res => res.json()).then(data => {
                if(data.success) {
                    this.unreadCount = 0;
                    document.querySelectorAll('.notif-unread-dot').forEach(el => el.remove());
                    document.querySelectorAll('.notif-unread-bg').forEach(el => {
                        el.classList.remove('bg-teal-50/50', 'notif-unread-bg');
                        el.classList.add('bg-white');
                    });
                }
            });
        }
    }">
        <!-- Bell Trigger Button -->
        <button @click="notifOpen = !notifOpen" 
                type="button" 
                class="relative p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none focus:ring-2 focus:ring-brand-500/20"
                title="Pusat Notifikasi">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            
            <!-- Badge Counter -->
            <template x-if="unreadCount > 0">
                <span class="absolute -top-0.5 -right-0.5 flex h-4 min-w-[16px] px-1 items-center justify-center rounded-full bg-rose-600 text-[10px] font-black text-white shadow-xs">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                    <span class="relative" x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                </span>
            </template>
        </button>

        <!-- Dropdown Box -->
        <div x-show="notifOpen" 
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             @click.away="notifOpen = false"
             class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200/90 z-50 overflow-hidden divide-y divide-slate-100"
             style="display: none;">
            
            <!-- Dropdown Header -->
            <div class="px-4 py-3 bg-slate-50/80 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-black text-slate-900 tracking-tight">Notifikasi</span>
                    <template x-if="unreadCount > 0">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-teal-100 text-teal-800"
                              x-text="unreadCount + ' Baru'"></span>
                    </template>
                </div>

                <template x-if="unreadCount > 0">
                    <button @click="markAllRead()" 
                            type="button" 
                            class="text-[11px] font-semibold text-brand-600 hover:text-brand-700 transition hover:underline">
                        Tandai semua dibaca
                    </button>
                </template>
            </div>

            <!-- Notification Items List -->
            <div class="max-h-[380px] overflow-y-auto divide-y divide-slate-100">
                @forelse($latestNotifications as $notification)
                    @php
                        $data = $notification->data;
                        $cat = $data['category'] ?? 'system';
                        $isUnread = is_null($notification->read_at);

                        $catConfig = match($cat) {
                            'certificate' => ['icon' => '📜', 'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                            'course'      => ['icon' => '🎓', 'bg' => 'bg-blue-50 text-blue-700 border-blue-200'],
                            'webinar'     => ['icon' => '🎥', 'bg' => 'bg-teal-50 text-teal-700 border-teal-200'],
                            'evaluation'  => ['icon' => '📝', 'bg' => 'bg-amber-50 text-amber-700 border-amber-200'],
                            'approval'    => ['icon' => '🏛️', 'bg' => 'bg-purple-50 text-purple-700 border-purple-200'],
                            'profile'     => ['icon' => '👤', 'bg' => 'bg-sky-50 text-sky-700 border-sky-200'],
                            default       => ['icon' => '🔔', 'bg' => 'bg-slate-100 text-slate-700 border-slate-200'],
                        };
                    @endphp

                    <a href="{{ route('notifications.read', $notification->id) }}" 
                       class="flex items-start gap-3 p-3.5 hover:bg-slate-50 transition group {{ $isUnread ? 'bg-teal-50/40 notif-unread-bg' : 'bg-white' }}">
                        
                        <!-- Category Badge Icon -->
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 text-sm border {{ $catConfig['bg'] }}">
                            {{ $catConfig['icon'] }}
                        </div>

                        <!-- Notification Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1">
                                <p class="text-xs font-bold text-slate-900 group-hover:text-brand-600 transition truncate">
                                    {{ $data['title'] ?? 'Pemberitahuan' }}
                                </p>
                                @if($isUnread)
                                    <span class="w-2 h-2 rounded-full bg-teal-500 shrink-0 notif-unread-dot"></span>
                                @endif
                            </div>
                            <p class="text-[11px] text-slate-500 line-clamp-2 mt-0.5 leading-relaxed">
                                {{ $data['message'] ?? '' }}
                            </p>
                            <span class="text-[10px] text-slate-400 font-mono mt-1 block">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </a>
                @empty
                    <!-- Empty State -->
                    <div class="p-8 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-xl mb-2">
                            🔕
                        </div>
                        <p class="text-xs font-bold text-slate-700">Tidak ada notifikasi baru</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Semua info pembelajaran dan sistem akan muncul di sini</p>
                    </div>
                @endforelse
            </div>

            <!-- Dropdown Footer -->
            <div class="p-2.5 bg-slate-50/80 text-center">
                <a href="{{ route('notifications.index') }}" 
                   class="text-xs font-bold text-brand-600 hover:text-brand-700 transition hover:underline inline-flex items-center gap-1">
                    <span>Lihat Seluruh Riwayat Notifikasi</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

        </div>
    </div>
@endauth
