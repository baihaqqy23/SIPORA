@extends('layouts.publik')

@section('title', 'Profil Kontingen: ' . $kontingen->nama)

@section('content')
<div class="space-y-8">
    {{-- Header Kontingen --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 md:p-8 shadow-sm">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-red-600 to-red-800 text-xl font-bold text-white shadow-md">
                    {{ strtoupper(substr($kontingen->nama, 0, 2)) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">{{ $kontingen->nama }}</h1>
                    <p class="text-sm text-slate-500">{{ $kontingen->kota }}, {{ $kontingen->provinsi }}</p>
                </div>
            </div>

            {{-- Medali Summary --}}
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 rounded-xl bg-amber-50 border border-amber-200 px-4 py-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-amber-400 text-xs font-black text-white">🥇</span>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-amber-800">Emas</p>
                        <p class="text-lg font-extrabold text-amber-900">{{ $medaliCount['emas'] ?? 0 }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 rounded-xl bg-slate-100 border border-slate-300 px-4 py-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-300 text-xs font-black text-slate-700">🥈</span>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-700">Perak</p>
                        <p class="text-lg font-extrabold text-slate-800">{{ $medaliCount['perak'] ?? 0 }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 rounded-xl bg-amber-900/10 border border-amber-900/20 px-4 py-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-amber-700 text-xs font-black text-white">🥉</span>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-amber-900">Perunggu</p>
                        <p class="text-lg font-extrabold text-amber-950">{{ $medaliCount['perunggu'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar Atlet --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-base font-bold text-slate-800 mb-4">Daftar Atlet Kontingen ({{ $kontingen->atlet->count() }})</h2>
        @if($kontingen->atlet->isEmpty())
            <p class="text-sm text-slate-400 py-4 text-center">Belum ada data atlet terdaftar.</p>
        @else
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($kontingen->atlet as $atlet)
                    <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50/50 p-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-200 text-xs font-bold text-slate-600">
                            {{ $atlet->gender === 'L' ? 'PA' : 'PI' }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-800 truncate">{{ $atlet->nama }}</p>
                            <p class="text-xs text-slate-500">{{ $atlet->asal_kota }} &bull; {{ $atlet->gender === 'L' ? 'Putra' : 'Putri' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Riwayat Pertandingan --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-base font-bold text-slate-800 mb-4">Jadwal & Hasil Pertandingan</h2>
        @if($pertandingan->isEmpty())
            <p class="text-sm text-slate-400 py-4 text-center">Belum ada pertandingan terjadwal untuk kontingen ini.</p>
        @else
            <div class="space-y-3">
                @foreach($pertandingan as $p)
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-xl border border-slate-100 p-4 hover:bg-slate-50 transition">
                        <div class="space-y-1">
                            <span class="text-[11px] font-semibold text-red-700 uppercase tracking-wider">
                                {{ $p->nomorLomba?->cabangOlahraga?->nama }}
                            </span>
                            <p class="text-sm font-bold text-slate-800">{{ $p->nomorLomba?->nama }} - {{ $p->babak }}</p>
                            <p class="text-xs text-slate-500">{{ $p->tanggal?->translatedFormat('d M Y') }} &bull; {{ $p->lapangan?->nama }} ({{ $p->lapangan?->venue?->nama }})</p>
                        </div>
                        <div class="sm:text-right">
                            <span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $p->status === 'selesai' ? 'bg-green-100 text-green-800' : ($p->status === 'berlangsung' ? 'bg-amber-100 text-amber-800 animate-pulse' : 'bg-slate-100 text-slate-600') }}">
                                {{ ucfirst($p->status) }}
                            </span>
                            @if($p->hasilPertandingan)
                                <p class="text-xs text-slate-600 font-medium mt-1">{{ $p->hasilPertandingan->keterangan }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
