<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIPORNAS') — Portal Kontingen</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-full bg-slate-50 font-sans antialiased" x-data>

@include('components.toast')

{{-- Topbar --}}
<header class="border-b border-slate-200 bg-white sticky top-0 z-30">
    <div class="max-w-5xl mx-auto px-4 h-14 flex items-center gap-4">
        <a href="{{ route('kontingen.dashboard') }}" class="flex items-center gap-2 shrink-0">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand text-white font-bold text-sm">S</div>
            <div class="hidden sm:block">
                <div class="text-xs font-semibold text-slate-800">SIPORNAS</div>
                <div class="text-[10px] text-slate-400">Portal Kontingen</div>
            </div>
        </a>

        {{-- Kontingen Name Badge --}}
        @if(auth()->user()->kontingen)
        <div class="hidden sm:flex items-center gap-2 text-xs text-slate-500">
            <span class="text-slate-300">|</span>
            <span class="font-medium text-slate-700">{{ auth()->user()->kontingen->nama }}</span>
            <x-status-badge :status="auth()->user()->kontingen->status" />
        </div>
        @endif

        {{-- Nav --}}
        <nav class="flex items-center gap-1 text-sm ml-2">
            <a href="{{ route('kontingen.dashboard') }}" class="px-3 py-1.5 rounded-lg text-xs {{ request()->routeIs('kontingen.dashboard') ? 'bg-slate-100 text-slate-800 font-medium' : 'text-slate-500 hover:text-slate-700' }}">Dashboard</a>
            <a href="{{ route('kontingen.atlet.index') }}" class="px-3 py-1.5 rounded-lg text-xs {{ request()->routeIs('kontingen.atlet.*') ? 'bg-slate-100 text-slate-800 font-medium' : 'text-slate-500 hover:text-slate-700' }}">Atlet</a>
            <a href="{{ route('kontingen.pendaftaran.index') }}" class="px-3 py-1.5 rounded-lg text-xs {{ request()->routeIs('kontingen.pendaftaran.*') ? 'bg-slate-100 text-slate-800 font-medium' : 'text-slate-500 hover:text-slate-700' }}">Pendaftaran</a>
            <a href="{{ route('kontingen.jadwal.index') }}" class="px-3 py-1.5 rounded-lg text-xs {{ request()->routeIs('kontingen.jadwal.*') ? 'bg-slate-100 text-slate-800 font-medium' : 'text-slate-500 hover:text-slate-700' }}">Jadwal</a>
        </nav>

        <div class="ml-auto flex items-center gap-3">
            {{-- Notifikasi --}}
            <div x-data="notifikasiBell()" class="relative">
                <button @click="toggleOpen()" class="relative rounded-lg p-2 text-slate-500 hover:bg-slate-100">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span x-show="unread > 0" x-text="unread" class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-brand text-[10px] font-bold text-white" x-cloak></span>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 top-10 z-50 w-72 rounded-lg border border-slate-200 bg-white shadow-lg" x-cloak>
                    <div class="border-b border-slate-100 px-4 py-2"><span class="text-xs font-semibold text-slate-600">Notifikasi</span></div>
                    <div class="max-h-60 overflow-y-auto divide-y divide-slate-50">
                        <template x-for="n in items" :key="n.id">
                            <a :href="n.url_tujuan || '#'" class="flex gap-3 px-4 py-3 hover:bg-slate-50" :class="{ 'bg-blue-50': !n.dibaca_pada }">
                                <div class="shrink-0 h-2 w-2 rounded-full mt-1.5" :class="n.dibaca_pada ? 'bg-slate-300' : 'bg-brand'"></div>
                                <div>
                                    <div class="text-xs font-medium" x-text="n.judul"></div>
                                    <div class="text-[11px] text-slate-500 line-clamp-1" x-text="n.pesan"></div>
                                </div>
                            </a>
                        </template>
                        <div x-show="items.length === 0" class="px-4 py-6 text-center text-xs text-slate-400">Tidak ada notifikasi</div>
                    </div>
                </div>
            </div>

            {{-- Profil --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm text-slate-600 hover:bg-slate-100">
                    <div class="h-7 w-7 rounded-full bg-brand text-white flex items-center justify-center text-xs font-semibold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <svg class="h-3 w-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-transition class="absolute right-0 top-10 z-50 w-48 rounded-lg border border-slate-200 bg-white shadow-lg py-1" x-cloak>
                    <div class="px-4 py-2 border-b border-slate-100">
                        <div class="text-xs font-medium text-slate-700 truncate">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-slate-400">Ofisial Kontingen</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Keluar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<main class="max-w-5xl mx-auto px-4 py-6">
    @yield('content')
</main>

<script>
function notifikasiBell() {
    return {
        open: false, unread: 0, items: [],
        async toggleOpen() { this.open = !this.open; if (this.open) await this.fetchNotifikasi(); },
        async fetchNotifikasi() {
            try { const res = await fetch('{{ route('internal.notifikasi') }}'); const data = await res.json(); this.items = data.items || []; this.unread = data.unread || 0; } catch(e) {}
        },
        init() { this.fetchNotifikasi(); setInterval(() => this.fetchNotifikasi(), 60000); }
    }
}
</script>

@stack('scripts')
</body>
</html>
