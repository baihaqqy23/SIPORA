@extends('layouts.kontingen')

@section('title', 'Jadwal Pertandingan Kontingen')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-xl font-bold text-slate-800">Jadwal Tanding Kontingen</h1>
        <p class="text-xs text-slate-500 mt-1">Jadwal pertandingan atlet kontingen {{ $kontingen->nama }}.</p>
    </div>

    @if($pertandingan->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center text-slate-400">
            <p class="text-base font-semibold text-slate-600">Belum ada jadwal pertandingan.</p>
            <p class="text-xs text-slate-400 mt-1">Jadwal akan muncul setelah proses drawing dan penyusunan jadwal selesai.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($pertandingan as $tanggal => $matches)
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    <div class="bg-slate-50 px-6 py-3 border-b border-slate-200 flex items-center justify-between">
                        <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <span>📅</span> {{ $tanggal !== 'Belum Ditentukan' ? \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') : $tanggal }}
                        </h2>
                        <span class="text-xs font-semibold text-slate-500">{{ $matches->count() }} Pertandingan</span>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @foreach($matches as $m)
                            <div class="p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-slate-50/60 transition">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="rounded bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-800 uppercase">
                                            {{ $m->nomorLomba?->cabangOlahraga?->nama }}
                                        </span>
                                        <span class="text-xs font-semibold text-slate-700">{{ $m->nomorLomba?->nama }}</span>
                                    </div>
                                    <p class="text-sm font-bold text-slate-800">{{ $m->babak }} &bull; {{ $m->lapangan?->nama }} ({{ $m->lapangan?->venue?->nama }})</p>
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
