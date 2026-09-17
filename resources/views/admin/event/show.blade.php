@extends('layouts.admin')

@section('title', $event->nama)

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <a href="{{ route('admin.event.index') }}" class="hover:text-neutral-800">Event</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-800 font-medium">{{ $event->nama }}</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.event.index') }}" class="p-1 text-neutral-400 hover:text-neutral-700 rounded-lg hover:bg-neutral-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            @if($event->logo_path)
                <img src="{{ Storage::url($event->logo_path) }}" alt="{{ $event->nama }}" class="w-14 h-14 rounded-xl object-cover border border-neutral-200">
            @else
                <div class="w-14 h-14 rounded-xl bg-blue-600 text-white font-black flex items-center justify-center text-lg shadow-sm">
                    POR
                </div>
            @endif
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold tracking-tight text-neutral-900">{{ $event->nama }}</h1>
                    @if($event->status === 'berlangsung')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Berlangsung
                        </span>
                    @elseif($event->status === 'pendaftaran_dibuka')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                            Pendaftaran Dibuka
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-neutral-100 text-neutral-600">
                            {{ ucfirst(str_replace('_', ' ', $event->status)) }}
                        </span>
                    @endif
                </div>
                <p class="text-sm text-neutral-500 mt-0.5">
                    {{ $event->tanggal_mulai ? $event->tanggal_mulai->translatedFormat('d F Y') : '-' }} s/d {{ $event->tanggal_selesai ? $event->tanggal_selesai->translatedFormat('d F Y') : '-' }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.event.edit', $event) }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors">
                <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Event
            </a>
            <form action="{{ route('admin.event.destroy', $event) }}" method="POST"
                  onsubmit="return confirm('Hapus event {{ $event->nama }}?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <!-- Stats Ringkasan Event -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-5 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
            <p class="text-xs font-medium text-neutral-500 uppercase">Cabang Olahraga</p>
            <p class="text-2xl font-bold text-neutral-900 mt-1">{{ $event->cabang_olahraga_count }}</p>
        </div>
        <div class="p-5 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
            <p class="text-xs font-medium text-neutral-500 uppercase">Kontingen Daerah</p>
            <p class="text-2xl font-bold text-neutral-900 mt-1">{{ $event->kontingen_count }}</p>
        </div>
        <div class="p-5 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
            <p class="text-xs font-medium text-neutral-500 uppercase">Venue Pertandingan</p>
            <p class="text-2xl font-bold text-neutral-900 mt-1">{{ $event->venues_count }}</p>
        </div>
        <div class="p-5 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
            <p class="text-xs font-medium text-neutral-500 uppercase">Panitia Pelaksana</p>
            <p class="text-2xl font-bold text-neutral-900 mt-1">{{ $event->panitia_count }}</p>
        </div>
    </div>

    <!-- Detail Info & Konfigurasi -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white border border-neutral-200/80 rounded-xl shadow-xs p-5 space-y-4">
            <h2 class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">Konfigurasi Jadwal & Usia</h2>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-neutral-400 text-xs block">Deskripsi:</span>
                    <p class="text-neutral-800">{{ $event->deskripsi ?: 'Tidak ada deskripsi' }}</p>
                </div>
                <div>
                    <span class="text-neutral-400 text-xs block">Periode Pendaftaran:</span>
                    <span class="text-neutral-800 font-medium">
                        {{ $event->pendaftaran_mulai ? $event->pendaftaran_mulai->format('d/m/Y H:i') : '-' }} s/d {{ $event->pendaftaran_selesai ? $event->pendaftaran_selesai->format('d/m/Y H:i') : '-' }}
                    </span>
                </div>
                <div>
                    <span class="text-neutral-400 text-xs block">Tanggal Patokan Usia Atlet:</span>
                    <span class="text-neutral-800 font-medium">{{ $event->tanggal_patokan_umur ? $event->tanggal_patokan_umur->translatedFormat('d F Y') : '-' }}</span>
                </div>
                <div>
                    <span class="text-neutral-400 text-xs block">Maksimal Nomor Lomba per Atlet:</span>
                    <span class="text-neutral-800 font-medium">{{ $event->maks_nomor_lomba_per_atlet ?? 3 }} Nomor</span>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 bg-white border border-neutral-200/80 rounded-xl shadow-xs p-5 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-neutral-900">Cabang Olahraga pada Event Ini</h2>
                <a href="{{ route('admin.cabor.create', ['event_id' => $event->id]) }}" class="text-xs font-medium text-blue-600 hover:underline">
                    + Tambah Cabor
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @forelse($event->cabangOlahraga as $cabor)
                    <div class="p-3 border border-neutral-200 rounded-lg flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full" style="background-color: {{ $cabor->warna ?? '#2563eb' }}"></span>
                            <div>
                                <a href="{{ route('admin.cabor.show', $cabor) }}" class="text-xs font-bold text-neutral-900 hover:text-blue-600">
                                    {{ $cabor->nama }}
                                </a>
                                <div class="text-[11px] text-neutral-400">{{ $cabor->singkatan ?: '' }}</div>
                            </div>
                        </div>
                        <a href="{{ route('admin.cabor.show', $cabor) }}" class="text-xs text-neutral-400 hover:text-neutral-700">
                            Detail &rarr;
                        </a>
                    </div>
                @empty
                    <p class="text-xs text-neutral-400 col-span-2 py-4">Belum ada cabang olahraga yang didaftarkan pada event ini.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
