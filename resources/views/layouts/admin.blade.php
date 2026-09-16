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
    {{-- Sidebar --}}
    <aside
        class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-arena text-slate-100 transition-transform duration-200 md:relative md:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
        x-cloak
    >
        {{-- Logo --}}
        <div class="flex h-14 items-center gap-2 border-b border-slate-700 px-4">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand text-white font-bold text-sm">S</div>
            <div>
                <div class="text-xs font-semibold text-white leading-tight">SIPORNAS</div>
                <div class="text-[10px] text-slate-400 leading-tight">Panel Admin Dispora</div>
            </div>
            <button @click="sidebarOpen = false" class="ml-auto text-slate-400 hover:text-white md:hidden">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto py-4 text-sm">
            @php
                $navItems = [
                    ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'squares-2x2'],
                    ['route' => 'admin.event.index', 'label' => 'Event', 'icon' => 'trophy'],
                    ['route' => 'admin.cabor.index', 'label' => 'Cabor & Nomor Lomba', 'icon' => 'flag'],
                    ['route' => 'admin.venue.index', 'label' => 'Venue & Lapangan', 'icon' => 'map-pin'],
                    ['route' => 'admin.kontingen.index', 'label' => 'Kontingen', 'icon' => 'user-group'],
                    ['route' => 'admin.atlet.index', 'label' => 'Atlet', 'icon' => 'user'],
                    ['route' => 'admin.verifikasi.index', 'label' => 'Verifikasi Pendaftaran', 'icon' => 'clipboard-document-check'],
                    ['route' => 'admin.drawing.index', 'label' => 'Drawing & Bracket', 'icon' => 'share'],
                    ['route' => 'admin.papan-jadwal.index', 'label' => 'Papan Jadwal', 'icon' => 'calendar-days'],
                    ['route' => 'admin.panitia.index', 'label' => 'Panitia', 'icon' => 'identification'],
                    ['route' => 'admin.rundown.index', 'label' => 'Rundown', 'icon' => 'clock'],
                    ['route' => 'admin.hasil.index', 'label' => 'Hasil & Medali', 'icon' => 'chart-bar'],
                    ['route' => 'admin.klasemen.index', 'label' => 'Klasemen Medali', 'icon' => 'trophy'],
                    ['route' => 'admin.pengumuman.index', 'label' => 'Pengumuman', 'icon' => 'megaphone'],
                    ['route' => 'admin.laporan.index', 'label' => 'Laporan', 'icon' => 'arrow-down-tray'],
                ];
            @endphp

            @foreach($navItems as $item)
                @php $active = request()->routeIs(rtrim($item['route'], '.index') . '*'); @endphp
                <a
                    href="{{ route($item['route']) }}"
                    class="flex items-center gap-3 px-4 py-2 mx-2 rounded-lg transition-colors {{ $active ? 'bg-brand text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}"
                >
                    @if(view()->exists('components.icons.' . $item['icon']))
                        @include('components.icons.' . $item['icon'], ['class' => 'h-4 w-4 shrink-0'])
                    @else
                        <span class="h-2 w-2 rounded-full bg-slate-400 shrink-0"></span>
                    @endif
                    <span class="truncate">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        {{-- User info --}}
        <div class="border-t border-slate-700 p-4">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-full bg-slate-600 flex items-center justify-center text-xs font-semibold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <div class="truncate text-xs font-medium text-white">{{ auth()->user()->name }}</div>
                    <div class="text-[10px] text-slate-400 uppercase">Admin Dispora</div>
                </div>
            </div>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="flex flex-1 flex-col overflow-hidden">
        {{-- Topbar --}}
        <header class="flex h-14 items-center border-b border-slate-200 bg-white px-4 gap-4 shrink-0">
            <button @click="sidebarOpen = true" class="text-slate-500 hover:text-slate-700 md:hidden">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-1 text-sm text-slate-500 min-w-0">
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
