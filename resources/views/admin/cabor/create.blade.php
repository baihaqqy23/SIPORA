@extends('layouts.admin')

@section('title', 'Tambah Cabang Olahraga')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <a href="{{ route('admin.cabor.index') }}" class="hover:text-neutral-800">Cabor</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-400">Tambah</span>
@endsection

@section('content')
<div class="max-w-2xl space-y-6">
    <div>
        <h1 class="text-lg font-semibold text-neutral-800">Tambah Cabang Olahraga</h1>
        <p class="text-xs text-neutral-500 mt-0.5">Isikan data cabang olahraga baru untuk event aktif.</p>
    </div>

    <form method="POST" action="{{ route('admin.cabor.store') }}" class="rounded-2xl border border-neutral-200 bg-white shadow-sm">
        @csrf
        <input type="hidden" name="event_id" value="{{ $event?->id }}">

        <div class="p-6 space-y-5">
            {{-- Nama --}}
            <div>
                <label for="nama" class="block text-xs font-semibold text-neutral-700 mb-1.5">Nama Cabang Olahraga <span class="text-red-500">*</span></label>
                <input type="text" name="nama" id="nama" value="{{ old('nama') }}" required class="w-full rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 placeholder:text-neutral-400 focus:border-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-400" placeholder="cth: Bulu Tangkis">
                @error('nama') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Singkatan & Warna --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="singkatan" class="block text-xs font-semibold text-neutral-700 mb-1.5">Singkatan / Kode <span class="text-red-500">*</span></label>
                    <input type="text" name="singkatan" id="singkatan" value="{{ old('singkatan') }}" required maxlength="10" class="w-full rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 uppercase placeholder:text-neutral-400 focus:border-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-400" placeholder="cth: BDM">
                    @error('singkatan') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="warna" class="block text-xs font-semibold text-neutral-700 mb-1.5">Warna Identitas</label>
                    <div class="flex items-center gap-2">
                        <input type="color" name="warna" id="warna" value="{{ old('warna', '#C8102E') }}" class="h-9 w-12 cursor-pointer rounded-lg border border-neutral-200">
                        <span class="text-xs text-neutral-400">Digunakan di badge & jadwal</span>
                    </div>
                    @error('warna') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Durasi & Jeda --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="durasi_default_menit" class="block text-xs font-semibold text-neutral-700 mb-1.5">Durasi Default (menit)</label>
                    <input type="number" name="durasi_default_menit" id="durasi_default_menit" value="{{ old('durasi_default_menit', 60) }}" min="1" class="w-full rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-400">
                    @error('durasi_default_menit') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="jeda_antar_tanding_menit" class="block text-xs font-semibold text-neutral-700 mb-1.5">Jeda Antar Tanding (menit)</label>
                    <input type="number" name="jeda_antar_tanding_menit" id="jeda_antar_tanding_menit" value="{{ old('jeda_antar_tanding_menit', 30) }}" min="0" class="w-full rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-400">
                    @error('jeda_antar_tanding_menit') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Venue --}}
            <div>
                <label class="block text-xs font-semibold text-neutral-700 mb-1.5">Venue Pertandingan</label>
                <div class="space-y-2">
                    @forelse($venues as $venue)
                        <label class="flex items-center gap-2 px-3 py-2 rounded-lg border border-neutral-200 hover:bg-neutral-50 cursor-pointer transition-colors has-[:checked]:border-neutral-900 has-[:checked]:bg-neutral-50">
                            <input type="checkbox" name="venue_ids[]" value="{{ $venue->id }}" {{ in_array($venue->id, old('venue_ids', [])) ? 'checked' : '' }} class="h-4 w-4 rounded border-neutral-300 text-neutral-900 focus:ring-neutral-500">
                            <span class="text-sm text-neutral-700">{{ $venue->nama }}</span>
                            @if($venue->alamat)
                                <span class="text-[11px] text-neutral-400 ml-auto">{{ Str::limit($venue->alamat, 40) }}</span>
                            @endif
                        </label>
                    @empty
                        <p class="text-xs text-neutral-400 italic">Belum ada venue untuk event ini.</p>
                    @endforelse
                </div>
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="deskripsi" class="block text-xs font-semibold text-neutral-700 mb-1.5">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="3" class="w-full rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 placeholder:text-neutral-400 focus:border-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-400" placeholder="Keterangan tambahan tentang cabor ini...">{{ old('deskripsi') }}</textarea>
                @error('deskripsi') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-2 border-t border-neutral-200 px-6 py-4">
            <a href="{{ route('admin.cabor.index') }}" class="rounded-lg border border-neutral-200 bg-white px-4 py-2 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 transition-colors">Batal</a>
            <button type="submit" class="rounded-lg bg-neutral-900 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-neutral-800 transition-colors">Simpan Cabor</button>
        </div>
    </form>
</div>
@endsection
