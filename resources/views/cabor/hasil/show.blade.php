@extends('layouts.cabor')

@section('title', 'Input Hasil Pertandingan — Match #' . ($pertandingan->nomor_pertandingan ?? $pertandingan->id))

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('cabor.jadwal.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 transition-colors">&larr; Kembali ke Jadwal</a>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white mt-1">Input Skor & Hasil Pertandingan</h1>
        </div>
        <span class="inline-block px-3 py-1 rounded-lg text-xs font-bold uppercase
            @if($pertandingan->status === 'selesai') bg-purple-100 text-purple-700
            @elseif($pertandingan->status === 'berlangsung') bg-emerald-100 text-emerald-700
            @else bg-slate-100 text-slate-700
            @endif">
            {{ $pertandingan->status }}
        </span>
    </div>

    {{-- Detail Pertandingan Header --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div>
                <span class="text-slate-400">Nomor Lomba:</span>
                <p class="font-bold text-slate-900 dark:text-white mt-0.5">{{ $pertandingan->nomorLomba?->nama }}</p>
            </div>
            <div>
                <span class="text-slate-400">Babak:</span>
                <p class="font-semibold text-slate-900 dark:text-white mt-0.5">Babak {{ ucfirst(str_replace('_', ' ', $pertandingan->babak)) }}</p>
            </div>
            <div>
                <span class="text-slate-400">Jadwal & Tempat:</span>
                <p class="font-semibold text-slate-900 dark:text-white mt-0.5">{{ $pertandingan->tanggal?->format('d M Y') }} &bull; {{ $pertandingan->jam_mulai ? substr($pertandingan->jam_mulai, 0, 5) : 'TBD' }} &bull; {{ $pertandingan->lapangan?->nama ?? 'Lapangan TBD' }}</p>
            </div>
        </div>
    </div>

    {{-- Form Input Skor & Pemenang --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <form action="{{ route('cabor.hasil.simpan', $pertandingan) }}" method="POST" class="space-y-6">
            @csrf

            <h2 class="text-sm font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-2">Peserta & Skor Akhir</h2>

            <div class="space-y-4">
                @foreach($pertandingan->peserta as $index => $p)
                    <div class="p-4 border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50/50 dark:bg-slate-800/40 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs flex items-center justify-center">{{ $index + 1 }}</span>
                                <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $p->peserta?->nama ?? $p->peserta?->nama_tim ?? 'TBD' }}</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1 pl-8">{{ $p->peserta?->kontingen?->nama ?? '-' }}</p>
                        </div>

                        <div class="flex items-center gap-4">
                            <div>
                                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Skor / Poin</label>
                                <input type="text" name="skor[{{ $p->id }}]" value="{{ old("skor.{$p->id}", $p->skor) }}" placeholder="Contoh: 21-18, 21-19" class="text-xs rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-1.5 w-44 font-mono font-bold">
                            </div>

                            <div class="pt-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="pemenang_id" value="{{ $p->id }}" {{ $p->hasil === 'menang' || old('pemenang_id') == $p->id ? 'checked' : '' }} class="text-primary-600 focus:ring-primary-500">
                                    <span class="text-xs font-bold text-emerald-600">Pemenang</span>
                                </label>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Status Pertandingan <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5">
                        <option value="selesai" {{ $pertandingan->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="berlangsung" {{ $pertandingan->status === 'berlangsung' ? 'selected' : '' }}>Sedang Berlangsung</option>
                        <option value="ditunda" {{ $pertandingan->status === 'ditunda' ? 'selected' : '' }}>Ditunda</option>
                        <option value="dibatalkan" {{ $pertandingan->status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>

                @if($pertandingan->babak === 'final' || $pertandingan->babak === 'perebutan_juara_3')
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Penetapan Medali (Untuk Pemenang)</label>
                        <select name="medali" class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5">
                            <option value="">-- Tidak Menetapkan Medali --</option>
                            <option value="emas">Medali Emas (Juara 1)</option>
                            <option value="perak">Medali Perak (Juara 2)</option>
                            <option value="perunggu">Medali Perunggu (Juara 3)</option>
                        </select>
                    </div>
                @endif
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Catatan Wasit / Pertandingan</label>
                <textarea name="catatan" rows="3" placeholder="Catatan jalannya pertandingan, pelanggaran, atau detail set..." class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-3">{{ old('catatan', $pertandingan->catatan) }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('cabor.jadwal.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-100 rounded-xl">Batal</a>
                <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-slate-900 dark:bg-primary-600 hover:bg-slate-800 rounded-xl shadow-xs">
                    Simpan Hasil Pertandingan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
