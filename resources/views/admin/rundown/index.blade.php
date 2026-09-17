@extends('layouts.admin')

@section('title', 'Jadwal & Rundown Acara')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-800 font-medium">Rundown</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900">Rundown Acara & Kegiatan</h1>
            <p class="text-sm text-neutral-500 mt-1">Kelola susunan acara seremonial, technical meeting, dan agenda event.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.acara-rundown.create', ['tanggal' => $tanggal]) }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Agenda Acara
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="p-5 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wider">Total Agenda</p>
                    <p class="text-2xl font-bold text-neutral-900 mt-1">{{ $totalAcara }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="p-5 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wider">Dipublikasikan</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $totalPublikasi }}</p>
                </div>
                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="p-5 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wider">Draft / Internal</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ $totalAcara - $totalPublikasi }}</p>
                </div>
                <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="p-4 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
        <form method="GET" action="{{ route('admin.rundown.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-xs font-medium text-neutral-700 mb-1.5">Filter Event</label>
                <select name="event_id" class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Event</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}" {{ $selectedEventId == $event->id ? 'selected' : '' }}>
                            {{ $event->nama }} {{ $event->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-neutral-700 mb-1.5">Filter Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}"
                       class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-neutral-900 rounded-lg hover:bg-neutral-800 transition-colors">
                    Terapkan Filter
                </button>
                @if($tanggal || $selectedEventId)
                    <a href="{{ route('admin.rundown.index') }}" class="px-3 py-2 text-sm font-medium text-neutral-600 bg-neutral-100 hover:bg-neutral-200 rounded-lg transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white border border-neutral-200/80 rounded-xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-neutral-600">
                <thead class="bg-neutral-50/80 text-xs font-semibold text-neutral-500 uppercase tracking-wider border-b border-neutral-200/80">
                    <tr>
                        <th scope="col" class="px-6 py-3.5">Waktu & Tanggal</th>
                        <th scope="col" class="px-6 py-3.5">Agenda / Kegiatan</th>
                        <th scope="col" class="px-6 py-3.5">Lokasi</th>
                        <th scope="col" class="px-6 py-3.5">Penanggung Jawab</th>
                        <th scope="col" class="px-6 py-3.5">Status</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200/80">
                    @forelse($rundownList as $item)
                        <tr class="hover:bg-neutral-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-neutral-900">
                                    {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l, d M Y') }}
                                </div>
                                <div class="text-xs text-neutral-500 mt-0.5">
                                    {{ substr($item->waktu_mulai, 0, 5) }}
                                    @if($item->waktu_selesai)
                                        - {{ substr($item->waktu_selesai, 0, 5) }} WIB
                                    @else
                                        WIB - Selesai
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-neutral-900">{{ $item->judul }}</div>
                                @if($item->catatan)
                                    <div class="text-xs text-neutral-500 mt-1 line-clamp-1">{{ $item->catatan }}</div>
                                @endif
                                <div class="text-[11px] text-neutral-400 mt-0.5">{{ $item->event?->nama }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-1.5 text-neutral-700">
                                    <svg class="w-4 h-4 text-neutral-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>{{ $item->lokasi ?: '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-neutral-700 font-medium">{{ $item->penanggung_jawab ?: '-' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->dipublikasikan)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Publik
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-neutral-100 text-neutral-600 border border-neutral-200">
                                        Internal (Draft)
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.rundown.show', ['tanggal' => $item->tanggal->format('Y-m-d')]) }}"
                                       title="Lihat Detail Tanggal"
                                       class="p-1.5 text-neutral-500 hover:text-blue-600 hover:bg-neutral-100 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.acara-rundown.edit', $item->id) }}"
                                       title="Edit Agenda"
                                       class="p-1.5 text-neutral-500 hover:text-amber-600 hover:bg-neutral-100 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.acara-rundown.destroy', $item->id) }}" method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus agenda acara ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                title="Hapus Agenda"
                                                class="p-1.5 text-neutral-500 hover:text-red-600 hover:bg-neutral-100 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-neutral-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-neutral-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-base font-medium text-neutral-700">Belum ada agenda rundown</p>
                                    <p class="text-xs text-neutral-400 mt-1 max-w-sm">Mulai tambahkan kegiatan seremonial, technical meeting, atau pembukaan untuk event olahraga Anda.</p>
                                    <a href="{{ route('admin.acara-rundown.create', ['tanggal' => $tanggal]) }}" class="mt-4 px-4 py-2 text-xs font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                        + Tambah Agenda Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rundownList->hasPages())
            <div class="px-6 py-4 border-t border-neutral-200/80">
                {{ $rundownList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
