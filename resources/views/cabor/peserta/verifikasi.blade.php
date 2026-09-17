@extends('layouts.cabor')

@section('title', 'Verifikasi Peserta — ' . ($pendaftaran->atlet?->nama ?? $pendaftaran->timKontingen?->nama_tim))

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('cabor.peserta.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 transition-colors">&larr; Kembali ke Daftar Peserta</a>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white mt-1">Verifikasi Peserta</h1>
        </div>
        <span class="inline-block px-3 py-1 rounded-lg text-xs font-bold uppercase
            @if($pendaftaran->status === 'disetujui') bg-emerald-100 text-emerald-700
            @elseif($pendaftaran->status === 'ditolak') bg-rose-100 text-rose-700
            @else bg-amber-100 text-amber-700
            @endif">
            Status: {{ $pendaftaran->status }}
        </span>
    </div>

    {{-- Detail Atlet/Tim Card --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-4">
        <h2 class="text-sm font-bold text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-2">Biodata Peserta</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div>
                <span class="text-slate-400">Nama Peserta:</span>
                <p class="font-bold text-slate-800 dark:text-white text-sm mt-0.5">{{ $pendaftaran->atlet?->nama ?? $pendaftaran->timKontingen?->nama_tim }}</p>
            </div>
            <div>
                <span class="text-slate-400">Kontingen:</span>
                <p class="font-semibold text-slate-800 dark:text-white mt-0.5">{{ $pendaftaran->atlet?->kontingen?->nama ?? $pendaftaran->timKontingen?->kontingen?->nama }}</p>
            </div>
            <div>
                <span class="text-slate-400">Nomor Lomba:</span>
                <p class="font-semibold text-slate-800 dark:text-white mt-0.5">{{ $pendaftaran->nomorLomba?->nama }} ({{ $pendaftaran->nomorLomba?->gender }})</p>
            </div>
            @if($pendaftaran->atlet)
                <div>
                    <span class="text-slate-400">NIK & Tanggal Lahir:</span>
                    <p class="font-semibold text-slate-800 dark:text-white mt-0.5">{{ $pendaftaran->atlet->nik }} &bull; {{ $pendaftaran->atlet->tanggal_lahir?->format('d/m/Y') }} ({{ $pendaftaran->atlet->umur }} th)</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Berkas Atlet --}}
    @if($pendaftaran->atlet && $pendaftaran->atlet->berkas->isNotEmpty())
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-2">Berkas Persyaratan</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($pendaftaran->atlet->berkas as $berkas)
                    <div class="flex items-center justify-between p-3 border border-slate-200 dark:border-slate-800 rounded-xl">
                        <div>
                            <p class="text-xs font-bold text-slate-800 dark:text-white">{{ strtoupper(str_replace('_', ' ', $berkas->jenis_berkas)) }}</p>
                            <span class="text-[10px] text-slate-400">{{ $berkas->created_at?->format('d/m/Y') }}</span>
                        </div>
                        <a href="{{ asset('storage/' . $berkas->file_path) }}" target="_blank" class="px-2.5 py-1 text-xs font-semibold text-primary-600 bg-primary-50 rounded-lg hover:bg-primary-100">
                            Lihat Berkas
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Form Verifikasi --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <h2 class="text-sm font-bold text-slate-800 dark:text-white mb-4">Form Putusan Verifikasi</h2>
        <form action="{{ route('cabor.peserta.verifikasi.proses', $pendaftaran) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Putusan Status <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-2 p-3 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800">
                        <input type="radio" name="status" value="disetujui" {{ $pendaftaran->status === 'disetujui' ? 'checked' : '' }} required class="text-primary-600 focus:ring-primary-500">
                        <span class="text-xs font-bold text-emerald-600">Setujui / Absah</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800">
                        <input type="radio" name="status" value="ditolak" {{ $pendaftaran->status === 'ditolak' ? 'checked' : '' }} required class="text-primary-600 focus:ring-primary-500">
                        <span class="text-xs font-bold text-rose-600">Tolak Pendaftaran</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Catatan / Alasan Penolakan</label>
                <textarea name="catatan" rows="3" placeholder="Tuliskan catatan untuk kontingen (misal: berkas KTP kurang jelas)..." class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-3 text-slate-900 dark:text-white">{{ old('catatan', $pendaftaran->catatan) }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('cabor.peserta.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-100 rounded-xl">Batal</a>
                <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-slate-900 dark:bg-primary-600 hover:bg-slate-800 rounded-xl shadow-xs">
                    Simpan Verifikasi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
