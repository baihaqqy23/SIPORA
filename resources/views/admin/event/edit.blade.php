@extends('layouts.admin')

@section('title', 'Edit Event - ' . $event->nama)

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <a href="{{ route('admin.event.index') }}" class="hover:text-neutral-800">Event</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-800 font-medium">Edit</span>
@endsection

@section('content')
<div class="max-w-3xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-neutral-900">Edit Konfigurasi Event</h1>
        <p class="text-sm text-neutral-500 mt-1">Perbarui jadwal pelaksanaan, masa pendaftaran, batas usia atlet, atau status.</p>
    </div>

    <form action="{{ route('admin.event.update', $event) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="p-6 bg-white border border-neutral-200/80 rounded-xl shadow-xs space-y-5">
            <!-- Nama Event -->
            <div>
                <label for="nama" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                    Nama Resmi Event <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nama" name="nama" required
                       value="{{ old('nama', $event->nama) }}"
                       class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('nama') border-red-500 @enderror">
                @error('nama')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="deskripsi" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                    Deskripsi Singkat Event
                </label>
                <textarea id="deskripsi" name="deskripsi" rows="3"
                          class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">{{ old('deskripsi', $event->deskripsi) }}</textarea>
            </div>

            <!-- Tanggal Pelaksanaan Event -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="tanggal_mulai" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Mulai Event <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" required
                           value="{{ old('tanggal_mulai', $event->tanggal_mulai ? $event->tanggal_mulai->format('Y-m-d') : '') }}"
                           class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('tanggal_mulai') border-red-500 @enderror">
                    @error('tanggal_mulai')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tanggal_selesai" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Selesai Event <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" required
                           value="{{ old('tanggal_selesai', $event->tanggal_selesai ? $event->tanggal_selesai->format('Y-m-d') : '') }}"
                           class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('tanggal_selesai') border-red-500 @enderror">
                    @error('tanggal_selesai')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="kategori_usia" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Patokan Usia Atlet <span class="text-red-500">*</span>
                    </label>
                    <select id="kategori_usia" name="kategori_usia" required
                            class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('kategori_usia') border-red-500 @enderror">
                        @foreach(\App\Models\Event::daftarKategoriUsia() as $kode => $info)
                            <option value="{{ $kode }}" {{ old('kategori_usia', $event->kategori_usia) == $kode ? 'selected' : '' }}>
                                {{ $info['label'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori_usia')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Periode Pendaftaran -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="pendaftaran_mulai" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Pendaftaran Dibuka <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" id="pendaftaran_mulai" name="pendaftaran_mulai" required
                           value="{{ old('pendaftaran_mulai', $event->pendaftaran_mulai ? $event->pendaftaran_mulai->format('Y-m-d\TH:i') : '') }}"
                           class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('pendaftaran_mulai') border-red-500 @enderror">
                    @error('pendaftaran_mulai')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="pendaftaran_selesai" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Pendaftaran Ditutup <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" id="pendaftaran_selesai" name="pendaftaran_selesai" required
                           value="{{ old('pendaftaran_selesai', $event->pendaftaran_selesai ? $event->pendaftaran_selesai->format('Y-m-d\TH:i') : '') }}"
                           class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('pendaftaran_selesai') border-red-500 @enderror">
                    @error('pendaftaran_selesai')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Maks Nomor Lomba & Status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="maks_nomor_lomba_per_atlet" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Maksimal Nomor Lomba / Atlet
                    </label>
                    <input type="number" id="maks_nomor_lomba_per_atlet" name="maks_nomor_lomba_per_atlet" min="1" max="10"
                           value="{{ old('maks_nomor_lomba_per_atlet', $event->maks_nomor_lomba_per_atlet) }}"
                           class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="status" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Status Event <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status" required
                            class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                        <option value="draft" {{ old('status', $event->status) == 'draft' ? 'selected' : '' }}>Draft (Persiapan)</option>
                        <option value="pendaftaran_dibuka" {{ old('status', $event->status) == 'pendaftaran_dibuka' ? 'selected' : '' }}>Pendaftaran Dibuka</option>
                        <option value="pendaftaran_ditutup" {{ old('status', $event->status) == 'pendaftaran_ditutup' ? 'selected' : '' }}>Pendaftaran Ditutup</option>
                        <option value="berlangsung" {{ old('status', $event->status) == 'berlangsung' ? 'selected' : '' }}>Berlangsung</option>
                        <option value="selesai" {{ old('status', $event->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
            </div>

            <!-- Logo -->
            <div>
                <label for="logo" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                    Ganti Logo / Maskot Event (Opsional)
                </label>
                @if($event->logo_path)
                    <div class="mb-2 flex items-center gap-2">
                        <img src="{{ Storage::url($event->logo_path) }}" class="w-10 h-10 rounded-lg object-cover border">
                        <span class="text-xs text-neutral-500">Logo saat ini terpasang</span>
                    </div>
                @endif
                <input type="file" id="logo" name="logo" accept="image/*"
                       class="w-full text-xs text-neutral-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.event.show', $event) }}"
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
