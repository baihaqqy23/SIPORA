@extends('layouts.admin')

@section('title', 'Tambah Nomor Lomba - ' . $cabor->nama)

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <a href="{{ route('admin.cabor.index') }}" class="hover:text-neutral-800">Cabor</a>
    <span class="mx-1 text-neutral-300">/</span>
    <a href="{{ route('admin.cabor.show', $cabor) }}" class="hover:text-neutral-800">{{ $cabor->singkatan }}</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-400">Tambah Nomor Lomba</span>
@endsection

@section('content')
<div class="max-w-2xl space-y-6">
    <div>
        <h1 class="text-lg font-semibold text-neutral-800">Tambah Nomor Lomba</h1>
        <p class="text-xs text-neutral-500 mt-0.5">Cabang: <strong>{{ $cabor->nama }}</strong></p>
    </div>

    <form method="POST" action="{{ route('admin.cabor.nomor-lomba.store', $cabor) }}" class="rounded-2xl border border-neutral-200 bg-white shadow-sm">
        @csrf

        <div class="p-6 space-y-5">
            {{-- Nama --}}
            <div>
                <label for="nama" class="block text-xs font-semibold text-neutral-700 mb-1.5">Nama Nomor Lomba <span class="text-red-500">*</span></label>
                <input type="text" name="nama" id="nama" value="{{ old('nama') }}" required class="w-full rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 placeholder:text-neutral-400 focus:border-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-400" placeholder="cth: Tunggal Putra, 100M Sprint">
                @error('nama') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Gender & Jenis --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="gender" class="block text-xs font-semibold text-neutral-700 mb-1.5">Gender <span class="text-red-500">*</span></label>
                    <select name="gender" id="gender" required class="w-full rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-400">
                        <option value="putra" {{ old('gender') === 'putra' ? 'selected' : '' }}>Putra</option>
                        <option value="putri" {{ old('gender') === 'putri' ? 'selected' : '' }}>Putri</option>
                        <option value="campuran" {{ old('gender') === 'campuran' ? 'selected' : '' }}>Campuran</option>
                    </select>
                    @error('gender') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="jenis" class="block text-xs font-semibold text-neutral-700 mb-1.5">Jenis <span class="text-red-500">*</span></label>
                    <select name="jenis" id="jenis" required class="w-full rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-400">
                        <option value="perorangan" {{ old('jenis') === 'perorangan' ? 'selected' : '' }}>Perorangan</option>
                        <option value="beregu" {{ old('jenis') === 'beregu' ? 'selected' : '' }}>Beregu</option>
                    </select>
                    @error('jenis') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Jumlah Anggota & Cadangan (for beregu) --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="jumlah_anggota" class="block text-xs font-semibold text-neutral-700 mb-1.5">Jumlah Anggota (Beregu)</label>
                    <input type="number" name="jumlah_anggota" id="jumlah_anggota" value="{{ old('jumlah_anggota') }}" min="1" class="w-full rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-400" placeholder="cth: 5">
                    @error('jumlah_anggota') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="jumlah_cadangan" class="block text-xs font-semibold text-neutral-700 mb-1.5">Jumlah Cadangan</label>
                    <input type="number" name="jumlah_cadangan" id="jumlah_cadangan" value="{{ old('jumlah_cadangan') }}" min="0" class="w-full rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-400" placeholder="cth: 2">
                    @error('jumlah_cadangan') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Umur Min & Max --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="umur_min" class="block text-xs font-semibold text-neutral-700 mb-1.5">Umur Minimal (tahun)</label>
                    <input type="number" name="umur_min" id="umur_min" value="{{ old('umur_min') }}" min="0" class="w-full rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-400" placeholder="Kosongkan jika umum">
                    @error('umur_min') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="umur_maks" class="block text-xs font-semibold text-neutral-700 mb-1.5">Umur Maksimal (tahun)</label>
                    <input type="number" name="umur_maks" id="umur_maks" value="{{ old('umur_maks') }}" min="0" class="w-full rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-400" placeholder="Kosongkan jika umum">
                    @error('umur_maks') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Format Pertandingan --}}
            <div>
                <label for="format_pertandingan" class="block text-xs font-semibold text-neutral-700 mb-1.5">Format Pertandingan <span class="text-red-500">*</span></label>
                <select name="format_pertandingan" id="format_pertandingan" required class="w-full rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-400">
                    <option value="gugur_tunggal" {{ old('format_pertandingan') === 'gugur_tunggal' ? 'selected' : '' }}>Gugur Tunggal (Single Elimination)</option>
                    <option value="round_robin" {{ old('format_pertandingan') === 'round_robin' ? 'selected' : '' }}>Round Robin</option>
                    <option value="heat" {{ old('format_pertandingan') === 'heat' ? 'selected' : '' }}>Heat (Babak Waktu)</option>
                    <option value="penilaian" {{ old('format_pertandingan') === 'penilaian' ? 'selected' : '' }}>Penilaian (Scoring)</option>
                </select>
                @error('format_pertandingan') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Kuota & Kapasitas --}}
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label for="kuota_per_kontingen" class="block text-xs font-semibold text-neutral-700 mb-1.5">Kuota/Kontingen</label>
                    <input type="number" name="kuota_per_kontingen" id="kuota_per_kontingen" value="{{ old('kuota_per_kontingen', 2) }}" min="1" class="w-full rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-400">
                    @error('kuota_per_kontingen') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="kapasitas_total" class="block text-xs font-semibold text-neutral-700 mb-1.5">Kapasitas Total</label>
                    <input type="number" name="kapasitas_total" id="kapasitas_total" value="{{ old('kapasitas_total') }}" min="1" class="w-full rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-400" placeholder="Kosong = tak terbatas">
                    @error('kapasitas_total') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="jumlah_perunggu" class="block text-xs font-semibold text-neutral-700 mb-1.5">Jumlah Perunggu</label>
                    <input type="number" name="jumlah_perunggu" id="jumlah_perunggu" value="{{ old('jumlah_perunggu', 1) }}" min="1" class="w-full rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-400">
                    @error('jumlah_perunggu') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-2 border-t border-neutral-200 px-6 py-4">
            <a href="{{ route('admin.cabor.show', $cabor) }}" class="rounded-lg border border-neutral-200 bg-white px-4 py-2 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 transition-colors">Batal</a>
            <button type="submit" class="rounded-lg bg-neutral-900 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-neutral-800 transition-colors">Simpan Nomor Lomba</button>
        </div>
    </form>
</div>
@endsection
