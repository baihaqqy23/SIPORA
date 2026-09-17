@extends('layouts.admin')

@section('title', $cabor->nama)

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <a href="{{ route('admin.cabor.index') }}" class="hover:text-neutral-800">Cabor</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-400">{{ $cabor->singkatan }}</span>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl flex items-center justify-center text-white font-bold text-sm shadow-sm" style="background-color: {{ $cabor->warna ?? '#C8102E' }}">
                {{ $cabor->singkatan }}
            </div>
            <div>
                <h1 class="text-lg font-semibold text-neutral-800">{{ $cabor->nama }}</h1>
                <p class="text-xs text-neutral-500 mt-0.5">{{ $cabor->event?->nama ?? 'Event tidak ditemukan' }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.cabor.nomor-lomba.create', $cabor) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-neutral-900 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-neutral-800 transition-colors">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Tambah Nomor Lomba
            </a>
            <a href="{{ route('admin.cabor.edit', $cabor) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-neutral-200 bg-white px-3.5 py-2 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 transition-colors">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/></svg>
                Edit
            </a>
        </div>
    </div>

    {{-- Detail Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-xl border border-neutral-200 bg-white p-4">
            <p class="text-[11px] font-semibold text-neutral-400 uppercase tracking-wider">Durasi Default</p>
            <p class="text-xl font-bold text-neutral-900 mt-1">{{ $cabor->durasi_default_menit }} <span class="text-sm font-normal text-neutral-500">menit</span></p>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-4">
            <p class="text-[11px] font-semibold text-neutral-400 uppercase tracking-wider">Jeda Antar Tanding</p>
            <p class="text-xl font-bold text-neutral-900 mt-1">{{ $cabor->jeda_antar_tanding_menit }} <span class="text-sm font-normal text-neutral-500">menit</span></p>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-4">
            <p class="text-[11px] font-semibold text-neutral-400 uppercase tracking-wider">Jumlah Nomor Lomba</p>
            <p class="text-xl font-bold text-neutral-900 mt-1">{{ $cabor->nomorLomba->count() }}</p>
        </div>
    </div>

    {{-- Info Tambahan --}}
    @if($cabor->deskripsi || $cabor->venues->isNotEmpty())
    <div class="rounded-xl border border-neutral-200 bg-white p-5 space-y-3">
        @if($cabor->deskripsi)
            <div>
                <p class="text-[11px] font-semibold text-neutral-400 uppercase tracking-wider mb-1">Deskripsi</p>
                <p class="text-sm text-neutral-700">{{ $cabor->deskripsi }}</p>
            </div>
        @endif
        @if($cabor->venues->isNotEmpty())
            <div>
                <p class="text-[11px] font-semibold text-neutral-400 uppercase tracking-wider mb-1">Venue Pertandingan</p>
                <div class="flex flex-wrap gap-2 mt-1">
                    @foreach($cabor->venues as $venue)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-neutral-100 text-neutral-700 border border-neutral-200">
                            <svg class="h-3.5 w-3.5 text-neutral-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                            {{ $venue->nama }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    @endif

    {{-- Nomor Lomba Table --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-semibold text-neutral-800">Daftar Nomor Lomba</h2>
        </div>

        @if($cabor->nomorLomba->isEmpty())
            <div class="rounded-2xl border border-neutral-200 bg-white p-12 text-center">
                <p class="text-base font-semibold text-neutral-600">Belum ada nomor lomba</p>
                <p class="text-xs text-neutral-400 mt-1">Tambahkan nomor lomba untuk cabor ini.</p>
                <a href="{{ route('admin.cabor.nomor-lomba.create', $cabor) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-neutral-900 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-neutral-800 transition-colors mt-4">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                    Tambah Nomor Lomba
                </a>
            </div>
        @else
            <div class="rounded-2xl border border-neutral-200 bg-white shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-neutral-50 border-b border-neutral-200 text-neutral-600 font-semibold uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="px-4 py-3">Nama Nomor Lomba</th>
                                <th class="px-4 py-3">Gender</th>
                                <th class="px-4 py-3">Jenis</th>
                                <th class="px-4 py-3">Format</th>
                                <th class="px-4 py-3 text-center">Kuota/Kontingen</th>
                                <th class="px-4 py-3">Umur</th>
                                <th class="px-4 py-3">Bracket</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @foreach($cabor->nomorLomba as $nl)
                                <tr class="hover:bg-neutral-50/70 transition">
                                    <td class="px-4 py-3 font-bold text-neutral-800 text-sm">{{ $nl->nama }}</td>
                                    <td class="px-4 py-3">
                                        @php
                                            $genderColor = match($nl->gender) {
                                                'putra' => 'bg-blue-100 text-blue-800',
                                                'putri' => 'bg-pink-100 text-pink-800',
                                                'campuran' => 'bg-purple-100 text-purple-800',
                                                default => 'bg-neutral-100 text-neutral-800',
                                            };
                                        @endphp
                                        <span class="inline-block rounded px-2 py-0.5 text-[10px] font-bold {{ $genderColor }} uppercase">{{ $nl->gender }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-neutral-600 capitalize">
                                        {{ $nl->jenis }}
                                        @if($nl->jenis === 'beregu' && $nl->jumlah_anggota)
                                            <span class="text-neutral-400">({{ $nl->jumlah_anggota }} org)</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-neutral-600">
                                        {{ str_replace('_', ' ', ucfirst($nl->format_pertandingan)) }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-neutral-700 font-semibold">{{ $nl->kuota_per_kontingen ?? '-' }}</td>
                                    <td class="px-4 py-3 text-neutral-600">
                                        @if($nl->umur_min || $nl->umur_maks)
                                            {{ $nl->umur_min ?? '?' }}-{{ $nl->umur_maks ?? '?' }} th
                                        @else
                                            <span class="text-neutral-400">Umum</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @php
                                            $bracketColor = match($nl->status_bracket) {
                                                'tergenerate' => 'bg-amber-100 text-amber-800',
                                                'terkunci' => 'bg-emerald-100 text-emerald-800',
                                                default => 'bg-neutral-100 text-neutral-600',
                                            };
                                        @endphp
                                        <span class="inline-block rounded px-2 py-0.5 text-[10px] font-bold {{ $bracketColor }} capitalize">{{ $nl->status_bracket }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <a href="{{ route('admin.nomor-lomba.edit', $nl) }}" class="rounded-lg p-1.5 text-neutral-500 hover:bg-neutral-100 hover:text-neutral-900 transition-colors" title="Edit">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/></svg>
                                            </a>
                                            <form method="POST" action="{{ route('admin.nomor-lomba.destroy', $nl) }}" onsubmit="return confirm('Hapus nomor lomba {{ $nl->nama }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-lg p-1.5 text-neutral-400 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
