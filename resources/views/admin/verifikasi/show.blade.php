@extends('layouts.admin')

@section('title', 'Detail Verifikasi Pendaftaran')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-800">Dashboard</a>
    <span class="mx-2 text-slate-300">/</span>
    <a href="{{ route('admin.verifikasi.index') }}" class="hover:text-slate-800">Verifikasi</a>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-400">Detail</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-semibold text-slate-800">Pemeriksaan Keabsahan & Berkas</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kode Registrasi: #{{ $pendaftaran->id }} &bull; Kontingen: {{ $pendaftaran->kontingen?->nama }}</p>
        </div>
        <a href="{{ route('admin.verifikasi.index') }}" class="text-xs font-semibold text-slate-600 hover:underline">
            &larr; Kembali ke Daftar
        </a>
    </div>

    {{-- Hasil Validasi Otomatis (Rules & Constraints) --}}
    @if(!empty($validasiOtomatis['pesan']))
        <div class="rounded-2xl border {{ $validasiOtomatis['valid'] ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }} p-5 space-y-2">
            <h2 class="text-xs font-bold uppercase tracking-wider {{ $validasiOtomatis['valid'] ? 'text-green-800' : 'text-red-800' }}">
                {{ $validasiOtomatis['valid'] ? '✓ Validasi Sistem Memenuhi Syarat' : '⚠ Catatan Validasi Sistem' }}
            </h2>
            <ul class="list-disc list-inside text-xs {{ $validasiOtomatis['valid'] ? 'text-green-700' : 'text-red-700' }} space-y-1">
                @foreach($validasiOtomatis['pesan'] as $pesan)
                    <li>{{ $pesan }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Informasi Atlet & Nomor Lomba --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-2">Identitas Atlet</h2>
            @if($pendaftaran->atlet)
                <div class="space-y-2 text-xs">
                    <p><span class="text-slate-400 w-28 inline-block">Nama Lengkap:</span> <strong class="text-slate-800">{{ $pendaftaran->atlet->nama }}</strong></p>
                    <p><span class="text-slate-400 w-28 inline-block">NIK:</span> <strong class="text-slate-800">{{ $pendaftaran->atlet->nik }}</strong></p>
                    <p><span class="text-slate-400 w-28 inline-block">Gender:</span> <strong class="text-slate-800">{{ $pendaftaran->atlet->gender === 'L' ? 'Laki-laki (Putra)' : 'Perempuan (Putri)' }}</strong></p>
                    <p><span class="text-slate-400 w-28 inline-block">Tanggal Lahir:</span> <strong class="text-slate-800">{{ $pendaftaran->atlet->tanggal_lahir?->translatedFormat('d F Y') }} ({{ $pendaftaran->atlet->hitungUmurPada() }} tahun)</strong></p>
                    <p><span class="text-slate-400 w-28 inline-block">Asal Daerah:</span> <strong class="text-slate-800">{{ $pendaftaran->atlet->asal_kota }}</strong></p>
                </div>
            @endif
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-2">Detail Nomor Lomba</h2>
            <div class="space-y-2 text-xs">
                <p><span class="text-slate-400 w-28 inline-block">Cabang Olahraga:</span> <strong class="text-slate-800">{{ $pendaftaran->nomorLomba?->cabangOlahraga?->nama }}</strong></p>
                <p><span class="text-slate-400 w-28 inline-block">Nomor Lomba:</span> <strong class="text-slate-800">{{ $pendaftaran->nomorLomba?->nama }}</strong></p>
                <p><span class="text-slate-400 w-28 inline-block">Kategori:</span> <strong class="text-slate-800">{{ ucfirst($pendaftaran->nomorLomba?->gender) }} &bull; {{ ucfirst($pendaftaran->nomorLomba?->jenis) }}</strong></p>
                <p><span class="text-slate-400 w-28 inline-block">Status Saat Ini:</span> 
                    <span class="inline-block rounded px-2 py-0.5 font-bold text-[10px] bg-slate-100 text-slate-700">
                        {{ ucfirst(str_replace('_', ' ', $pendaftaran->status)) }}
                    </span>
                </p>
            </div>
        </div>
    </div>

    {{-- Dokumen Berkas Atlet --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
        <h2 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-2">Dokumen Berkas Atlet</h2>
        @if(!$pendaftaran->atlet || $pendaftaran->atlet->berkas->isEmpty())
            <p class="text-xs text-slate-400 italic py-2">Tidak ada berkas terunggah.</p>
        @else
            <div class="grid gap-3 sm:grid-cols-2">
                @foreach($pendaftaran->atlet->berkas as $b)
                    <div class="rounded-xl border border-slate-200 p-4 flex items-center justify-between">
                        <div>
                            <span class="rounded bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-800 uppercase">
                                {{ $b->jenis }}
                            </span>
                            <p class="text-xs font-semibold text-slate-800 mt-1 truncate">{{ $b->nama_file_asli }}</p>
                            <p class="text-[10px] text-slate-400">{{ round($b->ukuran_byte / 1024, 1) }} KB</p>
                        </div>
                        <a href="{{ route('kontingen.berkas.download', $b->id) }}" class="rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-bold text-white hover:bg-slate-900 transition">
                            ⬇ Unduh
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Form Keputusan Verifikasi --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
        <h2 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-2">Keputusan Verifikasi Panitia</h2>
        <form method="POST" action="{{ route('admin.verifikasi.proses', $pendaftaran->id) }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Verifikasi</label>
                <textarea name="catatan" rows="3" class="w-full rounded-xl border border-slate-300 p-3 text-xs focus:border-red-600 focus:outline-none" placeholder="Tuliskan catatan verifikasi atau alasan penolakan..."></textarea>
            </div>

            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" name="aksi" value="setujui" class="rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-bold text-white shadow hover:bg-emerald-700 transition">
                    ✓ Setujui Pendaftaran
                </button>
                <button type="submit" name="aksi" value="minta_perbaikan" class="rounded-xl bg-amber-600 px-5 py-2.5 text-xs font-bold text-white shadow hover:bg-amber-700 transition">
                    ⚠ Minta Perbaikan Berkas
                </button>
                <button type="submit" name="aksi" value="tolak" class="rounded-xl bg-red-600 px-5 py-2.5 text-xs font-bold text-white shadow hover:bg-red-700 transition">
                    ✕ Tolak Pendaftaran
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
