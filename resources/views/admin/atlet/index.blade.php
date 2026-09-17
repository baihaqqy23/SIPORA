@extends('layouts.admin')

@section('title', 'Data Atlet')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-800 font-medium">Atlet</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900">Database Atlet & Peserta</h1>
            <p class="text-sm text-neutral-500 mt-1">Daftar seluruh atlet peserta pekan olahraga dari semua kontingen daerah.</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wider">Total Atlet</p>
                    <p class="text-2xl font-bold text-neutral-900 mt-1">{{ $totalAtlet }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="p-5 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wider">Terverifikasi (Sah)</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $totalTerverifikasi }}</p>
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
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wider">Menunggu / Draft</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ $totalMenunggu }}</p>
                </div>
                <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="p-4 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
        <form method="GET" action="{{ route('admin.atlet.index') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-3">
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-neutral-700 mb-1.5">Cari Atlet</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Nama atlet, kota..."
                           class="w-full text-sm pl-9 border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                    <svg class="w-4 h-4 text-neutral-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-neutral-700 mb-1.5">Kontingen</label>
                <select name="kontingen_id" class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Kontingen</option>
                    @foreach($kontingens as $k)
                        <option value="{{ $k->id }}" {{ $kontingenId == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-neutral-700 mb-1.5">Status</label>
                <select name="status" class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="terverifikasi" {{ $status == 'terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
                    <option value="menunggu" {{ $status == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="draft" {{ $status == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="ditolak" {{ $status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="perlu_perbaikan" {{ $status == 'perlu_perbaikan' ? 'selected' : '' }}>Perlu Perbaikan</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-neutral-900 rounded-lg hover:bg-neutral-800 transition-colors">
                    Filter
                </button>
                @if($search || $kontingenId || $status || $gender)
                    <a href="{{ route('admin.atlet.index') }}" class="px-3 py-2 text-sm font-medium text-neutral-600 bg-neutral-100 hover:bg-neutral-200 rounded-lg transition-colors">
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
                        <th scope="col" class="px-6 py-3.5">Atlet</th>
                        <th scope="col" class="px-6 py-3.5">Kontingen Daerah</th>
                        <th scope="col" class="px-6 py-3.5">Gender / Usia</th>
                        <th scope="col" class="px-6 py-3.5">Berkas & Dokumen</th>
                        <th scope="col" class="px-6 py-3.5">Status</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200/80">
                    @forelse($atlets as $atlet)
                        <tr class="hover:bg-neutral-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($atlet->foto_path)
                                        <img src="{{ Storage::url($atlet->foto_path) }}" alt="{{ $atlet->nama }}" class="w-10 h-10 rounded-full object-cover border border-neutral-200">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-neutral-100 text-neutral-600 font-bold flex items-center justify-center text-xs">
                                            {{ strtoupper(substr($atlet->nama, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('admin.atlet.show', $atlet) }}" class="font-semibold text-neutral-900 hover:text-blue-600">
                                            {{ $atlet->nama }}
                                        </a>
                                        <div class="text-xs text-neutral-400 mt-0.5">Lahir: {{ $atlet->tanggal_lahir ? $atlet->tanggal_lahir->format('d/m/Y') : '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-neutral-800 font-medium">
                                {{ $atlet->kontingen?->nama }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-neutral-600">
                                {{ $atlet->gender === 'L' ? 'Laki-laki' : 'Perempuan' }} &bull; {{ $atlet->hitungUmurPada() }} tahun
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-neutral-100 text-neutral-700">
                                    {{ $atlet->berkas->count() }} Dokumen
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($atlet->status === 'terverifikasi')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Terverifikasi
                                    </span>
                                @elseif($atlet->status === 'menunggu')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                        Menunggu
                                    </span>
                                @elseif($atlet->status === 'ditolak')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200">
                                        Ditolak
                                    </span>
                                @elseif($atlet->status === 'perlu_perbaikan')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200">
                                        Perlu Perbaikan
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-neutral-100 text-neutral-600 border border-neutral-200">
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.atlet.show', $atlet) }}"
                                       class="p-1.5 text-neutral-500 hover:text-blue-600 hover:bg-neutral-100 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.atlet.edit', $atlet) }}"
                                       class="p-1.5 text-neutral-500 hover:text-amber-600 hover:bg-neutral-100 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.atlet.destroy', $atlet) }}" method="POST"
                                          onsubmit="return confirm('Hapus data atlet {{ $atlet->nama }}?')">
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
                            <td colspan="6" class="px-6 py-12 text-center text-neutral-400">
                                <p class="text-base font-medium text-neutral-700">Tidak ada data atlet</p>
                                <p class="text-xs text-neutral-400 mt-1">Atlet didaftarkan oleh masing-masing ofisial kontingen daerah.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($atlets->hasPages())
            <div class="px-6 py-4 border-t border-neutral-200/80">
                {{ $atlets->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
