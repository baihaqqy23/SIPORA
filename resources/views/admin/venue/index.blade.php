@extends('layouts.admin')

@section('title', 'Data Venue & Lapangan')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-800 font-medium">Venue</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900">Venue & Arena Pertandingan</h1>
            <p class="text-sm text-neutral-500 mt-1">Kelola lokasi venue, stadion, GOR, dan alokasi lapangan cabor.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.venue.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Venue
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="p-5 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wider">Total Venue</p>
                    <p class="text-2xl font-bold text-neutral-900 mt-1">{{ $totalVenues }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="p-5 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wider">Total Lapangan / Court</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $totalLapangan }}</p>
                </div>
                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="p-4 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
        <form method="GET" action="{{ route('admin.venue.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium text-neutral-700 mb-1.5">Cari Venue</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Nama venue, alamat..."
                           class="w-full text-sm pl-9 border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                    <svg class="w-4 h-4 text-neutral-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-neutral-700 mb-1.5">Event</label>
                <select name="event_id" class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Event</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}" {{ $eventId == $event->id ? 'selected' : '' }}>{{ $event->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-neutral-900 rounded-lg hover:bg-neutral-800 transition-colors">
                    Filter
                </button>
                @if($search || $eventId)
                    <a href="{{ route('admin.venue.index') }}" class="px-3 py-2 text-sm font-medium text-neutral-600 bg-neutral-100 hover:bg-neutral-200 rounded-lg transition-colors">
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
                        <th scope="col" class="px-6 py-3.5">Venue & Alamat</th>
                        <th scope="col" class="px-6 py-3.5">Event</th>
                        <th scope="col" class="px-6 py-3.5">Kapasitas</th>
                        <th scope="col" class="px-6 py-3.5">Jam Operasional</th>
                        <th scope="col" class="px-6 py-3.5">Lapangan</th>
                        <th scope="col" class="px-6 py-3.5">Cabor</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200/80">
                    @forelse($venues as $venue)
                        <tr class="hover:bg-neutral-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.venue.show', $venue) }}" class="font-semibold text-neutral-900 hover:text-blue-600">
                                    {{ $venue->nama }}
                                </a>
                                @if($venue->alamat)
                                    <div class="text-xs text-neutral-500 mt-0.5 line-clamp-1">{{ $venue->alamat }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-neutral-600">
                                {{ $venue->event?->nama }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-neutral-700 font-medium">
                                {{ $venue->kapasitas ? number_format($venue->kapasitas) . ' Penonton' : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-neutral-600">
                                {{ substr($venue->jam_operasional_mulai, 0, 5) }} - {{ substr($venue->jam_operasional_selesai, 0, 5) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">
                                    {{ $venue->lapangan_count }} Lapangan
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1 max-w-xs">
                                    @forelse($venue->cabangOlahraga as $cabor)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-medium bg-neutral-100 text-neutral-700">
                                            {{ $cabor->singkatan ?: $cabor->nama }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-neutral-400">-</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.venue.show', $venue) }}"
                                       class="p-1.5 text-neutral-500 hover:text-blue-600 hover:bg-neutral-100 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.venue.edit', $venue) }}"
                                       class="p-1.5 text-neutral-500 hover:text-amber-600 hover:bg-neutral-100 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.venue.destroy', $venue) }}" method="POST"
                                          onsubmit="return confirm('Hapus venue {{ $venue->nama }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
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
                            <td colspan="7" class="px-6 py-12 text-center text-neutral-400">
                                <p class="text-base font-medium text-neutral-700">Belum ada venue pertandingan</p>
                                <a href="{{ route('admin.venue.create') }}" class="mt-3 inline-block px-4 py-2 text-xs font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                    + Tambah Venue Pertama
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($venues->hasPages())
            <div class="px-6 py-4 border-t border-neutral-200/80">
                {{ $venues->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
