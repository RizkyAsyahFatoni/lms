@extends('layouts.app')

@section('title', 'Kalender Pembelajaran | Corpu Berau')
@section('meta_description', 'Kalender kegiatan pembelajaran ASN Kabupaten Berau - E-Course, Webinar, Diklat Tatap Muka, dan Bimtek.')

@section('head_extra')
    {{-- FullCalendar v6 --}}
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js"></script>

    <style>
        /* FullCalendar custom theming */
        .fc .fc-toolbar-title { font-weight: 800; font-size: 1.25rem; color: #0f172a; }
        .fc .fc-button { font-family: inherit !important; font-weight: 700 !important; font-size: 0.75rem !important; }
        .fc .fc-button-primary { background-color: #1e293b !important; border-color: #1e293b !important; }
        .fc .fc-button-primary:hover { background-color: #334155 !important; }
        .fc .fc-button-primary:not(:disabled).fc-button-active { background-color: #0ea5e9 !important; border-color: #0ea5e9 !important; }
        .fc-event { border-radius: 6px !important; padding: 2px 4px !important; cursor: pointer; font-size: 0.72rem !important; font-weight: 600 !important; }
        .fc-day-today { background-color: #eff6ff !important; }
        .fc .fc-daygrid-day-number { font-size: 0.8rem; font-weight: 700; color: #475569; }
        .fc .fc-col-header-cell-cushion { font-size: 0.75rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }

        /* Event tooltip */
        .event-tooltip {
            position: fixed;
            z-index: 9999;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 16px;
            max-width: 320px;
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.15);
            pointer-events: none;
            transition: opacity 0.15s ease;
        }
    </style>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900">📅 Kalender Pembelajaran</h1>
            <p class="text-slate-500 mt-1">Semua kegiatan diklat, webinar, dan kursus ASN Kabupaten Berau</p>
        </div>
        <div class="flex items-center gap-3">
            @auth
            <span class="px-3 py-1.5 rounded-xl bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200">
                ✅ Tampil Sesuai OPD: {{ auth()->user()->opd_name ?: 'Publik' }}
            </span>
            @endauth
            <a href="{{ route('courses.index') }}" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-bold hover:bg-slate-700 transition">
                Katalog Kursus
            </a>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white rounded-2xl border border-slate-200 p-4 text-center shadow-xs">
            <div class="text-3xl font-black text-slate-900">{{ $stats['upcoming_courses'] }}</div>
            <div class="text-xs text-slate-500 font-medium mt-1">Kursus Aktif</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 text-center shadow-xs">
            <div class="text-3xl font-black text-purple-700">{{ $stats['upcoming_webinars'] }}</div>
            <div class="text-xs text-slate-500 font-medium mt-1">Webinar Mendatang</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 text-center col-span-2">
            {{-- Legenda Warna --}}
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Keterangan Warna</p>
            <div class="flex flex-wrap gap-2 justify-center">
                @foreach([
                    ['label' => 'E-Course', 'color' => '#0ea5e9'],
                    ['label' => 'Webinar', 'color' => '#8b5cf6'],
                    ['label' => 'Tatap Muka', 'color' => '#f97316'],
                    ['label' => 'Bimtek', 'color' => '#ef4444'],
                    ['label' => 'Workshop', 'color' => '#f59e0b'],
                    ['label' => 'Seminar', 'color' => '#10b981'],
                    ['label' => 'Hybrid', 'color' => '#ec4899'],
                ] as $leg)
                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700">
                    <span class="w-3 h-3 rounded-full" style="background-color: {{ $leg['color'] }}"></span>
                    {{ $leg['label'] }}
                </span>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Filter Controls --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs flex flex-wrap gap-3 items-center">
        <div class="flex items-center gap-2">
            <label class="text-xs font-bold text-slate-600">Jenis:</label>
            <select id="filter-type" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="all">Semua Kegiatan</option>
                <option value="course">Kursus / Diklat</option>
                <option value="webinar">Webinar Live</option>
            </select>
        </div>
        @auth
        @if($opdList->count() > 0)
        <div class="flex items-center gap-2">
            <label class="text-xs font-bold text-slate-600">OPD:</label>
            <select id="filter-opd" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">Semua OPD</option>
                @foreach($opdList as $opd)
                <option value="{{ $opd }}" {{ auth()->user()->opd_name === $opd ? 'selected' : '' }}>{{ $opd }}</option>
                @endforeach
            </select>
        </div>
        @endif
        @endauth
        <button id="btn-refresh" class="px-4 py-2 rounded-xl bg-blue-600 text-white text-xs font-bold hover:bg-blue-700 transition">
            🔄 Perbarui
        </button>
    </div>

    {{-- Kalender Utama --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-4 sm:p-6">
        <div id="calendar"></div>
    </div>

</div>

{{-- Event Tooltip --}}
<div id="event-tooltip" class="event-tooltip hidden">
    <div id="tooltip-type" class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1"></div>
    <div id="tooltip-title" class="font-bold text-slate-900 text-sm leading-tight mb-2"></div>
    <div id="tooltip-details" class="text-xs text-slate-600 space-y-1"></div>
    <div id="tooltip-quota" class="mt-2"></div>
    <div id="tooltip-desc" class="text-xs text-slate-500 mt-2 leading-relaxed line-clamp-3 border-t border-slate-100 pt-2"></div>
    <div class="mt-3 pt-2 border-t border-slate-100">
        <p class="text-xs text-blue-600 font-bold">Klik untuk detail & daftar →</p>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const tooltip = document.getElementById('event-tooltip');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listMonth'
        },
        buttonText: {
            today: 'Hari Ini',
            month: 'Bulan',
            week: 'Minggu',
            list: 'Daftar',
        },
        height: 'auto',
        events: function (fetchInfo, successCallback, failureCallback) {
            const type = document.getElementById('filter-type')?.value || 'all';
            const opd = document.getElementById('filter-opd')?.value || '';

            fetch(`{{ route('calendar.events') }}?start=${fetchInfo.startStr}&end=${fetchInfo.endStr}&type=${type}&opd=${encodeURIComponent(opd)}`)
                .then(r => r.json())
                .then(data => successCallback(data))
                .catch(() => failureCallback('Gagal memuat data kalender.'));
        },
        eventClick: function (info) {
            if (info.event.url) {
                window.open(info.event.url, '_blank');
                info.jsEvent.preventDefault();
            }
        },
        eventMouseEnter: function (info) {
            const props = info.event.extendedProps;

            document.getElementById('tooltip-type').textContent = props.type === 'webinar' ? '🎙️ Webinar Live' : `📚 ${props.course_type || 'Kursus'}`;
            document.getElementById('tooltip-title').textContent = info.event.title.replace(/^[🟢🔴] /, '');

            const details = [];
            if (props.jp) details.push(`⏱️ ${props.jp} JP`);
            if (props.instructor || props.speaker) details.push(`👨‍🏫 ${props.instructor || props.speaker}`);
            if (props.location) details.push(`📍 ${props.location}`);
            if (props.time_start) details.push(`🕒 ${props.time_start}${props.time_end ? ' – ' + props.time_end : ''}`);
            if (props.opd) details.push(`🏛️ ${props.opd}`);

            document.getElementById('tooltip-details').innerHTML = details.map(d => `<div>${d}</div>`).join('');

            const quotaEl = document.getElementById('tooltip-quota');
            if (props.quota) {
                const remaining = props.remaining ?? props.quota;
                const isFull = props.is_full;
                quotaEl.innerHTML = `<span class="px-2 py-1 rounded-lg text-xs font-bold ${isFull ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700'}">
                    ${isFull ? '🔴 Kuota Penuh' : `🟢 Sisa ${remaining} dari ${props.quota} kursi`}
                </span>`;
            } else {
                quotaEl.innerHTML = '';
            }

            document.getElementById('tooltip-desc').textContent = props.description || '';

            tooltip.classList.remove('hidden');
        },
        eventMouseLeave: function () {
            tooltip.classList.add('hidden');
        },
        eventDidMount: function (info) {
            info.el.addEventListener('mousemove', function (e) {
                tooltip.style.left = (e.clientX + 12) + 'px';
                tooltip.style.top = (e.clientY - 10) + 'px';
                const rect = tooltip.getBoundingClientRect();
                if (rect.right > window.innerWidth) {
                    tooltip.style.left = (e.clientX - rect.width - 12) + 'px';
                }
                if (rect.bottom > window.innerHeight) {
                    tooltip.style.top = (e.clientY - rect.height - 10) + 'px';
                }
            });
        },
    });

    calendar.render();

    // Filter controls
    document.getElementById('btn-refresh').addEventListener('click', () => calendar.refetchEvents());
    document.getElementById('filter-type')?.addEventListener('change', () => calendar.refetchEvents());
    document.getElementById('filter-opd')?.addEventListener('change', () => calendar.refetchEvents());
});
</script>
@endsection
