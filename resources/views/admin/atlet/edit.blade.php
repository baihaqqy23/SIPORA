@extends('layouts.admin')

@section('title', 'Edit Data Atlet - ' . $atlet->nama)

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <a href="{{ route('admin.atlet.index') }}" class="hover:text-neutral-800">Atlet</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-800 font-medium">Edit</span>
@endsection

@section('content')
<div class="max-w-3xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-neutral-900">Edit Data Atlet</h1>
        <p class="text-sm text-neutral-500 mt-1">Perbarui biodata atlet, status keabsahan atlet, atau catatan verifikasi.</p>
    </div>

    <form action="{{ route('admin.atlet.update', $atlet) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="p-6 bg-white border border-neutral-200/80 rounded-xl shadow-xs space-y-5">
            <!-- Kontingen Selection -->
            <div>
                <label for="kontingen_id" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                    Kontingen Daerah <span class="text-red-500">*</span>
                </label>
                <select id="kontingen_id" name="kontingen_id" required
                        class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('kontingen_id') border-red-500 @enderror">
                    <option value="">Pilih Kontingen...</option>
                    @foreach($kontingens as $k)
                        <option value="{{ $k->id }}" {{ old('kontingen_id', $atlet->kontingen_id) == $k->id ? 'selected' : '' }}>
                            {{ $k->nama }} ({{ $k->kota }})
                        </option>
                    @endforeach
                </select>
                @error('kontingen_id')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nama & NIK -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nama" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Nama Lengkap Atlet <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nama" name="nama" required
                           value="{{ old('nama', $atlet->nama) }}"
                           class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('nama') border-red-500 @enderror">
                    @error('nama')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nik" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        NIK (16 Digit)
                    </label>
                    <input type="text" id="nik" name="nik" maxlength="16" placeholder="3201..."
                           value="{{ old('nik', $atlet->nik) }}"
                           class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('nik') border-red-500 @enderror">
                    @error('nik')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Tanggal Lahir, Gender & Asal Kota -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="tanggal_lahir" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Tanggal Lahir <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" required
                           value="{{ old('tanggal_lahir', $atlet->tanggal_lahir ? $atlet->tanggal_lahir->format('Y-m-d') : '') }}"
                           class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('tanggal_lahir') border-red-500 @enderror">
                    @error('tanggal_lahir')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="gender" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Jenis Kelamin <span class="text-red-500">*</span>
                    </label>
                    <select id="gender" name="gender" required
                            class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                        <option value="L" {{ old('gender', $atlet->gender) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender', $atlet->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div>
                    <label for="asal_kota" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Tempat / Kota Asal
                    </label>
                    <input type="text" id="asal_kota" name="asal_kota"
                           value="{{ old('asal_kota', $atlet->asal_kota) }}"
                           class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <!-- Status Verifikasi & Catatan -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="status" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Status Keabsahan <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status" required
                            class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                        <option value="terverifikasi" {{ old('status', $atlet->status) == 'terverifikasi' ? 'selected' : '' }}>Terverifikasi (Sah)</option>
                        <option value="menunggu" {{ old('status', $atlet->status) == 'menunggu' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="draft" {{ old('status', $atlet->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="perlu_perbaikan" {{ old('status', $atlet->status) == 'perlu_perbaikan' ? 'selected' : '' }}>Perlu Perbaikan</option>
                        <option value="ditolak" {{ old('status', $atlet->status) == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label for="catatan_status" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Catatan Keabsahan / Verifikasi
                    </label>
                    <input type="text" id="catatan_status" name="catatan_status" placeholder="Misal: Foto KTP buram, harap perbarui..."
                           value="{{ old('catatan_status', $atlet->catatan_status) }}"
                           class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <!-- Foto Atlet -->
            <div>
                <label for="foto" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                    Ganti Foto Atlet (Opsional)
                </label>
                @if($atlet->foto_path)
                    <div class="mb-2 flex items-center gap-2">
                        <img src="{{ Storage::url($atlet->foto_path) }}" class="w-10 h-10 rounded-full object-cover border">
                        <span class="text-xs text-neutral-500">Foto profil saat ini</span>
                    </div>
                @endif
                <input type="file" id="foto" name="foto" accept="image/*"
                       class="w-full text-xs text-neutral-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.atlet.show', $atlet) }}"
               class="px-4 py-2.5 text-sm font-medium text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm transition-colors">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
