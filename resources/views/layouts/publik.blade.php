<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIPORNAS') — Sistem Informasi Olahraga Nasional</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-full bg-slate-50 font-sans antialiased" x-data>

{{-- Toast --}}
@include('components.toast')

{{-- Header --}}
<header class="border-b border-slate-200 bg-white sticky top-0 z-30">
    <div class="max-w-6xl mx-auto px-4 h-14 flex items-center gap-4">
        <a href="{{ route('publik.beranda') }}" class="flex items-center gap-2 shrink-0">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand text-white font-bold text-sm">S</div>
            <span class="font-semibold text-slate-800 text-sm hidden sm:block">SIPORNAS</span>
        </a>

        <nav class="flex items-center gap-1 text-sm ml-4">
            <a href="{{ route('publik.beranda') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('publik.beranda') ? 'text-brand font-medium' : 'text-slate-500 hover:text-slate-800' }}">Beranda</a>
            <a href="{{ route('publik.jadwal') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('publik.jadwal') ? 'text-brand font-medium' : 'text-slate-500 hover:text-slate-800' }}">Jadwal</a>
            <a href="{{ route('publik.hasil') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('publik.hasil') ? 'text-brand font-medium' : 'text-slate-500 hover:text-slate-800' }}">Hasil</a>
            <a href="{{ route('publik.klasemen') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('publik.klasemen') ? 'text-brand font-medium' : 'text-slate-500 hover:text-slate-800' }}">Klasemen</a>
            <a href="{{ route('publik.pengumuman') }}" class="px-3 py-1.5 rounded-lg hidden sm:block {{ request()->routeIs('publik.pengumuman') ? 'text-brand font-medium' : 'text-slate-500 hover:text-slate-800' }}">Pengumuman</a>
        </nav>

        <div class="ml-auto">
            @auth
                <a href="{{ match(auth()->user()->role) { 'admin' => route('admin.dashboard'), 'pj_cabor' => route('cabor.dashboard'), 'kontingen' => route('kontingen.dashboard'), default => '/' } }}"
                   class="text-sm text-brand font-medium hover:underline">
                    Dashboard →
                </a>
            @else
                <a href="{{ route('login') }}" class="text-sm bg-brand text-white px-4 py-1.5 rounded-lg hover:bg-brand/90 font-medium">
                    Masuk
                </a>
            @endauth
        </div>
    </div>
</header>

<main class="max-w-6xl mx-auto px-4 py-6">
    @yield('content')
</main>

<footer class="border-t border-slate-200 mt-12 py-6 bg-white">
    <div class="max-w-6xl mx-auto px-4 text-center text-xs text-slate-400">
        &copy; {{ date('Y') }} SIPORNAS — Dinas Pemuda dan Olahraga. Seluruh data dilindungi sesuai UU No. 27/2022 tentang Perlindungan Data Pribadi.
    </div>
</footer>

@stack('scripts')
</body>
</html>
