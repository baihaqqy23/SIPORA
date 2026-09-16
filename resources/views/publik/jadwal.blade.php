@extends('layouts.publik')

@section('title', 'Jadwal Pertandingan')
@section('meta_description', 'Jadwal lengkap pertandingan Pekan Olahraga Nasional per venue dan cabang olahraga')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800">Jadwal Pertandingan</h1>
            <p class="text-sm text-slate-500 mt-1">Daftar agenda pertandingan dikelompokkan berdasarkan venue dan lapangan.</p>
        </div>

        {{-- Filter Tanggal --}}
        <form method="GET" action="{{ route('publik.jadwal') }}" class="flex items-center gap-2">
            <input
                type="date"
                name="tanggal"
                value="{{ $tanggal }}"
                class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm focus:border-red-600 focus:outline-none"
                onchange="this.form.submit()"
            >
            <button type="submit" class="rounded-xl bg-red-700 px-4 py-2 text-xs font-bold text-white shadow hover:bg-red-800 transition">
                Filter
            </button>
        </form>
    </div>

    @if($pertandingan->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center text-slate-400">
            <p class="text-base font-semibold text-slate-600">Tidak ada pertandingan pada tanggal {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}.</p>
            <p class="text-xs text-slate-400 mt-1">Silakan pilih tanggal lain pada formulir di atas.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($pertandingan as $venueNama => $matchList)
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    <div class="bg-slate-100/80 px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                        <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                            <span>📍</span> {{ $venueNama }}
                        </h2>
                        <span class="text-xs font-semibold text-slate-500">{{ $matchList->count() }} Partai</span>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @foreach($matchList as $m)
                            <div class="p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-slate-50/60 transition">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="rounded bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-800 uppercase">
                                            {{ $m->nomorLomba?->cabangOlahraga?->nama }}
                                        </span>
                                        <span class="text-xs font-semibold text-slate-700">{{ $m->nomorLomba?->nama }}</span>
                                    </div>
                                    <p class="text-sm font-bold text-slate-800">{{ $m->babak }} &bull; {{ $m->lapangan?->nama }}</p>
                                </div>
                                <div class="flex items-center gap-4 shrink-0">
                                    <div class="text-right">
                                        <p class="text-xs font-bold text-slate-700">{{ \Carbon\Carbon::parse($m->waktu_mulai)->format('H:i') }} WIB</p>
                                        <p class="text-[11px] text-slate-400">Durasi: {{ $m->durasi_menit ?? 45 }}m</p>
                                    </div>
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $m->status === 'berlangsung' ? 'bg-amber-100 text-amber-800 animate-pulse' : ($m->status === 'selesai' ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-600') }}">
                                        {{ ucfirst($m->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
