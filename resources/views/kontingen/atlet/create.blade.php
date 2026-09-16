@extends('layouts.kontingen')

@section('title', 'Tambah Data Atlet')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Tambah Atlet Baru</h1>
            <p class="text-xs text-slate-500 mt-1">Isi identitas atlet kontingen {{ $kontingen->nama }}.</p>
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

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('kontingen.atlet.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label for="nama" class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap Atlet <span class="text-red-500">*</span></label>
                <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 focus:outline-none" placeholder="Sesuai KTP / KK">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nik" class="block text-xs font-semibold text-slate-700 mb-1">NIK (16 Digit) <span class="text-red-500">*</span></label>
                    <input type="text" id="nik" name="nik" value="{{ old('nik') }}" required maxlength="20"
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 focus:outline-none" placeholder="3201...">
                </div>
                <div>
                    <label for="gender" class="block text-xs font-semibold text-slate-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                    <select id="gender" name="gender" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 focus:outline-none">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki (Putra)</option>
                        <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan (Putri)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="tanggal_lahir" class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 focus:outline-none">
                </div>
                <div>
                    <label for="asal_kota" class="block text-xs font-semibold text-slate-700 mb-1">Kabupaten/Kota Asal <span class="text-red-500">*</span></label>
                    <input type="text" id="asal_kota" name="asal_kota" value="{{ old('asal_kota', $kontingen->kota) }}" required
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 focus:outline-none">
                </div>
            </div>

            <div>
                <label for="foto" class="block text-xs font-semibold text-slate-700 mb-1">Pas Foto Atlet</label>
                <input type="file" id="foto" name="foto" accept="image/*"
                    class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                <p class="text-[11px] text-slate-400 mt-1">Format JPG/PNG, maksimal 2 MB.</p>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('kontingen.atlet.index') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="rounded-xl bg-red-700 px-5 py-2.5 text-xs font-bold text-white shadow hover:bg-red-800 transition">
                    Simpan Atlet
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
