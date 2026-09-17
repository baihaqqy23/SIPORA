<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIPORNAS') — Sistem Informasi Olahraga Nasional</title>
    <meta name="description" content="@yield('meta_description', 'SIPORNAS - Sistem Informasi Manajemen Event Olahraga Nasional Dinas Pemuda dan Olahraga')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="h-full bg-slate-50 text-slate-900 font-sans antialiased" x-data="{ sidebarOpen: false }" @keydown.escape="sidebarOpen = false">

{{-- Flash Toast Notification --}}
<div
    x-data="{
        show: false,
        type: 'success',
        message: '',
        init() {
            @if(session('success'))
                this.type = 'success';
                this.message = '{{ addslashes(session('success')) }}';
                this.show = true;
            @elseif(session('error'))
                this.type = 'error';
                this.message = '{{ addslashes(session('error')) }}';
                this.show = true;
            @elseif(session('warning'))
                this.type = 'warning';
                this.message = '{{ addslashes(session('warning')) }}';
                this.show = true;
            @endif
            if(this.show) { setTimeout(() => this.show = false, 4000); }
        }
    }"
    class="fixed top-4 right-4 z-[100]"
>
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-[-10px]"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-end="opacity-0 translate-y-[-10px]"
        class="flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg text-sm font-medium min-w-[260px] max-w-sm"
        :class="{
            'bg-emerald-600 text-white': type === 'success',
            'bg-red-600 text-white': type === 'error',
            'bg-amber-500 text-white': type === 'warning'
        }"
    >
        <span x-show="type === 'success'">✓</span>
        <span x-show="type === 'error'">✕</span>
        <span x-show="type === 'warning'">⚠</span>
        <span x-text="message"></span>
        <button @click="show = false" class="ml-auto opacity-70 hover:opacity-100">✕</button>
    </div>
</div>

{{-- Sidebar Off-Canvas (Mobile) --}}
<div
    x-show="sidebarOpen"
    x-transition:enter="transition-opacity ease-linear duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="sidebarOpen = false"
    class="fixed inset-0 z-40 bg-black/50 md:hidden"
    x-cloak
></div>

