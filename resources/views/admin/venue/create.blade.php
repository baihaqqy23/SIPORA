@extends('layouts.admin')

@section('title', 'Tambah Venue Baru')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <a href="{{ route('admin.venue.index') }}" class="hover:text-neutral-800">Venue</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-800 font-medium">Tambah</span>
@endsection

@section('content')
<div class="max-w-3xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-neutral-900">Tambah Venue Pertandingan</h1>
        <p class="text-sm text-neutral-500 mt-1">Daftarkan stadion, GOR, gedung, atau arena penyelenggaraan lomba.</p>
    </div>

    <form action="{{ route('admin.venue.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="p-6 bg-white border border-neutral-200/80 rounded-xl shadow-xs space-y-5">
            <!-- Event Selection -->
            <div>
                <label for="event_id" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                    Event Terkait <span class="text-red-500">*</span>
                </label>
                <select id="event_id" name="event_id" required
                        class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('event_id') border-red-500 @enderror">
                    <option value="">Pilih Event...</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}" {{ old('event_id', $activeEvent?->id) == $event->id ? 'selected' : '' }}>
                            {{ $event->nama }} {{ $event->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('event_id')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nama & Kapasitas -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label for="nama" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Nama Venue / Arena <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nama" name="nama" required placeholder="Contoh: GOR Patriot Chandrabhaga"
                           value="{{ old('nama') }}"
                           class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('nama') border-red-500 @enderror">
                    @error('nama')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="kapasitas" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Kapasitas Penonton
                    </label>
                    <input type="number" id="kapasitas" name="kapasitas" min="0" placeholder="1000"
                           value="{{ old('kapasitas') }}"
                           class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('kapasitas') border-red-500 @enderror">
                    @error('kapasitas')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Alamat -->
            <div>
                <label for="alamat" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                    Alamat Lengkap
                </label>
                <textarea id="alamat" name="alamat" rows="2" placeholder="Jl. Jendral Ahmad Yani No. 1..."
                          class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('alamat') border-red-500 @enderror">{{ old('alamat') }}</textarea>
                @error('alamat')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jam Operasional -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="jam_operasional_mulai" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Jam Buka / Operasional Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="time" id="jam_operasional_mulai" name="jam_operasional_mulai" required
                           value="{{ old('jam_operasional_mulai', '07:00') }}"
                           class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('jam_operasional_mulai') border-red-500 @enderror">
                    @error('jam_operasional_mulai')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="jam_operasional_selesai" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Jam Tutup / Operasional Selesai <span class="text-red-500">*</span>
                    </label>
                    <input type="time" id="jam_operasional_selesai" name="jam_operasional_selesai" required
                           value="{{ old('jam_operasional_selesai', '21:00') }}"
                           class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('jam_operasional_selesai') border-red-500 @enderror">
                    @error('jam_operasional_selesai')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Cabang Olahraga Terkait -->
            <div>
                <label class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                    Cabang Olahraga yang Bertanding di Venue Ini
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 p-3 bg-neutral-50 rounded-lg border border-neutral-200/80 max-h-48 overflow-y-auto">
                    @foreach($caborList as $cabor)
                        <label class="inline-flex items-center gap-2 text-xs text-neutral-800 cursor-pointer p-1 hover:bg-white rounded">
                            <input type="checkbox" name="cabor_ids[]" value="{{ $cabor->id }}"
                                   {{ in_array($cabor->id, old('cabor_ids', [])) ? 'checked' : '' }}
                                   class="rounded border-neutral-300 text-blue-600 focus:ring-blue-500">
                            <span>{{ $cabor->nama }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Lapangan / Court Awal -->
            <div x-data="{ lapangans: ['Lapangan 1'] }">
                <label class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                    Daftar Lapangan / Court
                </label>
                <div class="space-y-2">
                    <template x-for="(lap, index) in lapangans" :key="index">
                        <div class="flex items-center gap-2">
                            <input type="text" name="lapangan_nama[]" x-model="lapangans[index]" placeholder="Nama Lapangan (Contoh: Court A)"
                                   class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                            <button type="button" @click="lapangans.splice(index, 1)" x-show="lapangans.length > 1"
                                    class="p-2 text-neutral-400 hover:text-red-600 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
                <button type="button" @click="lapangans.push('Lapangan ' + (lapangans.length + 1))"
                        class="mt-2 text-xs font-medium text-blue-600 hover:text-blue-700 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Lapangan Lain
                </button>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.venue.index') }}"
               class="px-4 py-2.5 text-sm font-medium text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm transition-colors">
                Simpan Venue
            </button>
        </div>
    </form>
</div>
@endsection
