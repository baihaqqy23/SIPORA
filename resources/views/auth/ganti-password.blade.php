<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password — {{ config('app.name', 'SIPORNAS') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-900 text-slate-100 font-sans antialiased flex items-center justify-center p-4 relative">
@php
    $dashboardUrl = match(auth()->user()?->role) {
        'admin' => route('admin.dashboard'),
        'pj_cabor' => route('cabor.dashboard'),
        'kontingen' => route('kontingen.dashboard'),
        default => url('/'),
    };
@endphp

<!-- Fixed Top-Left Back Button -->
<div class="fixed top-6 left-6 z-20">
    <a href="{{ $dashboardUrl }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-300 hover:text-white transition-all bg-slate-800/90 hover:bg-slate-800 px-4 py-2.5 rounded-xl border border-slate-700/80 shadow-lg backdrop-blur-sm group">
        <svg class="w-4 h-4 text-slate-400 group-hover:text-white transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Dashboard
    </a>
</div>

<div class="w-full max-w-md">

    <!-- Header Box -->
    <div class="text-center mb-6">
        <div class="mx-auto mb-3 h-14 w-14 rounded-2xl bg-primary-600 flex items-center justify-center text-2xl font-bold text-white shadow-lg shadow-primary-500/20">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Ganti Password</h1>
        <p class="text-sm text-slate-400 mt-1">Perbarui kata sandi akun Anda untuk menjaga keamanan data.</p>
    </div>

    <!-- Form Card -->
    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-2xl p-6 sm:p-8">
        @if($errors->any())
            <div class="mb-5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 px-4 py-3 text-sm text-rose-700 dark:text-rose-300">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('ganti-password.post') }}" x-data="{ loading: false }" @submit="loading = true" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                    Password Baru <span class="text-rose-500">*</span>
                </label>
                <input type="password" name="password" required minlength="8"
                    class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                    placeholder="Minimal 8 karakter">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                    Konfirmasi Password Baru <span class="text-rose-500">*</span>
                </label>
                <input type="password" name="password_confirmation" required minlength="8"
                    class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                    placeholder="Ulangi password baru">
            </div>

            <div class="pt-2 space-y-2.5">
                <button type="submit" :disabled="loading"
                    class="w-full rounded-xl bg-slate-900 dark:bg-primary-600 py-3 text-sm font-semibold text-white hover:bg-slate-800 dark:hover:bg-primary-700 disabled:opacity-70 transition-all shadow-sm focus:ring-2 focus:ring-primary-500">
                    <span x-show="!loading">Simpan Password Baru</span>
                    <span x-show="loading" class="flex items-center justify-center gap-2" x-cloak>
                        <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Menyimpan...
                    </span>
                </button>

                <a href="{{ $dashboardUrl }}"
                    class="w-full inline-flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-700/60 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    Batal & Kembali
                </a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
