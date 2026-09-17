@extends('layouts.admin')

@section('title', 'Detail Rundown - ' . \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y'))

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <a href="{{ route('admin.rundown.index') }}" class="hover:text-neutral-800">Rundown</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-800 font-medium">{{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.rundown.index') }}" class="p-1 text-neutral-400 hover:text-neutral-700 rounded-lg hover:bg-neutral-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-neutral-900">
                    Rundown {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
                </h1>
            </div>
            <p class="text-sm text-neutral-500 mt-1 pl-7">Susunan jadwal acara kegiatan dan pertandingan pada tanggal ini.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.rundown.export-pdf', ['tanggal' => $tanggal]) }}" target="_blank"
               class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors">
                <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak / PDF
            </a>
            <a href="{{ route('admin.acara-rundown.create', ['tanggal' => $tanggal]) }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Agenda
            </a>
        </div>
    </div>

    <!-- Timeline Timeline Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Agenda Acara Non-Tanding -->
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold text-neutral-900 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                    Agenda Acara & Seremonial
                </h2>
                <span class="text-xs text-neutral-500">{{ $acaraList->count() }} Kegiatan</span>
            </div>

            <div class="bg-white border border-neutral-200/80 rounded-xl shadow-xs p-6 space-y-6">
                @forelse($acaraList as $item)
                    <div class="relative pl-6 pb-6 last:pb-0 border-l-2 border-neutral-200 last:border-l-transparent">
                        <span class="absolute -left-[9px] top-0 w-4 h-4 rounded-full border-2 border-white bg-blue-600 shadow-xs"></span>
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-neutral-100 text-neutral-700">
                                    {{ substr($item->waktu_mulai, 0, 5) }} - {{ $item->waktu_selesai ? substr($item->waktu_selesai, 0, 5) : 'Selesai' }} WIB
                                </span>
                                <h3 class="text-base font-bold text-neutral-900 mt-1.5">{{ $item->judul }}</h3>
                                @if($item->catatan)
                                    <p class="text-sm text-neutral-600 mt-1">{{ $item->catatan }}</p>
                                @endif
                                <div class="flex flex-wrap items-center gap-4 text-xs text-neutral-500 mt-2">
                                    @if($item->lokasi)
                                        <div class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            </svg>
                                            <span>{{ $item->lokasi }}</span>
                                        </div>
                                    @endif
                                    @if($item->penanggung_jawab)
                                        <div class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span>PIC: {{ $item->penanggung_jawab }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.acara-rundown.edit', $item->id) }}"
                                   class="p-1 text-neutral-400 hover:text-amber-600 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-neutral-400">
                        <p class="text-sm">Tidak ada agenda seremonial pada tanggal ini.</p>
                        <a href="{{ route('admin.acara-rundown.create', ['tanggal' => $tanggal]) }}" class="text-xs text-blue-600 hover:underline mt-1 inline-block">
                            + Tambah agenda
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Pertandingan pada Tanggal Tersebut -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold text-neutral-900 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    Jadwal Pertandingan
                </h2>
                <span class="text-xs text-neutral-500">{{ $pertandinganList->count() }} Laga</span>
            </div>

            <div class="bg-white border border-neutral-200/80 rounded-xl shadow-xs divide-y divide-neutral-200/80 max-h-[600px] overflow-y-auto">
                @forelse($pertandinganList as $match)
                    <div class="p-4 hover:bg-neutral-50/50 transition-colors">
                        <div class="flex items-center justify-between text-xs text-neutral-500 mb-1">
                            <span class="font-semibold text-blue-600">{{ $match->nomorLomba?->cabangOlahraga?->nama }}</span>
                            <span>{{ $match->waktu_mulai ? \Carbon\Carbon::parse($match->waktu_mulai)->format('H:i') : '-' }} WIB</span>
                        </div>
                        <div class="text-xs font-medium text-neutral-800">{{ $match->nomorLomba?->nama }} ({{ $match->babak }})</div>
                        <div class="text-xs text-neutral-500 mt-1 flex items-center gap-1">
                            <span>{{ $match->lapangan?->venue?->nama }} - {{ $match->lapangan?->nama }}</span>
                        </div>
                        <div class="mt-2 text-xs font-semibold text-neutral-700 bg-neutral-50 p-2 rounded border border-neutral-100">
                            @if($match->peserta->count() >= 2)
                                {{ $match->peserta[0]->atlet?->nama ?? $match->peserta[0]->timKontingen?->nama ?? 'TBD' }}
                                <span class="text-neutral-400 font-normal">vs</span>
                                {{ $match->peserta[1]->atlet?->nama ?? $match->peserta[1]->timKontingen?->nama ?? 'TBD' }}
                            @else
                                <span class="text-neutral-400 italic">Peserta belum ditentukan</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-neutral-400">
                        <p class="text-sm">Tidak ada jadwal pertandingan pada tanggal ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
