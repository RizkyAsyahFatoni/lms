@extends('layouts.admin')
@section('title', 'Bank Soal')
@section('page_title', 'Bank Soal & Import Massal')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">📚 Bank Soal</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola koleksi soal yang dapat digunakan pada pre-test/post-test dan quiz modul pembelajaran</p>
        </div>
        <a href="{{ route('admin.question-banks.template') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 transition">
            ⬇️ Download Template CSV
        </a>
    </div>

    {{-- Create New Bank --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5">
        <h2 class="font-bold text-slate-900 mb-3">➕ Buat Bank Soal Baru</h2>
        <form action="{{ route('admin.question-banks.store') }}" method="POST" class="flex flex-wrap gap-3">
            @csrf
            <div class="flex-1 min-w-48">
                <input type="text" name="name" required placeholder="Nama Bank Soal (e.g., Soal Kepemimpinan 2026)"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div class="flex-1 min-w-40">
                <input type="text" name="category" placeholder="Kategori (opsional)"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-bold hover:bg-blue-700 transition">
                Buat Bank Soal
            </button>
        </form>
    </div>

    {{-- Daftar Bank Soal --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        @if($banks->isEmpty())
        <div class="text-center py-16">
            <div class="text-5xl mb-3">📂</div>
            <p class="text-slate-500 font-medium">Belum ada bank soal. Buat yang pertama di atas!</p>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-5">
            @foreach($banks as $bank)
            <div class="p-5 rounded-2xl border border-slate-200 hover:border-blue-300 hover:shadow-md transition group">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-xl shrink-0">📚</div>
                    <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-mono font-bold">{{ $bank->questions_count }} soal</span>
                </div>
                <h3 class="font-bold text-slate-900 line-clamp-2 group-hover:text-blue-700 transition leading-tight">{{ $bank->name }}</h3>
                @if($bank->category)
                <p class="text-xs text-slate-500 mt-1">{{ $bank->category }}</p>
                @endif
                <p class="text-xs text-slate-400 mt-2">Dibuat {{ $bank->created_at->diffForHumans() }}</p>

                <div class="flex gap-2 mt-4 pt-3 border-t border-slate-100">
                    <a href="{{ route('admin.question-banks.show', $bank) }}"
                       class="flex-1 text-center px-3 py-2 rounded-xl bg-blue-600 text-white text-xs font-bold hover:bg-blue-700 transition">
                        Kelola Soal →
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @if($banks->hasPages())
        <div class="p-4 border-t border-slate-100">{{ $banks->links() }}</div>
        @endif
        @endif
    </div>
</div>
@endsection
