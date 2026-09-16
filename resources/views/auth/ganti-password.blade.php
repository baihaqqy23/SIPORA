<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password — SIPORNAS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-arena font-sans antialiased flex items-center justify-center p-4" x-data>
<div class="w-full max-w-sm">
    <div class="text-center mb-8">
        <div class="mx-auto mb-3 h-14 w-14 rounded-2xl bg-brand flex items-center justify-center text-2xl font-bold text-white shadow-lg">S</div>
        <h1 class="text-xl font-bold text-white">Ganti Password</h1>
        <p class="text-sm text-slate-400 mt-1">Anda diwajibkan mengganti password sebelum melanjutkan</p>
    </div>
    <div class="rounded-2xl bg-white shadow-xl p-6">
        @if($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif
        <form method="POST" action="{{ route('ganti-password.post') }}" x-data="{ loading: false }" @submit="loading = true">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Password Baru</label>
                    <input type="password" name="password" required minlength="8"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand"
                        placeholder="Minimal 8 karakter">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand">
                </div>
                <button type="submit" :disabled="loading"
                    class="w-full rounded-lg bg-brand py-2.5 text-sm font-medium text-white hover:bg-brand/90 disabled:opacity-70 transition-colors">
                    <span x-show="!loading">Simpan Password Baru</span>
                    <span x-show="loading" class="flex items-center justify-center gap-2" x-cloak>
                        <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
