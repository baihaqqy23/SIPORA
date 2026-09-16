<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk — SIPORNAS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-arena font-sans antialiased flex items-center justify-center p-4" x-data>

<div class="w-full max-w-sm">
    {{-- Logo --}}
    <div class="text-center mb-8">
        <div class="mx-auto mb-3 h-14 w-14 rounded-2xl bg-brand flex items-center justify-center text-2xl font-bold text-white shadow-lg">S</div>
        <h1 class="text-xl font-bold text-white">SIPORNAS</h1>
        <p class="text-sm text-slate-400 mt-1">Sistem Informasi Olahraga Nasional</p>
    </div>

    {{-- Card --}}
    <div class="rounded-2xl bg-white shadow-xl p-6">
        <h2 class="text-base font-semibold text-slate-800 mb-1">Masuk ke Akun Anda</h2>
        <p class="text-xs text-slate-500 mb-5">Gunakan email dan password yang telah didaftarkan</p>

        @if($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('login.post') }}"
            x-data="{ loading: false }"
            @submit="loading = true"
        >
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-xs font-medium text-slate-700 mb-1">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent @error('email') border-red-400 @enderror"
                        placeholder="email@kontingen.com"
                    >
                </div>

                <div>
                    <label for="password" class="block text-xs font-medium text-slate-700 mb-1">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent"
                        placeholder="••••••••"
                    >
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-xs text-slate-600">
                        <input type="checkbox" name="ingat_saya" class="h-3.5 w-3.5 rounded border-slate-300 text-brand">
                        Ingat saya
                    </label>
                </div>

                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full rounded-lg bg-brand py-2.5 text-sm font-medium text-white hover:bg-brand/90 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2 disabled:opacity-70 transition-colors"
                >
                    <span x-show="!loading">Masuk</span>
                    <span x-show="loading" class="flex items-center justify-center gap-2" x-cloak>
                        <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Memproses...
                    </span>
                </button>
            </div>
        </form>

        <div class="mt-4 pt-4 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-500">
                Belum punya akun kontingen?
                <a href="{{ route('register.kontingen') }}" class="text-brand font-medium hover:underline">Daftar Kontingen</a>
            </p>
        </div>
    </div>

    <p class="mt-6 text-center text-xs text-slate-500">
        <a href="{{ route('publik.beranda') }}" class="hover:text-white transition-colors">← Kembali ke Portal Publik</a>
    </p>
</div>
</body>
</html>
