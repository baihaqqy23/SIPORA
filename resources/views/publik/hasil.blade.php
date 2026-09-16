@extends('layouts.publik')

@section('title', 'Hasil Pertandingan')
@section('meta_description', 'Hasil pertandingan dan skor akhir Pekan Olahraga Nasional')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-800">Hasil Pertandingan</h1>
        <p class="text-sm text-slate-500 mt-1">Daftar rekapitulasi hasil dan pemenang pertandingan yang telah selesai.</p>
    </div>

    @if($pertandingan->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center text-slate-400">
            <p class="text-base font-semibold text-slate-600">Belum ada pertandingan selesai.</p>
            <p class="text-xs text-slate-400 mt-1">Hasil pertandingan akan muncul setelah diinput dan diverifikasi oleh panitia.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($pertandingan as $p)
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="rounded bg-red-100 px-2.5 py-0.5 text-xs font-bold text-red-800 uppercase">
                                {{ $p->nomorLomba?->cabangOlahraga?->nama }}
                            </span>
                            <span class="text-sm font-semibold text-slate-800">{{ $p->nomorLomba?->nama }}</span>
                        </div>
                        <span class="rounded bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">
                            {{ $p->babak }}
                        </span>
                    </div>

                    {{-- Peserta / Skor --}}
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach($p->pesertaPertandingan as $pp)
                            <div class="flex items-center justify-between rounded-xl border {{ $pp->hasil === 'menang' ? 'border-emerald-300 bg-emerald-50/50' : 'border-slate-200 bg-slate-50/50' }} p-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-bold text-slate-800">{{ $pp->peserta?->nama ?? 'Peserta' }}</p>
                                        @if($pp->hasil === 'menang')
                                            <span class="rounded-full bg-emerald-600 px-2 py-0.2 text-[10px] font-bold text-white uppercase tracking-wider">Menang</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500">{{ $pp->peserta?->kontingen?->nama ?? '' }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-xl font-extrabold text-slate-900">{{ $pp->skor ?? '-' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($p->hasilPertandingan?->keterangan)
                        <div class="rounded-xl bg-slate-50 p-3 text-xs text-slate-600 border border-slate-100">
                            <span class="font-semibold text-slate-700">Keterangan:</span> {{ $p->hasilPertandingan->keterangan }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $pertandingan->links() }}
        </div>
    @endif
</div>
@endsection
