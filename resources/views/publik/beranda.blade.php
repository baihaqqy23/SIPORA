@extends('layouts.publik')

@section('title', 'Beranda')
@section('meta_description', 'Portal resmi SIPORNAS - Sistem Informasi Manajemen Event Olahraga Nasional')

@section('content')
<div class="space-y-10">

    {{-- Hero / Event Aktif ----}}
    @if($event)
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-red-700 via-red-800 to-slate-900 p-8 text-white shadow-xl">
            <div class="relative z-10 max-w-2xl space-y-4">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/20 px-3 py-1 text-xs font-semibold backdrop-blur-sm">
                    <span class="h-2 w-2 rounded-full bg-green-400 animate-pulse"></span>
                    {{ ucfirst(str_replace('_', ' ', $event->status)) }}
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">{{ $event->nama }}</h1>
                <p class="text-sm text-red-100 line-clamp-2">{{ $event->deskripsi }}</p>
                <div class="flex flex-wrap gap-4 pt-2 text-xs text-red-200">
                    <div class="flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>{{ \Carbon\Carbon::parse($event->tanggal_mulai)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($event->tanggal_selesai)->translatedFormat('d M Y') }}</span>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3 pt-2">
                    <a href="{{ route('publik.jadwal') }}" class="rounded-lg bg-white px-4 py-2 text-xs font-bold text-red-800 shadow hover:bg-red-50 transition">
                        Lihat Jadwal
                    </a>
                    <a href="{{ route('publik.klasemen') }}" class="rounded-lg border border-white/40 bg-white/10 px-4 py-2 text-xs font-semibold text-white backdrop-blur-sm hover:bg-white/20 transition">
                        Klasemen Medali
                    </a>
                </div>
            </div>
            <div class="absolute -bottom-10 -right-10 h-64 w-64 rounded-full bg-white/5 pointer-events-none"></div>
        </div>
    @else
        <div class="rounded-2xl bg-slate-100 p-8 text-center text-slate-500">
            <h1 class="text-lg font-bold text-slate-700">Belum Ada Event Aktif</h1>
            <p class="text-xs mt-1">Nantikan informasi penyelenggaraan event olahraga berikutnya.</p>
        </div>
    @endif

    {{-- Pengumuman --}}
    @if($pengumuman->isNotEmpty())
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-slate-800">Pengumuman Terbaru</h2>
                <a href="{{ route('publik.pengumuman') }}" class="text-xs font-semibold text-red-700 hover:underline">Semua Pengumuman &rarr;</a>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($pengumuman as $p)
                    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm space-y-2">
                        <span class="inline-block rounded px-2 py-0.5 text-[10px] font-semibold bg-red-100 text-red-800 uppercase">
                            {{ $p->target }}
                        </span>
                        <h3 class="text-sm font-bold text-slate-800 line-clamp-2">{{ $p->judul }}</h3>
                        <div class="text-xs text-slate-500 line-clamp-3 leading-relaxed">{!! strip_tags($p->isi) !!}</div>
                        <p class="text-[11px] text-slate-400 pt-1">{{ $p->created_at->translatedFormat('d M Y') }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Jadwal Hari Ini & Hasil Terbaru --}}
    <div class="grid gap-8 lg:grid-cols-2">

        {{-- Jadwal Hari Ini --}}
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-slate-800">Jadwal Hari Ini</h2>
                <a href="{{ route('publik.jadwal') }}" class="text-xs font-semibold text-red-700 hover:underline">Lihat Lengkap &rarr;</a>
            </div>
            @if($pertandinganHariIni->isEmpty())
                <div class="rounded-xl border border-slate-200 bg-white p-8 text-center text-sm text-slate-400">
                    Tidak ada jadwal pertandingan hari ini.
                </div>
            @else
                <div class="space-y-3">
                    @foreach($pertandinganHariIni as $j)
                        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm flex items-center justify-between gap-4">
                            <div class="space-y-1 min-w-0">
                                <span class="text-[11px] font-semibold text-red-700 uppercase tracking-wider">
                                    {{ $j->nomorLomba?->cabangOlahraga?->nama }}
                                </span>
                                <p class="text-sm font-bold text-slate-800 truncate">{{ $j->nomorLomba?->nama }}</p>
                                <p class="text-xs text-slate-500">{{ $j->babak }} &bull; {{ $j->lapangan?->nama }} ({{ $j->lapangan?->venue?->nama }})</p>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-xs font-bold text-slate-700">{{ \Carbon\Carbon::parse($j->waktu_mulai)->format('H:i') }} WIB</span>
                                <div>
                                    <span class="inline-block mt-1 rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $j->status === 'berlangsung' ? 'bg-amber-100 text-amber-800 animate-pulse' : 'bg-slate-100 text-slate-600' }}">
                                        {{ ucfirst($j->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Hasil Terbaru (Live Poller) --}}
        <div x-data="hasilTerbaruPoller()">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-slate-800">Hasil Terbaru</h2>
                <a href="{{ route('publik.hasil') }}" class="text-xs font-semibold text-red-700 hover:underline">Semua Hasil &rarr;</a>
            </div>

            <div class="space-y-3">
                <template x-for="h in hasil" :key="h.id">
                    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-semibold text-red-700" x-text="h.cabor + ' - ' + h.nomor_lomba"></span>
                            <span class="rounded bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-600" x-text="h.babak"></span>
                        </div>
                        <div class="space-y-1">
                            <template x-for="p in h.peserta" :key="p.nama">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-medium text-slate-800" x-text="p.nama"></span>
                                    <span class="font-bold text-slate-900" x-text="p.skor ?? '-'"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
                <div x-show="hasil.length === 0" class="rounded-xl border border-slate-200 bg-white py-8 text-center text-sm text-slate-400">
                    Belum ada hasil pertandingan.
                </div>
            </div>
            <p class="text-[11px] text-slate-400 text-right mt-2">Auto-refresh setiap 30 detik</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
function hasilTerbaruPoller() {
    return {
        hasil: @json($hasilTerbaruArray),
        init() {
            setInterval(() => this.poll(), 30000);
        },
        async poll() {
            try {
                const res = await fetch("{{ route('internal.hasil-terbaru') }}");
                const data = await res.json();
                if (data && data.data) {
                    this.hasil = data.data;
                }
            } catch(e) {}
        }
    }
}
</script>
@endpush
@endsection