<div class="flex h-full">
    {{-- Sidebar (shadcn/ui style) --}}
    <aside
        class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-[#fafafa] border-r border-neutral-200 text-neutral-900 transition-transform duration-200 md:relative md:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
        x-data="{
            openMaster: {{ (request()->routeIs('admin.event.*') || request()->routeIs('admin.cabor.*') || request()->routeIs('admin.nomor-lomba.*') || request()->routeIs('admin.venue.*') || request()->routeIs('admin.lapangan.*')) ? 'true' : 'false' }},
            openPeserta: {{ (request()->routeIs('admin.kontingen.*') || request()->routeIs('admin.atlet.*') || request()->routeIs('admin.verifikasi.*')) ? 'true' : 'false' }},
            openKompetisi: {{ (request()->routeIs('admin.drawing.*') || request()->routeIs('admin.bracket.*') || request()->routeIs('admin.papan-jadwal.*') || request()->routeIs('admin.rundown.*') || request()->routeIs('admin.acara-rundown.*') || request()->routeIs('admin.panitia.*') || request()->routeIs('admin.penugasan.*')) ? 'true' : 'false' }},
            openHasil: {{ (request()->routeIs('admin.hasil.*') || request()->routeIs('admin.klasemen.*') || request()->routeIs('admin.laporan.*') || request()->routeIs('admin.audit-log.*')) ? 'true' : 'false' }}
        }"
        x-cloak
    >
        {{-- Header / Workspace Switcher (shadcn style) --}}
        <div class="p-2 border-b border-neutral-200/80">
            <div class="flex items-center justify-between gap-2 p-1.5 rounded-lg hover:bg-neutral-100/80 transition-colors cursor-pointer group">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-neutral-900 text-white shadow-xs shrink-0">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/>
                            <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/>
                            <path d="M4 22h16"/>
                            <path d="M10 14.66V17c0 .55-.45 1-1 1H7v4h10v-4h-2c-.55 0-1-.45-1-1v-2.34"/>
                            <path d="M6 4h12a2 2 0 0 1 2 2v3a6 6 0 0 1-6 6h0a6 6 0 0 1-6-6V6a2 2 0 0 1 2-2Z"/>
                        </svg>
                    </div>
                    <div class="flex flex-col text-left min-w-0">
                        <span class="text-sm font-semibold text-neutral-900 leading-tight truncate">SIPORNAS</span>
                        <span class="text-[11px] text-neutral-500 font-normal leading-tight truncate">Panel Administrator</span>
                    </div>
                </div>
                <div class="flex items-center gap-1 shrink-0">
                    <svg class="h-4 w-4 text-neutral-400 group-hover:text-neutral-600 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m7 15 5 5 5-5"/>
                        <path d="m7 9 5-5 5 5"/>
                    </svg>
                    <button @click.stop="sidebarOpen = false" class="p-1 rounded-md text-neutral-400 hover:text-neutral-700 hover:bg-neutral-200/60 md:hidden">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Navigation Menu (Grouped & Hierarchical) --}}
        <nav class="flex-1 overflow-y-auto px-2 py-3 space-y-4 text-sm select-none">
            {{-- Group 1: Platform --}}
            <div>
                <div class="px-2.5 pb-1.5 text-[11px] font-semibold tracking-wider text-neutral-400 uppercase">
                    Platform
                </div>
                <div class="space-y-1">
                    {{-- Dashboard --}}
                    @php $activeDash = request()->routeIs('admin.dashboard'); @endphp
                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="flex items-center justify-between gap-3 px-2.5 py-1.5 rounded-lg text-sm transition-all duration-150 {{ $activeDash ? 'bg-white text-neutral-900 shadow-xs border border-neutral-200/90 font-medium' : 'text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100/80' }}"
                    >
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="h-4 w-4 shrink-0 {{ $activeDash ? 'text-neutral-900' : 'text-neutral-500' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="7" height="9" x="3" y="3" rx="1"/>
                                <rect width="7" height="5" x="14" y="3" rx="1"/>
                                <rect width="7" height="9" x="14" y="12" rx="1"/>
                                <rect width="7" height="5" x="3" y="16" rx="1"/>
                            </svg>
                            <span class="truncate">Dashboard</span>
                        </div>
                    </a>

                    {{-- Pengumuman --}}
                    @php $activePengumuman = request()->routeIs('admin.pengumuman.*'); @endphp
                    <a
                        href="{{ route('admin.pengumuman.index') }}"
                        class="flex items-center justify-between gap-3 px-2.5 py-1.5 rounded-lg text-sm transition-all duration-150 {{ $activePengumuman ? 'bg-white text-neutral-900 shadow-xs border border-neutral-200/90 font-medium' : 'text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100/80' }}"
                    >
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="h-4 w-4 shrink-0 {{ $activePengumuman ? 'text-neutral-900' : 'text-neutral-500' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m3 11 18-5v12L3 14v-3z"/>
                                <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>
                            </svg>
                            <span class="truncate">Pengumuman</span>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Group 2: Data Master --}}
            <div>
                <div class="px-2.5 pb-1.5 text-[11px] font-semibold tracking-wider text-neutral-400 uppercase">
                    Manajemen Data
                </div>
                <div class="space-y-1">
                    @php
                        $isMasterGroupActive = request()->routeIs('admin.event.*') || request()->routeIs('admin.cabor.*') || request()->routeIs('admin.nomor-lomba.*') || request()->routeIs('admin.venue.*') || request()->routeIs('admin.lapangan.*');
                    @endphp
                    <button
                        type="button"
                        @click="openMaster = !openMaster"
                        class="w-full flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-lg text-sm transition-all duration-150 {{ $isMasterGroupActive ? 'text-neutral-900 font-medium bg-neutral-100/60' : 'text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100/80' }}"
                    >
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="h-4 w-4 shrink-0 {{ $isMasterGroupActive ? 'text-neutral-900' : 'text-neutral-500' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <ellipse cx="12" cy="5" rx="9" ry="3"/>
                                <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
                                <path d="M3 12c0 1.66 4 3 9 3s9-1.34 9-3"/>
                            </svg>
                            <span class="truncate">Data Master</span>
                        </div>
                        <svg class="h-4 w-4 text-neutral-400 shrink-0 transition-transform duration-200" :class="openMaster ? 'rotate-90 text-neutral-700' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </button>

                    <div x-show="openMaster" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="ml-4 pl-3.5 my-1 space-y-0.5 border-l border-neutral-200" x-cloak>
                        @php $activeEvent = request()->routeIs('admin.event.*'); @endphp
                        <a
                            href="{{ route('admin.event.index') }}"
                            class="flex items-center gap-2 py-1.5 px-2 text-xs rounded-md transition-colors {{ $activeEvent ? 'text-neutral-900 font-semibold bg-white border border-neutral-200 shadow-2xs' : 'text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100/70' }}"
                        >
                            <span class="h-1.5 w-1.5 rounded-full {{ $activeEvent ? 'bg-neutral-900' : 'bg-neutral-300' }}"></span>
                            <span class="truncate">Event & Kejuaraan</span>
                        </a>

                        @php $activeCabor = request()->routeIs('admin.cabor.*') || request()->routeIs('admin.nomor-lomba.*'); @endphp
                        <a
                            href="{{ route('admin.cabor.index') }}"
                            class="flex items-center gap-2 py-1.5 px-2 text-xs rounded-md transition-colors {{ $activeCabor ? 'text-neutral-900 font-semibold bg-white border border-neutral-200 shadow-2xs' : 'text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100/70' }}"
                        >
                            <span class="h-1.5 w-1.5 rounded-full {{ $activeCabor ? 'bg-neutral-900' : 'bg-neutral-300' }}"></span>
                            <span class="truncate">Cabor & Nomor Lomba</span>
                        </a>

                        @php $activeVenue = request()->routeIs('admin.venue.*') || request()->routeIs('admin.lapangan.*'); @endphp
                        <a
                            href="{{ route('admin.venue.index') }}"
                            class="flex items-center gap-2 py-1.5 px-2 text-xs rounded-md transition-colors {{ $activeVenue ? 'text-neutral-900 font-semibold bg-white border border-neutral-200 shadow-2xs' : 'text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100/70' }}"
                        >
                            <span class="h-1.5 w-1.5 rounded-full {{ $activeVenue ? 'bg-neutral-900' : 'bg-neutral-300' }}"></span>
                            <span class="truncate">Venue & Lapangan</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Group 3: Kepesertaan --}}
            <div>
                <div class="px-2.5 pb-1.5 text-[11px] font-semibold tracking-wider text-neutral-400 uppercase">
                    Kepesertaan
                </div>
                <div class="space-y-1">
                    @php
                        $isPesertaGroupActive = request()->routeIs('admin.kontingen.*') || request()->routeIs('admin.atlet.*') || request()->routeIs('admin.verifikasi.*');
                    @endphp
                    <button
                        type="button"
                        @click="openPeserta = !openPeserta"
                        class="w-full flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-lg text-sm transition-all duration-150 {{ $isPesertaGroupActive ? 'text-neutral-900 font-medium bg-neutral-100/60' : 'text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100/80' }}"
                    >
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="h-4 w-4 shrink-0 {{ $isPesertaGroupActive ? 'text-neutral-900' : 'text-neutral-500' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <span class="truncate">Kontingen & Atlet</span>
                        </div>
                        <svg class="h-4 w-4 text-neutral-400 shrink-0 transition-transform duration-200" :class="openPeserta ? 'rotate-90 text-neutral-700' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </button>

                    <div x-show="openPeserta" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="ml-4 pl-3.5 my-1 space-y-0.5 border-l border-neutral-200" x-cloak>
                        @php $activeKontingen = request()->routeIs('admin.kontingen.*'); @endphp
                        <a
                            href="{{ route('admin.kontingen.index') }}"
                            class="flex items-center gap-2 py-1.5 px-2 text-xs rounded-md transition-colors {{ $activeKontingen ? 'text-neutral-900 font-semibold bg-white border border-neutral-200 shadow-2xs' : 'text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100/70' }}"
                        >
                            <span class="h-1.5 w-1.5 rounded-full {{ $activeKontingen ? 'bg-neutral-900' : 'bg-neutral-300' }}"></span>
                            <span class="truncate">Daftar Kontingen</span>
                        </a>

                        @php $activeAtlet = request()->routeIs('admin.atlet.*'); @endphp
                        <a
                            href="{{ route('admin.atlet.index') }}"
                            class="flex items-center gap-2 py-1.5 px-2 text-xs rounded-md transition-colors {{ $activeAtlet ? 'text-neutral-900 font-semibold bg-white border border-neutral-200 shadow-2xs' : 'text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100/70' }}"
                        >
                            <span class="h-1.5 w-1.5 rounded-full {{ $activeAtlet ? 'bg-neutral-900' : 'bg-neutral-300' }}"></span>
                            <span class="truncate">Database Atlet</span>
                        </a>

                        @php $activeVerifikasi = request()->routeIs('admin.verifikasi.*'); @endphp
                        <a
                            href="{{ route('admin.verifikasi.index') }}"
                            class="flex items-center gap-2 py-1.5 px-2 text-xs rounded-md transition-colors {{ $activeVerifikasi ? 'text-neutral-900 font-semibold bg-white border border-neutral-200 shadow-2xs' : 'text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100/70' }}"
                        >
                            <span class="h-1.5 w-1.5 rounded-full {{ $activeVerifikasi ? 'bg-neutral-900' : 'bg-neutral-300' }}"></span>
                            <span class="truncate">Verifikasi Berkas</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Group: Kepanitiaan & Akun PJ Cabor --}}
            <div>
                <div class="px-2.5 pb-1.5 text-[11px] font-semibold tracking-wider text-neutral-400 uppercase">
                    Kepanitiaan & Akun
                </div>
                <div class="space-y-1">
                    @php $activePanitia = request()->routeIs('admin.panitia.*') || request()->routeIs('admin.penugasan.*'); @endphp
                    <a
                        href="{{ route('admin.panitia.index') }}"
                        class="flex items-center justify-between gap-3 px-2.5 py-1.5 rounded-lg text-sm transition-all duration-150 {{ $activePanitia ? 'bg-white text-neutral-900 shadow-xs border border-neutral-200/90 font-medium' : 'text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100/80' }}"
                    >
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="h-4 w-4 shrink-0 {{ $activePanitia ? 'text-neutral-900' : 'text-neutral-500' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <span class="truncate">Panitia & Akun PJ Cabor</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-blue-50 text-blue-700">PJ Cabor</span>
                    </a>
                </div>
            </div>

            {{-- Group 4: Pertandingan & Jadwal --}}
            <div>
                <div class="px-2.5 pb-1.5 text-[11px] font-semibold tracking-wider text-neutral-400 uppercase">
                    Pertandingan
                </div>
                <div class="space-y-1">
                    @php
                        $isKompetisiGroupActive = request()->routeIs('admin.drawing.*') || request()->routeIs('admin.bracket.*') || request()->routeIs('admin.papan-jadwal.*') || request()->routeIs('admin.rundown.*') || request()->routeIs('admin.acara-rundown.*') || request()->routeIs('admin.panitia.*') || request()->routeIs('admin.penugasan.*');
                    @endphp
                    <button
                        type="button"
                        @click="openKompetisi = !openKompetisi"
                        class="w-full flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-lg text-sm transition-all duration-150 {{ $isKompetisiGroupActive ? 'text-neutral-900 font-medium bg-neutral-100/60' : 'text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100/80' }}"
                    >
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="h-4 w-4 shrink-0 {{ $isKompetisiGroupActive ? 'text-neutral-900' : 'text-neutral-500' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="4" rx="2"/>
                                <line x1="16" x2="16" y1="2" y2="6"/>
                                <line x1="8" x2="8" y1="2" y2="6"/>
                                <line x1="3" x2="21" y1="10" y2="10"/>
                                <path d="M8 14h.01"/>
                                <path d="M12 14h.01"/>
                                <path d="M16 14h.01"/>
                            </svg>
                            <span class="truncate">Jadwal & Kompetisi</span>
                        </div>
                        <svg class="h-4 w-4 text-neutral-400 shrink-0 transition-transform duration-200" :class="openKompetisi ? 'rotate-90 text-neutral-700' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </button>

                    <div x-show="openKompetisi" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="ml-4 pl-3.5 my-1 space-y-0.5 border-l border-neutral-200" x-cloak>
                        @php $activeDrawing = request()->routeIs('admin.drawing.*') || request()->routeIs('admin.bracket.*'); @endphp
                        <a
                            href="{{ route('admin.drawing.index') }}"
                            class="flex items-center gap-2 py-1.5 px-2 text-xs rounded-md transition-colors {{ $activeDrawing ? 'text-neutral-900 font-semibold bg-white border border-neutral-200 shadow-2xs' : 'text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100/70' }}"
                        >
                            <span class="h-1.5 w-1.5 rounded-full {{ $activeDrawing ? 'bg-neutral-900' : 'bg-neutral-300' }}"></span>
                            <span class="truncate">Drawing & Bracket</span>
                        </a>

                        @php $activeJadwal = request()->routeIs('admin.papan-jadwal.*'); @endphp
                        <a
                            href="{{ route('admin.papan-jadwal.index') }}"
                            class="flex items-center gap-2 py-1.5 px-2 text-xs rounded-md transition-colors {{ $activeJadwal ? 'text-neutral-900 font-semibold bg-white border border-neutral-200 shadow-2xs' : 'text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100/70' }}"
                        >
                            <span class="h-1.5 w-1.5 rounded-full {{ $activeJadwal ? 'bg-neutral-900' : 'bg-neutral-300' }}"></span>
                            <span class="truncate">Papan Jadwal</span>
                        </a>

                        @php $activeRundown = request()->routeIs('admin.rundown.*') || request()->routeIs('admin.acara-rundown.*'); @endphp
                        <a
                            href="{{ route('admin.rundown.index') }}"
                            class="flex items-center gap-2 py-1.5 px-2 text-xs rounded-md transition-colors {{ $activeRundown ? 'text-neutral-900 font-semibold bg-white border border-neutral-200 shadow-2xs' : 'text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100/70' }}"
                        >
                            <span class="h-1.5 w-1.5 rounded-full {{ $activeRundown ? 'bg-neutral-900' : 'bg-neutral-300' }}"></span>
                            <span class="truncate">Rundown Acara</span>
                        </a>

                    </div>
                </div>
            </div>

            {{-- Group 5: Hasil & Laporan --}}
            <div>
                <div class="px-2.5 pb-1.5 text-[11px] font-semibold tracking-wider text-neutral-400 uppercase">
                    Hasil & Laporan
                </div>
                <div class="space-y-1">
                    @php
                        $isHasilGroupActive = request()->routeIs('admin.hasil.*') || request()->routeIs('admin.klasemen.*') || request()->routeIs('admin.laporan.*') || request()->routeIs('admin.audit-log.*');
                    @endphp
                    <button
                        type="button"
                        @click="openHasil = !openHasil"
                        class="w-full flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-lg text-sm transition-all duration-150 {{ $isHasilGroupActive ? 'text-neutral-900 font-medium bg-neutral-100/60' : 'text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100/80' }}"
                    >
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="h-4 w-4 shrink-0 {{ $isHasilGroupActive ? 'text-neutral-900' : 'text-neutral-500' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" x2="18" y1="20" y2="10"/>
                                <line x1="12" x2="12" y1="20" y2="4"/>
                                <line x1="6" x2="6" y1="20" y2="14"/>
                            </svg>
                            <span class="truncate">Hasil & Evaluasi</span>
                        </div>
                        <svg class="h-4 w-4 text-neutral-400 shrink-0 transition-transform duration-200" :class="openHasil ? 'rotate-90 text-neutral-700' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </button>

                    <div x-show="openHasil" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="ml-4 pl-3.5 my-1 space-y-0.5 border-l border-neutral-200" x-cloak>
                        @php $activeHasil = request()->routeIs('admin.hasil.*'); @endphp
                        <a
                            href="{{ route('admin.hasil.index') }}"
                            class="flex items-center gap-2 py-1.5 px-2 text-xs rounded-md transition-colors {{ $activeHasil ? 'text-neutral-900 font-semibold bg-white border border-neutral-200 shadow-2xs' : 'text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100/70' }}"
                        >
                            <span class="h-1.5 w-1.5 rounded-full {{ $activeHasil ? 'bg-neutral-900' : 'bg-neutral-300' }}"></span>
                            <span class="truncate">Hasil Pertandingan</span>
                        </a>

                        @php $activeKlasemen = request()->routeIs('admin.klasemen.*'); @endphp
                        <a
                            href="{{ route('admin.klasemen.index') }}"
                            class="flex items-center gap-2 py-1.5 px-2 text-xs rounded-md transition-colors {{ $activeKlasemen ? 'text-neutral-900 font-semibold bg-white border border-neutral-200 shadow-2xs' : 'text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100/70' }}"
                        >
                            <span class="h-1.5 w-1.5 rounded-full {{ $activeKlasemen ? 'bg-neutral-900' : 'bg-neutral-300' }}"></span>
                            <span class="truncate">Klasemen Medali</span>
                        </a>

                        @php $activeLaporan = request()->routeIs('admin.laporan.*'); @endphp
                        <a
                            href="{{ route('admin.laporan.index') }}"
                            class="flex items-center gap-2 py-1.5 px-2 text-xs rounded-md transition-colors {{ $activeLaporan ? 'text-neutral-900 font-semibold bg-white border border-neutral-200 shadow-2xs' : 'text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100/70' }}"
                        >
                            <span class="h-1.5 w-1.5 rounded-full {{ $activeLaporan ? 'bg-neutral-900' : 'bg-neutral-300' }}"></span>
                            <span class="truncate">Laporan & Rekap</span>
                        </a>

                        @php $activeAudit = request()->routeIs('admin.audit-log.*'); @endphp
                        <a
                            href="{{ route('admin.audit-log.index') }}"
                            class="flex items-center gap-2 py-1.5 px-2 text-xs rounded-md transition-colors {{ $activeAudit ? 'text-neutral-900 font-semibold bg-white border border-neutral-200 shadow-2xs' : 'text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100/70' }}"
                        >
                            <span class="h-1.5 w-1.5 rounded-full {{ $activeAudit ? 'bg-neutral-900' : 'bg-neutral-300' }}"></span>
                            <span class="truncate">Log Audit Sistem</span>
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        {{-- Footer User Profile (shadcn style) --}}
        <div class="border-t border-neutral-200/80 p-2" x-data="{ userMenu: false }">
            <div class="relative">
                <button
                    type="button"
                    @click="userMenu = !userMenu"
                    @click.outside="userMenu = false"
                    class="w-full flex items-center justify-between gap-2.5 p-1.5 rounded-lg hover:bg-neutral-100/80 transition-colors text-left group"
                >
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="h-8 w-8 rounded-full bg-gradient-to-tr from-indigo-500 to-rose-500 text-white flex items-center justify-center text-xs font-semibold shadow-xs shrink-0 ring-1 ring-neutral-200">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="text-xs font-semibold text-neutral-900 leading-tight truncate">{{ auth()->user()->name }}</span>
                            <span class="text-[11px] text-neutral-500 font-normal leading-tight truncate">{{ auth()->user()->email }}</span>
                        </div>
                    </div>
                    <svg class="h-4 w-4 text-neutral-400 group-hover:text-neutral-600 shrink-0 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m7 15 5 5 5-5"/>
                        <path d="m7 9 5-5 5 5"/>
                    </svg>
                </button>

                {{-- Upward Popover Dropdown --}}
                <div
                    x-show="userMenu"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                    class="absolute bottom-full mb-2 left-0 w-60 rounded-xl border border-neutral-200 bg-white p-1.5 shadow-lg z-50 text-xs"
                    x-cloak
                >
                    <div class="px-2.5 py-2 border-b border-neutral-100 mb-1">
                        <div class="font-medium text-neutral-900 truncate">{{ auth()->user()->name }}</div>
                        <div class="text-[11px] text-neutral-400 truncate">{{ auth()->user()->email }}</div>
                        <span class="inline-flex items-center px-1.5 py-0.5 mt-1 rounded text-[10px] font-medium bg-neutral-100 text-neutral-700">Administrator</span>
                    </div>

                    <a href="{{ route('ganti-password') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900 transition-colors">
                        <svg class="h-3.5 w-3.5 text-neutral-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <span>Ganti Password</span>
                    </a>

                    <a href="{{ route('publik.beranda') }}" target="_blank" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900 transition-colors">
                        <svg class="h-3.5 w-3.5 text-neutral-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                            <polyline points="15 3 21 3 21 9"/>
                            <line x1="10" x2="21" y1="14" y2="3"/>
                        </svg>
                        <span>Kunjungi Beranda Publik</span>
                    </a>

                    <div class="border-t border-neutral-100 my-1"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-red-600 hover:bg-red-50 transition-colors text-left">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <polyline points="16 17 21 12 16 7"/>
                                <line x1="21" x2="9" y1="12" y2="12"/>
                            </svg>
                            <span>Keluar (Logout)</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="flex flex-1 flex-col overflow-hidden">
        {{-- Topbar (shadcn style) --}}
        <header class="flex h-14 items-center border-b border-neutral-200 bg-white px-4 gap-3 shrink-0">
            {{-- Sidebar Trigger [|] (shadcn Lucide panel-left) --}}
            <button
                @click="sidebarOpen = !sidebarOpen"
                type="button"
                class="flex h-8 w-8 items-center justify-center rounded-lg text-neutral-500 hover:bg-neutral-100 hover:text-neutral-900 transition-colors"
                title="Toggle Sidebar"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="18" x="3" y="3" rx="2" />
                    <path d="M9 3v18" />
                </svg>
            </button>

            <div class="h-4 w-px bg-neutral-200 hidden sm:block"></div>

            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-1.5 text-xs text-neutral-500 font-medium min-w-0">
                @yield('breadcrumb')
            </nav>

            <div class="ml-auto flex items-center gap-3">
                {{-- Notifikasi Bell --}}
                <div x-data="notifikasiBell()" class="relative">
                    <button @click="toggleOpen()" class="relative rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span x-show="unread > 0" x-text="unread" class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-brand text-[10px] font-bold text-white" x-cloak></span>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 top-10 z-50 w-72 rounded-lg border border-slate-200 bg-white shadow-lg" x-cloak>
                        <div class="border-b border-slate-100 px-4 py-2 flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-600">Notifikasi</span>
                            <a href="{{ route('notifikasi.baca-semua') }}" class="text-[10px] text-brand hover:underline" x-show="unread > 0">Tandai semua dibaca</a>
                        </div>
                        <div class="max-h-60 overflow-y-auto divide-y divide-slate-50">
                            <template x-for="n in items" :key="n.id">
                                <a :href="n.url_tujuan || '#'" class="flex gap-3 px-4 py-3 hover:bg-slate-50 transition-colors" :class="{ 'bg-blue-50': !n.dibaca_pada }">
                                    <div class="shrink-0 h-2 w-2 rounded-full mt-1.5" :class="n.dibaca_pada ? 'bg-slate-300' : 'bg-brand'"></div>
                                    <div>
                                        <div class="text-xs font-medium text-slate-800" x-text="n.judul"></div>
                                        <div class="text-[11px] text-slate-500 line-clamp-1" x-text="n.pesan"></div>
                                    </div>
                                </a>
                            </template>
                            <div x-show="items.length === 0" class="px-4 py-6 text-center text-xs text-slate-400">Tidak ada notifikasi</div>
                        </div>
                        <div class="border-t border-slate-100 px-4 py-2">
                            <a href="{{ route('notifikasi.index') }}" class="text-xs text-brand hover:underline">Lihat semua</a>
                        </div>
                    </div>
                </div>

                {{-- Profil Dropdown --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm text-slate-600 hover:bg-slate-100">
                        <div class="h-7 w-7 rounded-full bg-brand text-white flex items-center justify-center text-xs font-semibold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <span class="hidden sm:block max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                        <svg class="h-3 w-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition class="absolute right-0 top-10 z-50 w-48 rounded-lg border border-slate-200 bg-white shadow-lg py-1" x-cloak>
                        <a href="{{ route('ganti-password') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Ganti Password</a>
                        <div class="border-t border-slate-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Keluar</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

<script>
function notifikasiBell() {
    return {
        open: false,
        unread: 0,
        items: [],
        async toggleOpen() {
            this.open = !this.open;
            if (this.open) await this.fetchNotifikasi();
        },
        async fetchNotifikasi() {
            try {
                const res = await fetch('{{ route('internal.notifikasi') }}');
                const data = await res.json();
                this.items = data.items || [];
                this.unread = data.unread || 0;
            } catch(e) {}
        },
        init() {
            this.fetchNotifikasi();
            setInterval(() => this.fetchNotifikasi(), 60000);
        }
    }
}
</script>

@stack('scripts')
</body>
</html>
