@extends('layouts.admin')

@section('title', 'Edit Agenda Rundown')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <a href="{{ route('admin.rundown.index') }}" class="hover:text-neutral-800">Rundown</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-800 font-medium">Edit Agenda</span>
@endsection

@section('content')
<div class="max-w-3xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-neutral-900">Edit Agenda Rundown</h1>
        <p class="text-sm text-neutral-500 mt-1">Perbarui detail kegiatan, waktu, atau penanggung jawab agenda.</p>
    </div>

    <form action="{{ route('admin.acara-rundown.update', $acara->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="p-6 bg-white border border-neutral-200/80 rounded-xl shadow-xs space-y-5">
            <!-- Event Selection -->
            <div>
                <label for="event_id" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                    Event Olahraga <span class="text-red-500">*</span>
                </label>
                <select id="event_id" name="event_id" required
                        class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('event_id') border-red-500 @enderror">
                    <option value="">Pilih Event...</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}" {{ old('event_id', $acara->event_id) == $event->id ? 'selected' : '' }}>
                            {{ $event->nama }} {{ $event->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('event_id')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tanggal & Waktu -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="tanggal" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="tanggal" name="tanggal" required
                           value="{{ old('tanggal', $acara->tanggal ? $acara->tanggal->format('Y-m-d') : '') }}"
                           class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('tanggal') border-red-500 @enderror">
                    @error('tanggal')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="waktu_mulai" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Waktu Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="time" id="waktu_mulai" name="waktu_mulai" required
                           value="{{ old('waktu_mulai', substr($acara->waktu_mulai, 0, 5)) }}"
                           class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('waktu_mulai') border-red-500 @enderror">
                    @error('waktu_mulai')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="waktu_selesai" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Waktu Selesai (Opsional)
                    </label>
                    <input type="time" id="waktu_selesai" name="waktu_selesai"
                           value="{{ old('waktu_selesai', $acara->waktu_selesai ? substr($acara->waktu_selesai, 0, 5) : '') }}"
                           class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('waktu_selesai') border-red-500 @enderror">
                    @error('waktu_selesai')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Judul Agenda -->
            <div>
                <label for="judul" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                    Judul Acara / Kegiatan <span class="text-red-500">*</span>
                </label>
                <input type="text" id="judul" name="judul" required
                       value="{{ old('judul', $acara->judul) }}"
                       class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('judul') border-red-500 @enderror">
                @error('judul')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Lokasi & Penanggung Jawab -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="lokasi" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Lokasi / Tempat
                    </label>
                    <input type="text" id="lokasi" name="lokasi"
                           value="{{ old('lokasi', $acara->lokasi) }}"
                           class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('lokasi') border-red-500 @enderror">
                    @error('lokasi')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="penanggung_jawab" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Penanggung Jawab (PIC)
                    </label>
                    <input type="text" id="penanggung_jawab" name="penanggung_jawab"
                           value="{{ old('penanggung_jawab', $acara->penanggung_jawab) }}"
                           class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('penanggung_jawab') border-red-500 @enderror">
                    @error('penanggung_jawab')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Catatan -->
            <div>
                <label for="catatan" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                    Deskripsi / Catatan Tambahan
                </label>
                <textarea id="catatan" name="catatan" rows="3"
                          class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('catatan') border-red-500 @enderror">{{ old('catatan', $acara->catatan) }}</textarea>
                @error('catatan')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Publikasi Switch -->
            <div class="pt-3 border-t border-neutral-100 flex items-center justify-between">
                <div>
                    <span class="text-sm font-semibold text-neutral-900">Publikasikan ke Publik</span>
                    <p class="text-xs text-neutral-500">Jika aktif, agenda ini akan tampil di jadwal publik portal dan kontingen.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="dipublikasikan" value="1" {{ old('dipublikasikan', $acara->dipublikasikan) ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-neutral-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-neutral-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.rundown.index') }}"
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
