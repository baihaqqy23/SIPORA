<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Akses Ditolak | SIPORNAS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center bg-slate-50 p-6 font-sans antialiased text-slate-800">
    <div class="max-w-md w-full text-center space-y-6 rounded-2xl bg-white p-8 shadow-xl border border-slate-100">
        <div class="inline-flex h-20 w-20 items-center justify-center rounded-2xl bg-red-100 text-red-600 text-3xl font-black">
            403
        </div>
        <div class="space-y-2">
            <h1 class="text-xl font-bold text-slate-900">Akses Ditolak</h1>
            <p class="text-sm text-slate-500 leading-relaxed">
                {{ $exception->getMessage() ?: 'Anda tidak memiliki hak akses (role) untuk membuka halaman ini. Silakan kembali ke dashboard akun Anda.' }}
            </p>
        </div>
        <div class="pt-2 flex flex-col sm:flex-row gap-3 justify-center">
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="rounded-xl bg-red-700 px-5 py-2.5 text-xs font-bold text-white shadow hover:bg-red-800 transition">
                        Dashboard Admin
                    </a>
                @elseif(auth()->user()->isPjCabor())
                    <a href="{{ route('cabor.dashboard') }}" class="rounded-xl bg-red-700 px-5 py-2.5 text-xs font-bold text-white shadow hover:bg-red-800 transition">
                        Dashboard Cabor
                    </a>
                @elseif(auth()->user()->isKontingen())
                    <a href="{{ route('kontingen.dashboard') }}" class="rounded-xl bg-red-700 px-5 py-2.5 text-xs font-bold text-white shadow hover:bg-red-800 transition">
                        Dashboard Kontingen
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="rounded-xl bg-red-700 px-5 py-2.5 text-xs font-bold text-white shadow hover:bg-red-800 transition">
                    Halaman Login
                </a>
            @endauth
            <a href="{{ url('/') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                Beranda Publik
            </a>
        </div>
    </div>
</body>
</html>
