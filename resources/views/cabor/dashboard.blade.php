@extends('layouts.cabor')

@section('title', 'Dashboard PJ Cabor — ' . $cabor->nama)

@section('content')
<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl flex items-center justify-center text-white font-bold text-lg shadow-sm" style="background-color: {{ $cabor->warna ?? '#2563EB' }}">
                {{ $cabor->singkatan }}
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white">{{ $cabor->nama }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">{{ $cabor->event?->nama ?? 'Pekan Olahraga' }} &bull; Durasi tanding standar: {{ $cabor->durasi_default_menit }} menit</p>
            </div>
        </div>

        @if($assignedCabor->count() > 1)
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400">Pilih Cabor:</span>
                <select onchange="window.location.href = '?cabor_id=' + this.value" class="text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 py-1.5 px-3">
                    @foreach($assignedCabor as $ac)
                        <option value="{{ $ac->id }}" {{ $cabor->id == $ac->id ? 'selected' : '' }}>
                            {{ $ac->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Nomor Lomba</span>
                <span class="p-2 bg-blue-50 dark:bg-blue-950/50 text-blue-600 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ $totalNomorLomba }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Kategori pertandingan</div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Total Peserta</span>
                <span class="p-2 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ $totalPendaftaran }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Atlet/Tim terdaftar</div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Perlu Verifikasi</span>
                <span class="p-2 bg-amber-50 dark:bg-amber-950/50 text-amber-600 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-amber-600">{{ $menungguVerifikasi }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Pendaftaran baru</div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Pertandingan Selesai</span>
                <span class="p-2 bg-purple-50 dark:bg-purple-950/50 text-purple-600 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ $pertandinganSelesai }} <span class="text-sm font-normal text-slate-400">/ {{ $totalPertandingan }}</span></div>
            <div class="text-[11px] text-slate-400 mt-1">Match selesai</div>
        </div>
    </div>

    {{-- Main Grid: Pending Verifications & Today Matches --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Pending Verifications Card --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Pendaftaran Butuh Verifikasi</h2>
                        <p class="text-xs text-slate-400">Periksa kesesuaian berkas atlet & kuota nomor lomba</p>
                    </div>
                    <a href="{{ route('cabor.peserta.index', ['status' => 'menunggu']) }}" class="text-xs font-semibold text-primary-600 hover:underline">
                        Lihat Semua &rarr;
                    </a>
                </div>

                @if($pendaftaranTerbaru->isEmpty())
                    <div class="p-8 text-center border border-dashed border-slate-200 dark:border-slate-800 rounded-xl">
                        <svg class="w-8 h-8 text-emerald-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <p class="text-xs font-medium text-slate-700 dark:text-slate-300">Semua pendaftaran telah diverifikasi!</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($pendaftaranTerbaru as $p)
                            <div class="py-3 flex items-center justify-between gap-3">
                                <div>
                                    <div class="text-xs font-bold text-slate-900 dark:text-white">
                                        {{ $p->atlet?->nama ?? $p->timKontingen?->nama_tim }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        {{ $p->nomorLomba?->nama }} &bull; {{ $p->atlet?->kontingen?->nama ?? $p->timKontingen?->kontingen?->nama }}
                                    </div>
                                </div>
                                <a href="{{ route('cabor.peserta.verifikasi', $p) }}" class="px-3 py-1.5 text-xs font-semibold text-white bg-slate-900 dark:bg-primary-600 hover:bg-slate-800 rounded-lg shadow-xs transition-colors">
                                    Verifikasi
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Match Schedule / Results Card --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Jadwal Pertandingan</h2>
                        <p class="text-xs text-slate-400">Daftar pertandingan terdekat & pengisian skor</p>
                    </div>
                    <a href="{{ route('cabor.jadwal.index') }}" class="text-xs font-semibold text-primary-600 hover:underline">
                        Lihat Semua &rarr;
                    </a>
                </div>

                @if($pertandinganHariIni->isEmpty())
                    <div class="p-8 text-center border border-dashed border-slate-200 dark:border-slate-800 rounded-xl">
                        <p class="text-xs text-slate-400">Belum ada pertandingan dijadwalkan.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($pertandinganHariIni as $m)
                            <div class="py-3 flex items-center justify-between gap-3">
                                <div>
                                    <div class="text-xs font-bold text-slate-900 dark:text-white">
                                        {{ $m->nomorLomba?->nama }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        {{ $m->tanggal?->format('d M') }} &bull; {{ $m->jam_mulai ? substr($m->jam_mulai, 0, 5) : 'TBD' }} &bull; {{ $m->lapangan?->nama ?? 'Lapangan TBD' }}
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                        @if($m->status === 'selesai') bg-purple-100 text-purple-700
                                        @elseif($m->status === 'berlangsung') bg-emerald-100 text-emerald-700
                                        @else bg-slate-100 text-slate-600
                                        @endif">
                                        {{ $m->status }}
                                    </span>
                                    <a href="{{ route('cabor.hasil.show', $m) }}" class="p-1.5 text-slate-500 hover:text-primary-600 hover:bg-slate-100 rounded-lg" title="Input Hasil">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
