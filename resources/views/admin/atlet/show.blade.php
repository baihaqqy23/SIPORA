@extends('layouts.admin')

@section('title', 'Detail Atlet - ' . $atlet->nama)

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <a href="{{ route('admin.atlet.index') }}" class="hover:text-neutral-800">Atlet</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-800 font-medium">{{ $atlet->nama }}</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.atlet.index') }}" class="p-1 text-neutral-400 hover:text-neutral-700 rounded-lg hover:bg-neutral-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            @if($atlet->foto_path)
                <img src="{{ Storage::url($atlet->foto_path) }}" alt="{{ $atlet->nama }}" class="w-14 h-14 rounded-full object-cover border border-neutral-200 shadow-xs">
            @else
                <div class="w-14 h-14 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-lg">
                    {{ strtoupper(substr($atlet->nama, 0, 2)) }}
                </div>
            @endif
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold tracking-tight text-neutral-900">{{ $atlet->nama }}</h1>
                    @if($atlet->status === 'terverifikasi')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Terverifikasi
                        </span>
                    @elseif($atlet->status === 'menunggu')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                            Menunggu Verifikasi
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-neutral-100 text-neutral-600">
                            {{ ucfirst($atlet->status) }}
                        </span>
                    @endif
                </div>
                <p class="text-sm text-neutral-500 mt-0.5">
                    Kontingen: {{ $atlet->kontingen?->nama }} &bull; {{ $atlet->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}, {{ $atlet->hitungUmurPada() }} tahun
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.atlet.edit', $atlet) }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors">
                <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Data Atlet
            </a>
            <form action="{{ route('admin.atlet.destroy', $atlet) }}" method="POST"
                  onsubmit="return confirm('Hapus data atlet {{ $atlet->nama }}?')">
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

    <!-- Grid Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Info Profil -->
        <div class="bg-white border border-neutral-200/80 rounded-xl shadow-xs p-5 space-y-4">
            <h2 class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">Biodata & Identitas</h2>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-neutral-400 text-xs block">Nama Lengkap:</span>
                    <span class="text-neutral-800 font-medium">{{ $atlet->nama }}</span>
                </div>
                <div>
                    <span class="text-neutral-400 text-xs block">Tanggal Lahir:</span>
                    <span class="text-neutral-800 font-medium">
                        {{ $atlet->tanggal_lahir ? $atlet->tanggal_lahir->translatedFormat('d F Y') : '-' }}
                        ({{ $atlet->hitungUmurPada() }} tahun)
                    </span>
                </div>
                <div>
                    <span class="text-neutral-400 text-xs block">Kota Kelahiran / Asal:</span>
                    <span class="text-neutral-800 font-medium">{{ $atlet->asal_kota ?: '-' }}</span>
                </div>
                <div>
                    <span class="text-neutral-400 text-xs block">NIK:</span>
                    <span class="text-neutral-800 font-medium">{{ $atlet->nik ? substr($atlet->nik, 0, 6) . '******' . substr($atlet->nik, -4) : '-' }}</span>
                </div>
                @if($atlet->catatan_status)
                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-800">
                        <span class="font-semibold block mb-0.5">Catatan Verifikasi:</span>
                        {{ $atlet->catatan_status }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Berkas & Pendaftaran -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Dokumen / Berkas Atlet -->
            <div class="bg-white border border-neutral-200/80 rounded-xl shadow-xs p-5 space-y-4">
                <h2 class="text-base font-bold text-neutral-900">Berkas Verifikasi Atlet</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @forelse($atlet->berkas as $berkas)
                        <div class="p-3 border border-neutral-200 rounded-lg flex items-center justify-between">
                            <div>
                                <span class="font-medium text-neutral-800 text-xs block">{{ strtoupper(str_replace('_', ' ', $berkas->jenis_berkas)) }}</span>
                                <span class="text-[11px] text-neutral-400">{{ $berkas->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <a href="{{ Storage::url($berkas->file_path) }}" target="_blank"
                               class="px-2.5 py-1 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded">
                                Buka File &rarr;
                            </a>
                        </div>
                    @empty
                        <p class="text-xs text-neutral-400 col-span-2 py-3">Belum ada berkas yang diunggah untuk atlet ini.</p>
                    @endforelse
                </div>
            </div>

            <!-- Riwayat Pertandingan & Nomor Lomba -->
            <div class="bg-white border border-neutral-200/80 rounded-xl shadow-xs overflow-hidden">
                <div class="p-4 border-b border-neutral-200/80">
                    <h2 class="text-base font-bold text-neutral-900">Nomor Pertandingan yang Diikuti</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-neutral-600">
                        <thead class="bg-neutral-50 text-xs font-semibold text-neutral-500 uppercase">
                            <tr>
                                <th class="px-4 py-2.5">Cabor & Nomor Lomba</th>
                                <th class="px-4 py-2.5">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200/80">
                            @forelse($atlet->pendaftaran as $pend)
                                <tr class="hover:bg-neutral-50/50">
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-neutral-900">{{ $pend->nomorLomba?->cabangOlahraga?->nama }}</div>
                                        <div class="text-xs text-neutral-500">{{ $pend->nomorLomba?->nama }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $pend->status === 'disetujui' ? 'bg-emerald-50 text-emerald-700' : 'bg-neutral-100 text-neutral-700' }}">
                                            {{ ucfirst($pend->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-6 text-center text-xs text-neutral-400">
                                        Belum terdaftar pada nomor lomba manapun.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
