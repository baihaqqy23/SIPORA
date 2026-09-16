@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('breadcrumb')
    <span class="text-slate-400">Dashboard</span>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-lg font-semibold text-slate-800">Dashboard Admin Dispora</h1>
        <p class="text-sm text-slate-500 mt-0.5">
            {{ $event ? 'Event Aktif: ' . $event->nama : 'Belum ada event aktif.' }}
        </p>
    </div>

    {{-- Konflik Alert --}}
    @if($totalKonflikKeras > 0)
    <div class="rounded-xl bg-red-50 border border-red-200 p-4 flex items-start gap-3">
        <svg class="h-5 w-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.962-.833-2.732 0L3.07 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
        <div>
            <p class="text-sm font-semibold text-red-700">{{ $totalKonflikKeras }} Konflik Jadwal Keras Belum Diselesaikan</p>
            <p class="text-xs text-red-600 mt-0.5">Jadwal tidak bisa dipublikasikan sampai konflik ini diatasi.</p>
            <a href="{{ route('admin.papan-jadwal.index') }}" class="text-xs font-medium text-red-700 underline mt-1 inline-block">Lihat Papan Jadwal →</a>
        </div>
    </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <p class="text-xs text-slate-500 font-medium">Atlet Terdaftar</p>
            <p class="text-2xl font-bold text-slate-800 mt-1 tabular-nums">{{ number_format($stats['atlet_terdaftar']) }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ number_format($stats['atlet_terverifikasi']) }} terverifikasi</p>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <p class="text-xs text-slate-500 font-medium">Menunggu Verifikasi</p>
            <p class="text-2xl font-bold {{ $stats['menunggu_verifikasi'] > 0 ? 'text-amber-600' : 'text-slate-800' }} mt-1 tabular-nums">{{ $stats['menunggu_verifikasi'] }}</p>
            <a href="{{ route('admin.verifikasi.index') }}" class="text-xs text-brand hover:underline mt-1 inline-block">Lihat antrean →</a>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <p class="text-xs text-slate-500 font-medium">Pertandingan Hari Ini</p>
            <p class="text-2xl font-bold text-slate-800 mt-1 tabular-nums">{{ $stats['pertandingan_hari_ini'] }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ today()->isoFormat('dddd, D MMM Y') }}</p>
        </div>
        <div class="rounded-xl border p-5 {{ $totalKonflikKeras > 0 ? 'bg-red-50 border-red-200' : 'bg-white border-slate-200' }}">
            <p class="text-xs {{ $totalKonflikKeras > 0 ? 'text-red-500' : 'text-slate-500' }} font-medium">Konflik Jadwal Aktif</p>
            <p class="text-2xl font-bold {{ $totalKonflikKeras > 0 ? 'text-red-600' : 'text-slate-800' }} mt-1 tabular-nums">{{ $totalKonflikKeras }}</p>
            <a href="{{ route('admin.papan-jadwal.index') }}" class="text-xs {{ $totalKonflikKeras > 0 ? 'text-red-600' : 'text-brand' }} hover:underline mt-1 inline-block">Papan Jadwal →</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Pertandingan Hari Ini --}}
        <div class="lg:col-span-2 rounded-xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-700">Pertandingan Hari Ini</h2>
                <a href="{{ route('admin.papan-jadwal.index') }}" class="text-xs text-brand hover:underline">Lihat semua</a>
            </div>
            @if($pertandinganHariIni->count())
            <div class="divide-y divide-slate-50">
                {{-- Desktop table --}}
                <table class="hidden sm:table w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-500">Jam</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-500">Pertandingan</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-500">Venue</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($pertandinganHariIni as $p)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-xs font-mono text-slate-600 tabular-nums">
                                {{ $p->waktu_mulai ? substr($p->waktu_mulai, 0, 5) : '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-xs font-medium text-slate-800">{{ $p->nomorLomba->nama ?? '—' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $p->nomorLomba->cabangOlahraga->nama ?? '' }} • {{ $p->babak }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600">{{ $p->lapangan->venue->nama ?? '—' }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$p->status" /></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{-- Mobile card list --}}
                <div class="sm:hidden divide-y divide-slate-100">
                    @foreach($pertandinganHariIni as $p)
                    <div class="px-4 py-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono text-slate-600">{{ $p->waktu_mulai ? substr($p->waktu_mulai, 0, 5) : '—' }}</span>
                            <x-status-badge :status="$p->status" />
                        </div>
                        <div class="mt-1 text-xs font-medium text-slate-800">{{ $p->nomorLomba->nama ?? '—' }}</div>
                        <div class="text-[11px] text-slate-400">{{ $p->lapangan->venue->nama ?? '—' }} • {{ $p->babak }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <x-empty-state title="Tidak ada pertandingan hari ini" description="Jadwal pertandingan untuk hari ini belum ditetapkan." />
            @endif
        </div>

        {{-- Klasemen Mini --}}
        <div class="rounded-xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-700">Top 10 Medali</h2>
                <a href="{{ route('admin.klasemen.index') }}" class="text-xs text-brand hover:underline">Lengkap</a>
            </div>
            @if($klasemenTop10->count())
            <div class="divide-y divide-slate-50">
                @foreach($klasemenTop10 as $row)
                <div class="flex items-center gap-3 px-4 py-3">
                    <span class="text-xs font-bold text-slate-400 w-5 tabular-nums">{{ $row['peringkat'] }}</span>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-medium text-slate-800 truncate">{{ $row['kontingen_nama'] }}</div>
                        <div class="text-[11px] text-slate-400">{{ $row['provinsi'] }}</div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0 text-xs tabular-nums">
                        <span class="font-bold text-amber-600">{{ $row['emas'] }}</span>
                        <span class="text-slate-400">/</span>
                        <span class="font-semibold text-slate-500">{{ $row['perak'] }}</span>
                        <span class="text-slate-400">/</span>
                        <span class="font-semibold text-amber-800">{{ $row['perunggu'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <x-empty-state title="Belum ada medali" description="Hasil pertandingan yang menghasilkan medali akan tampil di sini." />
            @endif
        </div>
    </div>
</div>
@endsection
