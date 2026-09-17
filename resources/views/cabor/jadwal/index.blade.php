@extends('layouts.cabor')

@section('title', 'Jadwal Pertandingan — ' . $cabor->nama)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Jadwal & Hasil Pertandingan</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola slot tanding, status match, dan input skor hasil untuk cabor {{ $cabor->nama }}.</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm">
        <form method="GET" action="{{ route('cabor.jadwal.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="w-full sm:w-48">
                <select name="nomor_lomba_id" onchange="this.form.submit()" class="w-full text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2">
                    <option value="">Semua Nomor Lomba</option>
                    @foreach($cabor->nomorLomba as $nl)
                        <option value="{{ $nl->id }}" {{ request('nomor_lomba_id') == $nl->id ? 'selected' : '' }}>
                            {{ $nl->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-40">
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" onchange="this.form.submit()" class="w-full text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2">
            </div>
            <div class="w-full sm:w-40">
                <select name="status" onchange="this.form.submit()" class="w-full text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2">
                    <option value="">Semua Status</option>
                    <option value="terjadwal" {{ request('status') == 'terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                    <option value="berlangsung" {{ request('status') == 'berlangsung' ? 'selected' : '' }}>Berlangsung</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="ditunda" {{ request('status') == 'ditunda' ? 'selected' : '' }}>Ditunda</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 rounded-lg">
                Filter
            </button>
            @if(request()->hasAny(['nomor_lomba_id', 'tanggal', 'status']))
                <a href="{{ route('cabor.jadwal.index') }}" class="px-3 py-2 text-xs font-semibold text-rose-600 bg-rose-50 rounded-lg flex items-center justify-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Matches List --}}
    @if($pertandingan->isEmpty())
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-12 text-center shadow-sm">
            <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum ada pertandingan</p>
            <p class="text-xs text-slate-400 mt-1">Jadwal pertandingan yang dibuat melalui drawing bagan akan muncul di sini.</p>
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-semibold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-4 py-3">Pertandingan / Babak</th>
                            <th class="px-4 py-3">Waktu & Lapangan</th>
                            <th class="px-4 py-3">Peserta & Skor</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($pertandingan as $m)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $m->nomorLomba?->nama }}</div>
                                    <div class="text-[10px] text-slate-400">Match #{{ $m->nomor_pertandingan ?? $m->id }} &bull; Babak {{ ucfirst(str_replace('_', ' ', $m->babak)) }}</div>
                                </td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                    <div>{{ $m->tanggal?->format('d M Y') ?? 'Tanggal TBD' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $m->jam_mulai ? substr($m->jam_mulai, 0, 5) : 'TBD' }} &bull; {{ $m->lapangan?->nama ?? 'Lapangan TBD' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($m->peserta->isNotEmpty())
                                        <div class="space-y-1">
                                            @foreach($m->peserta as $p)
                                                <div class="flex items-center justify-between gap-4 {{ $p->hasil === 'menang' ? 'font-bold text-emerald-600 dark:text-emerald-400' : 'text-slate-700 dark:text-slate-300' }}">
                                                    <span>{{ $p->peserta?->nama ?? $p->peserta?->nama_tim ?? 'TBD' }} ({{ $p->peserta?->kontingen?->nama ?? '-' }})</span>
                                                    <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 font-mono text-xs">{{ $p->skor ?? '-' }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic">Peserta belum ditentukan</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2.5 py-0.5 rounded text-[10px] font-bold uppercase
                                        @if($m->status === 'selesai') bg-purple-100 text-purple-700
                                        @elseif($m->status === 'berlangsung') bg-emerald-100 text-emerald-700
                                        @elseif($m->status === 'ditunda') bg-amber-100 text-amber-700
                                        @else bg-slate-100 text-slate-600
                                        @endif">
                                        {{ $m->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('cabor.hasil.show', $m) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white bg-slate-900 dark:bg-primary-600 hover:bg-slate-800 rounded-lg shadow-xs transition-colors">
                                        Input Skor & Hasil
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($pertandingan->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                    {{ $pertandingan->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
