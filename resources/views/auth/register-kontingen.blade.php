<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kontingen — SIPORNAS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-slate-50 font-sans antialiased py-8" x-data>

<div class="max-w-lg mx-auto px-4">
    <div class="text-center mb-8">
        <a href="{{ route('publik.beranda') }}" class="inline-flex items-center gap-2 mb-4">
            <div class="h-10 w-10 rounded-xl bg-brand flex items-center justify-center text-white font-bold">S</div>
            <span class="text-lg font-bold text-slate-800">SIPORNAS</span>
        </a>
        <h1 class="text-xl font-bold text-slate-800">Pendaftaran Akun Kontingen</h1>
        <p class="text-sm text-slate-500 mt-1">Daftarkan kontingen daerah Anda. Akun akan diverifikasi Admin Dispora.</p>
    </div>

    @if($errors->any())
        <div class="mb-6 rounded-xl bg-red-50 border border-red-200 px-4 py-3">
            <p class="text-sm font-medium text-red-700 mb-2">Terdapat kesalahan:</p>
            <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl bg-white shadow-lg p-6">
        <form
            method="POST"
            action="{{ route('register.kontingen.post') }}"
            enctype="multipart/form-data"
            x-data="{ loading: false }"
            @submit="loading = true"
        >
            @csrf

            {{-- Data Kontingen --}}
            <div class="mb-6">
                <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Data Kontingen Daerah</h2>
                <div class="space-y-4">
                    <div>
                        <label for="nama_kontingen" class="block text-xs font-medium text-slate-700 mb-1">Nama Kontingen <span class="text-red-500">*</span></label>
                        <input type="text" id="nama_kontingen" name="nama_kontingen" value="{{ old('nama_kontingen') }}" required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand @error('nama_kontingen') border-red-400 @enderror"
                            placeholder="Kontingen Provinsi Jawa Barat">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="provinsi" class="block text-xs font-medium text-slate-700 mb-1">Provinsi <span class="text-red-500">*</span></label>
                            <input type="text" id="provinsi" name="provinsi" value="{{ old('provinsi') }}" required
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand"
                                placeholder="Jawa Barat">
                        </div>
                        <div>
                            <label for="kota" class="block text-xs font-medium text-slate-700 mb-1">Kota/Kabupaten <span class="text-red-500">*</span></label>
                            <input type="text" id="kota" name="kota" value="{{ old('kota') }}" required
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand"
                                placeholder="Kota Bandung">
                        </div>
                    </div>
                    <div>
                        <label for="surat_mandat" class="block text-xs font-medium text-slate-700 mb-1">Surat Mandat / SK Kontingen</label>
                        <input type="file" id="surat_mandat" name="surat_mandat" accept=".pdf,.jpg,.jpeg,.png"
                            class="w-full text-sm text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-brand file:text-white hover:file:bg-brand/90">
                        <p class="mt-1 text-[11px] text-slate-400">PDF/JPG/PNG, maks 2 MB</p>
                    </div>
                </div>
            </div>

            {{-- Data Ofisial --}}
            <div class="mb-6">
                <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Data Ofisial / Manajer Tim</h2>
                <div class="space-y-4">
                    <div>
                        <label for="nama_ofisial" class="block text-xs font-medium text-slate-700 mb-1">Nama Ofisial <span class="text-red-500">*</span></label>
                        <input type="text" id="nama_ofisial" name="nama_ofisial" value="{{ old('nama_ofisial') }}" required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand">
                    </div>
                    <div>
                        <label for="no_hp_ofisial" class="block text-xs font-medium text-slate-700 mb-1">No HP Ofisial <span class="text-red-500">*</span></label>
                        <input type="tel" id="no_hp_ofisial" name="no_hp_ofisial" value="{{ old('no_hp_ofisial') }}" required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand"
                            placeholder="08123456789">
                    </div>
                </div>
            </div>

            {{-- Data Akun --}}
            <div class="mb-6">
                <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Data Akun Login</h2>
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-xs font-medium text-slate-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand @error('email') border-red-400 @enderror"
                            placeholder="ofisial@kontingen.com">
                    </div>
                    <div>
                        <label for="password" class="block text-xs font-medium text-slate-700 mb-1">Password <span class="text-red-500">*</span></label>
                        <input type="password" id="password" name="password" required minlength="8"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand"
                            placeholder="Minimal 8 karakter">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-medium text-slate-700 mb-1">Konfirmasi Password <span class="text-red-500">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand">
                    </div>
                </div>
            </div>

            {{-- Persetujuan Privasi (NFR-S11) --}}
            <div class="mb-6 rounded-xl bg-slate-50 border border-slate-200 p-4">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="setuju_privasi" id="setuju_privasi" required
                        class="mt-0.5 h-4 w-4 rounded border-slate-300 text-brand">
                    <span class="text-xs text-slate-600 leading-relaxed">
                        Saya menyetujui <a href="#" class="text-brand underline">Kebijakan Privasi</a> dan memahami bahwa data pribadi atlet diproses sesuai dengan <strong>UU No. 27/2022 tentang Perlindungan Data Pribadi</strong>. Data hanya digunakan untuk keperluan penyelenggaraan event olahraga ini.
                    </span>
                </label>
            </div>

            <button
                type="submit"
                :disabled="loading"
                class="w-full rounded-lg bg-brand py-2.5 text-sm font-medium text-white hover:bg-brand/90 focus:ring-2 focus:ring-brand disabled:opacity-70 transition-colors"
            >
                <span x-show="!loading">Daftar Kontingen</span>
                <span x-show="loading" class="flex items-center justify-center gap-2" x-cloak>
                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    Mendaftarkan...
                </span>
            </button>
        </form>

        <div class="mt-4 pt-4 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-500">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-brand font-medium hover:underline">Masuk</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>
