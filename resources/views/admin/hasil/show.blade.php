@extends('layouts.admin')

@section('title', 'Input Hasil Pertandingan #' . $pertandingan->id)

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <a href="{{ route('admin.hasil.index') }}" class="hover:text-neutral-800">Hasil</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-800 font-medium">Laga #{{ $pertandingan->id }}</span>
@endsection

@section('content')
<div class="max-w-4xl space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.hasil.index') }}" class="p-1 text-neutral-400 hover:text-neutral-700 rounded-lg hover:bg-neutral-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-neutral-900">
                    {{ $pertandingan->nomorLomba?->cabangOlahraga?->nama }} - {{ $pertandingan->nomorLomba?->nama }}
                </h1>
            </div>
            <p class="text-sm text-neutral-500 mt-1 pl-7">
                Babak: <span class="font-semibold text-neutral-800">{{ strtoupper($pertandingan->babak) }}</span> &bull;
                Jadwal: {{ $pertandingan->tanggal ? $pertandingan->tanggal->format('d/m/Y') : '-' }} ({{ substr($pertandingan->waktu_mulai, 0, 5) }} WIB) &bull;
                Venue: {{ $pertandingan->lapangan?->venue?->nama }} - {{ $pertandingan->lapangan?->nama }}
            </p>
        </div>

        @if($pertandingan->hasilPertandingan)
            <form action="{{ route('admin.hasil.batalkan', $pertandingan) }}" method="POST"
                  onsubmit="return confirm('Apakah Anda yakin ingin membatalkan dan mereset hasil pertandingan ini?')">
                @csrf
                <button type="submit"
                        class="px-3.5 py-2 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                    Batalkan Hasil
                </button>
            </form>
        @endif
    </div>

    <!-- Form Input Hasil & Skor -->
    <form action="{{ route('admin.hasil.simpan', $pertandingan) }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white border border-neutral-200/80 rounded-xl shadow-xs overflow-hidden">
            <div class="p-4 bg-neutral-50 border-b border-neutral-200/80">
                <h2 class="text-sm font-bold text-neutral-900">Input Skor & Data Peserta Laga</h2>
                <p class="text-xs text-neutral-500">Masukkan perolehan skor atau catatan waktu bagi tiap peserta.</p>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($pertandingan->pesertaPertandingan as $index => $peserta)
                        <div class="p-5 border-2 {{ $peserta->hasil === 'menang' ? 'border-blue-500 bg-blue-50/20' : 'border-neutral-200' }} rounded-xl space-y-4">
                            <input type="hidden" name="peserta[{{ $index }}][id]" value="{{ $peserta->id }}">

                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider text-neutral-400">Peserta / Sudut #{{ $peserta->slot ?: ($index + 1) }}</span>
                                @if($peserta->kontingen)
                                    <span class="text-xs font-medium px-2 py-0.5 rounded bg-neutral-100 text-neutral-700">
                                        {{ $peserta->kontingen->nama }}
                                    </span>
                                @endif
                            </div>

                            <div>
                                <h3 class="text-lg font-bold text-neutral-900">{{ $peserta->nama }}</h3>
                                <p class="text-xs text-neutral-500">{{ $peserta->peserta?->asal_kota ?: '-' }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-3 pt-2">
                                <div>
                                    <label class="block text-xs font-semibold text-neutral-700 uppercase mb-1">Skor Poin</label>
                                    <input type="number" name="peserta[{{ $index }}][skor]" value="{{ old("peserta.{$index}.skor", $peserta->skor) }}"
                                           placeholder="0"
                                           class="w-full text-base font-bold font-mono text-neutral-900 border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-neutral-700 uppercase mb-1">Waktu / Nilai</label>
                                    <input type="text" name="peserta[{{ $index }}][nilai]" value="{{ old("peserta.{$index}.nilai", $peserta->nilai ?? $peserta->catatan_waktu) }}"
                                           placeholder="Contoh: 10.55"
                                           class="w-full text-sm font-mono text-neutral-900 border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-6 text-neutral-400 text-xs">
                            Peserta belum dialokasikan pada laga ini.
                        </div>
                    @endforelse
                </div>

                <!-- Pemenang & Status Laga -->
                <div class="pt-6 border-t border-neutral-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="pemenang_peserta_id" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                            Pemenang Pertandingan
                        </label>
                        <select id="pemenang_peserta_id" name="pemenang_peserta_id"
                                class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Belum ditentukan / Seri</option>
                            @foreach($pertandingan->pesertaPertandingan as $peserta)
                                <option value="{{ $peserta->id }}" {{ old('pemenang_peserta_id', $pertandingan->hasilPertandingan?->pemenang_peserta_id) == $peserta->id ? 'selected' : '' }}>
                                    🏆 {{ $peserta->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                            Status Pertandingan <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required
                                class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                            <option value="selesai" {{ old('status', $pertandingan->status) == 'selesai' ? 'selected' : '' }}>Selesai (Final)</option>
                            <option value="berlangsung" {{ old('status', $pertandingan->status) == 'berlangsung' ? 'selected' : '' }}>Sedang Berlangsung (Live)</option>
                            <option value="terjadwal" {{ old('status', $pertandingan->status) == 'terjadwal' ? 'selected' : '' }}>Terjadwal (Belum Selesai)</option>
                        </select>
                    </div>
                </div>

                <!-- Catatan / Keterangan -->
                <div>
                    <label for="keterangan" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Catatan Wasit / Berita Acara Pertandingan
                    </label>
                    <textarea id="keterangan" name="keterangan" rows="3" placeholder="Informasi diskualifikasi, walkover (WO), cedera, atau catatan skor babak per babak..."
                              class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">{{ old('keterangan', $pertandingan->hasilPertandingan?->keterangan) }}</textarea>
                </div>
            </div>

            <div class="p-4 bg-neutral-50 border-t border-neutral-200/80 flex items-center justify-end gap-3">
                <a href="{{ route('admin.hasil.index') }}"
                   class="px-4 py-2.5 text-sm font-medium text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors">
                    Kembali
                </a>
                <button type="submit"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm transition-colors">
                    Simpan Hasil Pertandingan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
