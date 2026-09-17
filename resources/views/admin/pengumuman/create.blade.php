@extends('layouts.admin')

@section('title', 'Buat Pengumuman Baru')

@section('content')
<div class="p-6 sm:p-8 max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.pengumuman.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors">
                    &larr; Kembali ke Pengumuman
                </a>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Buat Pengumuman Baru</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Sebarkan informasi resmi, jadwal perubahan, atau instruksi teknis.</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-xl p-6 shadow-sm">
        <form action="{{ route('admin.pengumuman.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label for="event_id" class="block text-sm font-semibold text-slate-900 dark:text-slate-100 mb-1.5">
                    Pilih Event Terkait <span class="text-rose-500">*</span>
                </label>
                <select id="event_id" name="event_id" required class="w-full py-2.5 px-3.5 text-sm rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                    <option value="">-- Pilih Event --</option>
                    @foreach($events as $ev)
                        <option value="{{ $ev->id }}" {{ old('event_id') == $ev->id ? 'selected' : '' }}>
                            {{ $ev->nama }} ({{ $ev->status }})
                        </option>
                    @endforeach
                </select>
                @error('event_id')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="judul" class="block text-sm font-semibold text-slate-900 dark:text-slate-100 mb-1.5">
                    Judul Pengumuman <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="judul" name="judul" value="{{ old('judul') }}" required placeholder="Contoh: Perubahan Jadwal Pertandingan Cabor Futsal" class="w-full py-2.5 px-3.5 text-sm rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                @error('judul')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="target" class="block text-sm font-semibold text-slate-900 dark:text-slate-100 mb-1.5">
                        Target Audiens <span class="text-rose-500">*</span>
                    </label>
                    <select id="target" name="target" required class="w-full py-2.5 px-3.5 text-sm rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                        <option value="semua" {{ old('target') == 'semua' ? 'selected' : '' }}>Semua Pengguna</option>
                        <option value="publik" {{ old('target') == 'publik' ? 'selected' : '' }}>Publik (Pengunjung Umum)</option>
                        <option value="kontingen" {{ old('target') == 'kontingen' ? 'selected' : '' }}>Kontingen / Official</option>
                        <option value="panitia" {{ old('target') == 'panitia' ? 'selected' : '' }}>Panitia & Wasit</option>
                    </select>
                    @error('target')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tayang_mulai" class="block text-sm font-semibold text-slate-900 dark:text-slate-100 mb-1.5">
                        Jadwal Tayang (Opsional)
                    </label>
                    <input type="datetime-local" id="tayang_mulai" name="tayang_mulai" value="{{ old('tayang_mulai') }}" class="w-full py-2.5 px-3.5 text-sm rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                    <p class="text-xs text-slate-400 mt-1">Kosongkan untuk langsung ditayangkan sekarang.</p>
                    @error('tayang_mulai')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="isi" class="block text-sm font-semibold text-slate-900 dark:text-slate-100 mb-1.5">
                    Isi Pengumuman <span class="text-rose-500">*</span>
                </label>
                <textarea id="isi" name="isi" rows="6" required placeholder="Tuliskan detail pengumuman secara rinci..." class="w-full py-2.5 px-3.5 text-sm rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 font-sans leading-relaxed">{{ old('isi') }}</textarea>
                @error('isi')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="lampiran" class="block text-sm font-semibold text-slate-900 dark:text-slate-100 mb-1.5">
                    File Lampiran (Opsional)
                </label>
                <input type="file" id="lampiran" name="lampiran" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-slate-100 dark:file:bg-slate-800 file:text-slate-700 dark:file:text-slate-200 hover:file:bg-slate-200 cursor-pointer">
                <p class="text-xs text-slate-400 mt-1">Format: PDF, PNG, JPG, DOCX, ZIP (Maks. 10MB)</p>
                @error('lampiran')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('admin.pengumuman.index') }}" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-slate-900 dark:bg-primary-600 hover:bg-slate-800 dark:hover:bg-primary-700 rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-primary-500">
                    Publikasikan Pengumuman
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
