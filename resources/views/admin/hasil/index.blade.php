@extends('layouts.admin')

@section('title', 'Hasil & Skor Pertandingan')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-800 font-medium">Hasil Pertandingan</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900">Hasil & Skor Pertandingan</h1>
            <p class="text-sm text-neutral-500 mt-1">Input skor laga, verifikasi pemenang babak, dan penetapan hasil resmi cabor.</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wider">Total Laga</p>
                    <p class="text-2xl font-bold text-neutral-900 mt-1">{{ $totalLaga }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="p-5 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wider">Selesai (Final)</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $totalSelesai }}</p>
                </div>
                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="p-5 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wider">Sedang Berlangsung</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ $totalBerlangsung }}</p>
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
        <form method="GET" action="{{ route('admin.hasil.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-neutral-700 mb-1.5">Cabang Olahraga</label>
                <select name="cabor_id" class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Cabor</option>
                    @foreach($caborList as $cabor)
                        <option value="{{ $cabor->id }}" {{ $caborId == $cabor->id ? 'selected' : '' }}>{{ $cabor->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-neutral-700 mb-1.5">Status Laga</label>
                <select name="status" class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status Aktif</option>
                    <option value="selesai" {{ $status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="berlangsung" {{ $status == 'berlangsung' ? 'selected' : '' }}>Sedang Berlangsung</option>
                    <option value="terjadwal" {{ $status == 'terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                    <option value="ditunda" {{ $status == 'ditunda' ? 'selected' : '' }}>Ditunda</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-neutral-700 mb-1.5">Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}"
                       class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-neutral-900 rounded-lg hover:bg-neutral-800 transition-colors">
                    Filter
                </button>
                @if($status || $caborId || $tanggal)
                    <a href="{{ route('admin.hasil.index') }}" class="px-3 py-2 text-sm font-medium text-neutral-600 bg-neutral-100 hover:bg-neutral-200 rounded-lg transition-colors">
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
                        <th scope="col" class="px-6 py-3.5">Cabor & Nomor</th>
                        <th scope="col" class="px-6 py-3.5">Babak & Jadwal</th>
                        <th scope="col" class="px-6 py-3.5">Pertandingan / Peserta</th>
                        <th scope="col" class="px-6 py-3.5">Skor / Hasil</th>
                        <th scope="col" class="px-6 py-3.5">Status</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200/80">
                    @forelse($pertandingan as $match)
                        <tr class="hover:bg-neutral-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-neutral-900">{{ $match->nomorLomba?->cabangOlahraga?->nama }}</div>
                                <div class="text-xs text-neutral-500 mt-0.5">{{ $match->nomorLomba?->nama }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-neutral-100 text-neutral-800 mb-1">
                                    {{ strtoupper($match->babak) }}
                                </span>
                                <div class="text-neutral-500">{{ $match->tanggal ? $match->tanggal->format('d/m/Y') : '-' }} &bull; {{ substr($match->waktu_mulai, 0, 5) }} WIB</div>
                                <div class="text-[11px] text-neutral-400">{{ $match->lapangan?->nama }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    @forelse($match->pesertaPertandingan as $peserta)
                                        <div class="flex items-center justify-between text-xs {{ $peserta->hasil === 'menang' ? 'font-bold text-blue-700' : 'text-neutral-700' }}">
                                            <span>{{ $peserta->nama }}</span>
                                            @if($peserta->skor !== null)
                                                <span class="font-mono bg-neutral-100 px-1.5 py-0.2 rounded">{{ $peserta->skor }}</span>
                                            @elseif($peserta->catatan_waktu !== null)
                                                <span class="font-mono text-neutral-500">{{ $peserta->catatan_waktu }}s</span>
                                            @endif
                                        </div>
                                    @empty
                                        <span class="text-xs text-neutral-400 italic">Peserta belum dialokasikan</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($match->hasilPertandingan?->pemenang)
                                    <div class="text-xs font-semibold text-emerald-700">
                                        🏆 {{ $match->hasilPertandingan->pemenang->nama }}
                                    </div>
                                @elseif($match->status === 'selesai')
                                    <span class="text-xs text-neutral-500">Selesai</span>
                                @else
                                    <span class="text-xs text-neutral-400 italic">Belum ada hasil</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($match->status === 'selesai')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Selesai
                                    </span>
                                @elseif($match->status === 'berlangsung')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                        Live
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-neutral-100 text-neutral-600">
                                        {{ ucfirst($match->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <a href="{{ route('admin.hasil.show', $match) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Input / Edit Skor
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-neutral-400">
                                <p class="text-base font-medium text-neutral-700">Tidak ada data pertandingan</p>
                                <p class="text-xs text-neutral-400 mt-1">Gunakan modul Drawing / Bracket untuk membuat bagan dan jadwal pertandingan terlebih dahulu.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pertandingan->hasPages())
            <div class="px-6 py-4 border-t border-neutral-200/80">
                {{ $pertandingan->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
