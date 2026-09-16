@extends('layouts.kontingen')

@section('title', 'Edit Atlet: ' . $atlet->nama)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Edit Data Atlet</h1>
            <p class="text-xs text-slate-500 mt-1">{{ $atlet->nama }} &bull; Kontingen {{ $kontingen->nama }}</p>
        </div>
        <a href="{{ route('kontingen.atlet.index') }}" class="text-xs font-semibold text-slate-600 hover:underline">
            &larr; Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="rounded-xl bg-red-50 border border-red-200 p-4">
            <ul class="list-disc list-inside text-xs text-red-700 space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Edit Data Utama --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-sm font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100">Informasi Pribadi</h2>
        <form method="POST" action="{{ route('kontingen.atlet.update', $atlet->id) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="nama" class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap Atlet <span class="text-red-500">*</span></label>
                <input type="text" id="nama" name="nama" value="{{ old('nama', $atlet->nama) }}" required
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 focus:outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nik" class="block text-xs font-semibold text-slate-700 mb-1">NIK <span class="text-red-500">*</span></label>
                    <input type="text" id="nik" name="nik" value="{{ old('nik', $atlet->nik) }}" required
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 focus:outline-none">
                </div>
                <div>
                    <label for="gender" class="block text-xs font-semibold text-slate-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                    <select id="gender" name="gender" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 focus:outline-none">
                        <option value="L" {{ old('gender', $atlet->gender) === 'L' ? 'selected' : '' }}>Laki-laki (Putra)</option>
                        <option value="P" {{ old('gender', $atlet->gender) === 'P' ? 'selected' : '' }}>Perempuan (Putri)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="tanggal_lahir" class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $atlet->tanggal_lahir?->format('Y-m-d')) }}" required
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 focus:outline-none">
                </div>
                <div>
                    <label for="asal_kota" class="block text-xs font-semibold text-slate-700 mb-1">Kabupaten/Kota Asal <span class="text-red-500">*</span></label>
                    <input type="text" id="asal_kota" name="asal_kota" value="{{ old('asal_kota', $atlet->asal_kota) }}" required
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 focus:outline-none">
                </div>
            </div>

            <div>
                <label for="foto" class="block text-xs font-semibold text-slate-700 mb-1">Ganti Foto Atlet</label>
                <input type="file" id="foto" name="foto" accept="image/*"
                    class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="rounded-xl bg-red-700 px-5 py-2.5 text-xs font-bold text-white shadow hover:bg-red-800 transition">
                    Perbarui Identitas
                </button>
            </div>
        </form>
    </div>

    {{-- Manajemen Berkas Atlet (Private Storage) --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-6">
        <div>
            <h2 class="text-sm font-bold text-slate-800">Berkas Verifikasi & Keabsahan (Private Disk)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Unggah KTP, KK, Akta Kelahiran, atau Surat Sehat untuk verifikasi keabsahan atlet.</p>
        </div>

        {{-- Form Upload Berkas --}}
        <form method="POST" action="{{ route('kontingen.atlet.berkas.upload', $atlet->id) }}" enctype="multipart/form-data" class="rounded-xl bg-slate-50 p-4 border border-slate-200 space-y-3">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Dokumen <span class="text-red-500">*</span></label>
                    <select name="jenis" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-red-600 focus:outline-none bg-white">
                        <option value="akta">Akta Kelahiran</option>
                        <option value="kk">Kartu Keluarga (KK)</option>
                        <option value="kartu_pelajar">Kartu Pelajar / KTP</option>
                        <option value="surat_sehat">Surat Keterangan Sehat</option>
                        <option value="lainnya">Dokumen Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih File (PDF/JPG/PNG, Max 2MB) <span class="text-red-500">*</span></label>
                    <input type="file" name="berkas" required accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-red-700 file:text-white hover:file:bg-red-800">
                </div>
            </div>
            <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-xs font-bold text-white hover:bg-slate-900 transition">
                + Unggah Berkas
            </button>
        </form>

        {{-- Daftar Berkas Terunggah --}}
        @if($atlet->berkas->isEmpty())
            <p class="text-xs text-slate-400 italic py-2">Belum ada berkas terunggah untuk atlet ini.</p>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($atlet->berkas as $b)
                    <div class="py-3 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="rounded bg-slate-100 px-2 py-1 text-[10px] font-bold text-slate-700 uppercase">
                                {{ $b->jenis }}
                            </span>
                            <div>
                                <p class="text-xs font-semibold text-slate-800">{{ $b->nama_file_asli }}</p>
                                <p class="text-[10px] text-slate-400">{{ round($b->ukuran_byte / 1024, 1) }} KB &bull; {{ $b->created_at->translatedFormat('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('kontingen.berkas.download', $b->id) }}" class="rounded-lg border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition">
                                ⬇ Unduh
                            </a>
                            <form method="POST" action="{{ route('kontingen.atlet.berkas.delete', $b->id) }}" onsubmit="return confirm('Hapus berkas ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg border border-red-200 px-2.5 py-1 text-xs font-semibold text-red-600 hover:bg-red-50 transition">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
