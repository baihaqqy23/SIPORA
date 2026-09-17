@extends('layouts.admin')

@section('title', $venue->nama)

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <a href="{{ route('admin.venue.index') }}" class="hover:text-neutral-800">Venue</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-800 font-medium">{{ $venue->nama }}</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.venue.index') }}" class="p-1 text-neutral-400 hover:text-neutral-700 rounded-lg hover:bg-neutral-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-neutral-900">{{ $venue->nama }}</h1>
            </div>
            <p class="text-sm text-neutral-500 mt-1 pl-7">
                Event: {{ $venue->event?->nama }} &bull; Jam Operasional: {{ substr($venue->jam_operasional_mulai, 0, 5) }} - {{ substr($venue->jam_operasional_selesai, 0, 5) }} WIB
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.venue.edit', $venue) }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors">
                <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Venue
            </a>
            <form action="{{ route('admin.venue.destroy', $venue) }}" method="POST"
                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus venue {{ $venue->nama }}?')">
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

    <!-- Overview Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Info Card -->
        <div class="bg-white border border-neutral-200/80 rounded-xl shadow-xs p-5 space-y-4">
            <h2 class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">Informasi Fasilitas</h2>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-neutral-400 text-xs block">Alamat:</span>
                    <span class="text-neutral-800 font-medium">{{ $venue->alamat ?: 'Tidak ada alamat tercatat' }}</span>
                </div>
                <div>
                    <span class="text-neutral-400 text-xs block">Kapasitas Penonton:</span>
                    <span class="text-neutral-800 font-medium">{{ $venue->kapasitas ? number_format($venue->kapasitas) . ' Orang' : '-' }}</span>
                </div>
                <div>
                    <span class="text-neutral-400 text-xs block">Cabang Olahraga yang Dilayani:</span>
                    <div class="flex flex-wrap gap-1 mt-1">
                        @forelse($venue->cabangOlahraga as $cabor)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">
                                {{ $cabor->nama }}
                            </span>
                        @empty
                            <span class="text-neutral-400 text-xs italic">Belum dihubungkan ke cabor manapun</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Lapangan Card -->
        <div class="lg:col-span-2 bg-white border border-neutral-200/80 rounded-xl shadow-xs p-5 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-neutral-900">Daftar Lapangan / Court / Matras</h2>
                    <p class="text-xs text-neutral-500">Kelola sub-lapangan dalam venue ini untuk penjadwalan pertandingan.</p>
                </div>
            </div>

            <!-- Form Tambah Lapangan Cepat -->
            <form action="{{ route('admin.venue.lapangan.store', $venue) }}" method="POST" class="flex gap-2">
                @csrf
                <input type="text" name="nama" required placeholder="Nama Lapangan Baru (misal: Lapangan 3 / Meja 2)"
                       class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 whitespace-nowrap">
                    + Tambah
                </button>
            </form>

            <!-- Table Lapangan -->
            <div class="border border-neutral-200/80 rounded-lg overflow-hidden">
                <table class="w-full text-left text-sm text-neutral-600">
                    <thead class="bg-neutral-50 text-xs font-semibold text-neutral-500 uppercase">
                        <tr>
                            <th class="px-4 py-2.5">No</th>
                            <th class="px-4 py-2.5">Nama Lapangan / Area</th>
                            <th class="px-4 py-2.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200/80">
                        @forelse($venue->lapangan as $index => $lap)
                            <tr class="hover:bg-neutral-50/50">
                                <td class="px-4 py-3 text-xs text-neutral-400 w-12">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-semibold text-neutral-900">
                                    <form id="edit-lapangan-{{ $lap->id }}" action="{{ route('admin.lapangan.update', $lap) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="nama" value="{{ $lap->nama }}"
                                               class="text-sm py-1 px-2 border-transparent hover:border-neutral-300 focus:border-blue-500 rounded bg-transparent focus:bg-white w-full max-w-xs transition-colors">
                                        <button type="submit" title="Simpan Nama" class="text-xs text-blue-600 hover:text-blue-800 p-1">
                                            Simpan
                                        </button>
                                    </form>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <form action="{{ route('admin.lapangan.destroy', $lap) }}" method="POST"
                                          onsubmit="return confirm('Hapus lapangan {{ $lap->nama }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-500 hover:text-red-700">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-neutral-400 text-xs">
                                    Belum ada lapangan yang terdaftar di venue ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
