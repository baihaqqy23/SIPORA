@extends('layouts.cabor')

@section('title', 'Bagan Drawing Bracket — ' . $nomorLomba->nama)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('cabor.dashboard') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 transition-colors">&larr; Kembali ke Dashboard</a>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white mt-1">Bagan Pertandingan: {{ $nomorLomba->nama }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Format: {{ str_replace('_', ' ', ucfirst($nomorLomba->format_pertandingan)) }} &bull; Status Bracket: {{ ucfirst($nomorLomba->status_bracket) }}</p>
        </div>

        @if($nomorLomba->status_bracket !== 'terkunci')
            <form action="{{ route('cabor.bracket.simpan', $nomorLomba) }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-white bg-slate-900 dark:bg-primary-600 hover:bg-slate-800 rounded-xl shadow-xs transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Generate & Kunci Bagan
                </button>
            </form>
        @endif
    </div>

    {{-- Bracket Matches Tree / List --}}
    @if($pertandingan->isEmpty())
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-12 text-center shadow-sm">
            <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Bagan belum di-generate</p>
            <p class="text-xs text-slate-400 mt-1">Klik tombol Generate untuk membuat bagan sistem gugur otomatis.</p>
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @php
                    $matchesByBabak = $pertandingan->groupBy('babak');
                @endphp
                @foreach($matchesByBabak as $babakName => $matches)
                    <div class="space-y-4">
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100 dark:border-slate-800 pb-2">
                            Babak {{ ucfirst(str_replace('_', ' ', $babakName)) }}
                        </div>
                        <div class="space-y-3">
                            @foreach($matches as $m)
                                <div class="p-3.5 border border-slate-200 dark:border-slate-800 rounded-xl bg-slate-50/50 dark:bg-slate-800/40 space-y-2">
                                    <div class="flex items-center justify-between text-[10px] text-slate-400">
                                        <span>Match #{{ $m->nomor_pertandingan ?? $m->id }}</span>
                                        <span class="font-bold uppercase {{ $m->status === 'selesai' ? 'text-purple-600' : 'text-slate-500' }}">{{ $m->status }}</span>
                                    </div>
                                    <div class="space-y-1">
                                        @foreach($m->peserta as $p)
                                            <div class="flex items-center justify-between text-xs {{ $p->hasil === 'menang' ? 'font-bold text-emerald-600' : 'text-slate-700 dark:text-slate-300' }}">
                                                <span class="truncate">{{ $p->peserta?->nama ?? $p->peserta?->nama_tim ?? 'TBD' }}</span>
                                                <span class="font-mono">{{ $p->skor ?? '-' }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="pt-1 text-right">
                                        <a href="{{ route('cabor.hasil.show', $m) }}" class="text-[11px] font-semibold text-primary-600 hover:underline">
                                            Input Hasil &rarr;
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
