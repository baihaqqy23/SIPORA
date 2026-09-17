@extends('layouts.admin')

@section('title', 'Edit ' . $kontingen->nama)

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <a href="{{ route('admin.kontingen.index') }}" class="hover:text-neutral-800">Kontingen</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-800 font-medium">Edit</span>
@endsection

@section('content')
<div class="max-w-3xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-neutral-900">Edit Kontingen: {{ $kontingen->nama }}</h1>
        <p class="text-sm text-neutral-500 mt-1">Perbarui data ofisial, status verifikasi, kontak, atau logo kontingen.</p>
    </div>

    <form action="{{ route('admin.kontingen.update', $kontingen) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="p-6 bg-white border border-neutral-200/80 rounded-xl shadow-xs space-y-5">
            <!-- Event Selection -->
            <div>
                <label for="event_id" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                    Event <span class="text-red-500">*</span>
                </label>
                <select id="event_id" name="event_id" required
                        class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('event_id') border-red-500 @enderror">
                    <option value="">Pilih Event...</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}" {{ old('event_id', $kontingen->event_id) == $event->id ? 'selected' : '' }}>
                            {{ $event->nama }} {{ $event->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('event_id')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nama Kontingen & Status -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label for="nama" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Nama Kontingen <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nama" name="nama" required
                           value="{{ old('nama', $kontingen->nama) }}"
                           class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('nama') border-red-500 @enderror">
                    @error('nama')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Status Verifikasi <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status" required
                            class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                        <option value="disetujui" {{ old('status', $kontingen->status) == 'disetujui' ? 'selected' : '' }}>Disetujui (Aktif)</option>
                        <option value="menunggu" {{ old('status', $kontingen->status) == 'menunggu' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="ditolak" {{ old('status', $kontingen->status) == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        <option value="nonaktif" {{ old('status', $kontingen->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>

            <!-- Kota & Provinsi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="kota" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Kabupaten / Kota <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="kota" name="kota" required
                           value="{{ old('kota', $kontingen->kota) }}"
                           class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('kota') border-red-500 @enderror">
                    @error('kota')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="provinsi" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Provinsi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="provinsi" name="provinsi" required
                           value="{{ old('provinsi', $kontingen->provinsi) }}"
                           class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('provinsi') border-red-500 @enderror">
                    @error('provinsi')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- PIC / Ofisial -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="nama_ofisial" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Nama Ketua / Ofisial <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nama_ofisial" name="nama_ofisial" required
                           value="{{ old('nama_ofisial', $kontingen->nama_ofisial) }}"
                           class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('nama_ofisial') border-red-500 @enderror">
                    @error('nama_ofisial')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="no_hp_ofisial" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        No. HP / WA Ofisial <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="no_hp_ofisial" name="no_hp_ofisial" required
                           value="{{ old('no_hp_ofisial', $kontingen->no_hp_ofisial) }}"
                           class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('no_hp_ofisial') border-red-500 @enderror">
                    @error('no_hp_ofisial')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Email Resmi <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" required
                           value="{{ old('email', $kontingen->email) }}"
                           class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Upload Logo & Mandat -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="logo" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Ganti Logo Kontingen (Opsional)
                    </label>
                    @if($kontingen->logo_path)
                        <div class="mb-2 flex items-center gap-2">
                            <img src="{{ Storage::url($kontingen->logo_path) }}" class="w-8 h-8 rounded-full border">
                            <span class="text-xs text-neutral-500">Logo saat ini terpasang</span>
                        </div>
                    @endif
                    <input type="file" id="logo" name="logo" accept="image/*"
                           class="w-full text-xs text-neutral-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <div>
                    <label for="surat_mandat" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Ganti Surat Mandat (Opsional)
                    </label>
                    @if($kontingen->surat_mandat_path)
                        <div class="mb-2">
                            <a href="{{ Storage::url($kontingen->surat_mandat_path) }}" target="_blank" class="text-xs text-blue-600 hover:underline">
                                Lihat berkas mandat tersimpan &rarr;
                            </a>
                        </div>
                    @endif
                    <input type="file" id="surat_mandat" name="surat_mandat" accept=".pdf,image/*"
                           class="w-full text-xs text-neutral-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.kontingen.show', $kontingen) }}"
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
