<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIPORNAS') — Portal PJ Cabor</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-full bg-slate-50 dark:bg-slate-950 font-sans antialiased text-slate-900 dark:text-slate-100" x-data>

@include('components.toast')

{{-- Topbar Header --}}
<header class="border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 sticky top-0 z-30 shadow-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
        
        {{-- Brand & Role Indicator --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('cabor.dashboard') }}" class="flex items-center gap-3 shrink-0 group">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-900 dark:bg-primary-600 text-white font-bold text-sm shadow-sm group-hover:scale-105 transition-transform">
                    S
                </div>
                <div>
                    <div class="text-sm font-bold text-slate-900 dark:text-white leading-none">SIPORNAS</div>
                    <div class="text-[11px] font-medium text-slate-400 mt-0.5">Portal PJ Cabor</div>
                </div>
            </a>

            {{-- Cabor Badge --}}
            @php
                $assignedCabor = auth()->user()->caborDitugaskan();
                $firstCabor = $assignedCabor->first();
            @endphp
            @if($firstCabor)
                <div class="hidden md:flex items-center gap-2 pl-3 border-l border-slate-200 dark:border-slate-800 text-xs">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg font-semibold text-white shadow-xs" style="background-color: {{ $firstCabor->warna ?? '#2563EB' }}">
                        {{ $firstCabor->nama }} ({{ $firstCabor->singkatan }})
                    </span>
                </div>
            @endif
        </div>

        {{-- Navigation Menu --}}
        <nav class="hidden sm:flex items-center gap-1.5 text-xs font-semibold">
            <a href="{{ route('cabor.dashboard') }}" class="px-3.5 py-2 rounded-lg transition-all {{ request()->routeIs('cabor.dashboard') ? 'bg-slate-900 dark:bg-primary-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                Dashboard
            </a>
            <a href="{{ route('cabor.peserta.index') }}" class="px-3.5 py-2 rounded-lg transition-all {{ request()->routeIs('cabor.peserta.*') ? 'bg-slate-900 dark:bg-primary-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                Peserta & Verifikasi
            </a>
            <a href="{{ route('cabor.jadwal.index') }}" class="px-3.5 py-2 rounded-lg transition-all {{ request()->routeIs('cabor.jadwal.*') ? 'bg-slate-900 dark:bg-primary-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                Jadwal & Hasil
            </a>
        </nav>

        {{-- Right Controls: Notification & Profile --}}
        <div class="flex items-center gap-3">
            {{-- Notifications --}}
            <a href="{{ route('notifikasi.index') }}" class="relative rounded-xl p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </a>

            {{-- User Dropdown --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-2 rounded-xl p-1.5 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <div class="h-8 w-8 rounded-xl bg-slate-900 dark:bg-primary-600 text-white flex items-center justify-center text-xs font-bold shadow-xs">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="hidden md:block text-left">
                        <div class="text-xs font-semibold leading-none">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-slate-400 mt-0.5">Penanggung Jawab Cabor</div>
                    </div>
                    <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open" x-transition class="absolute right-0 top-12 z-50 w-52 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl py-1.5" x-cloak>
                    <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-800">
                        <div class="text-xs font-semibold text-slate-800 dark:text-white truncate">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-slate-400 truncate">{{ auth()->user()->email }}</div>
                    </div>
                    <a href="{{ route('ganti-password') }}" class="flex items-center gap-2 px-4 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        Ganti Password
                    </a>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-xs text-primary-600 dark:text-primary-400 hover:bg-slate-50 dark:hover:bg-slate-800 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Ke Dashboard Admin
                        </a>
                    @endif
                    <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-xs text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 font-medium transition-colors flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

{{-- Main Body --}}
<main class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
    @yield('content')
</main>

@stack('scripts')
</body>
</html>
