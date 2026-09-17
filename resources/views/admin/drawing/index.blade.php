@extends('layouts.admin')

@section('title', 'Drawing & Bagan Pertandingan')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-800 font-medium">Drawing</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900">Drawing & Bagan Pertandingan (Bracket)</h1>
            <p class="text-sm text-neutral-500 mt-1">Lakukan pengundian bagan tanding untuk setiap nomor lomba cabang olahraga.</p>
        </div>
    </div>

    <!-- Filter Cabor -->
    <div class="p-4 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
        <form method="GET" action="{{ route('admin.drawing.index') }}" class="flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="w-full sm:w-80">
                <label class="block text-xs font-semibold text-neutral-700 uppercase mb-1">Filter Cabang Olahraga</label>
                <select name="cabor_id" onchange="this.form.submit()"
                        class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Cabang Olahraga</option>
                    @foreach($caborFilterList as $c)
                        <option value="{{ $c->id }}" {{ $caborId == $c->id ? 'selected' : '' }}>{{ $c->nama }}</option>
                    @endforeach
                </select>
            </div>
            @if($caborId)
                <div>
                    <a href="{{ route('admin.drawing.index') }}" class="px-3 py-2 text-xs font-semibold text-neutral-600 bg-neutral-100 hover:bg-neutral-200 rounded-lg">
                        Reset Filter
                    </a>
                </div>
            @endif
        </form>
    </div>

    <!-- List Cabor & Nomor Lomba -->
    <div class="space-y-6">
        @forelse($cabors as $cabor)
            <div class="bg-white border border-neutral-200/80 rounded-xl shadow-xs overflow-hidden">
                <div class="p-4 bg-neutral-50/80 border-b border-neutral-200/80 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-3.5 h-3.5 rounded-full" style="background-color: {{ $cabor->warna ?? '#2563eb' }}"></span>
                        <h2 class="text-base font-bold text-neutral-900">{{ $cabor->nama }}</h2>
                        @if($cabor->singkatan)
                            <span class="text-xs px-2 py-0.5 rounded bg-neutral-200 text-neutral-700 font-semibold">{{ $cabor->singkatan }}</span>
                        @endif
                    </div>
                    <span class="text-xs text-neutral-500 font-medium">{{ $cabor->nomorLomba->count() }} Nomor Lomba</span>
                </div>

                <div class="divide-y divide-neutral-200/80">
                    @forelse($cabor->nomorLomba as $nl)
                        <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-neutral-50/50 transition-colors">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-bold text-neutral-900 text-sm">{{ $nl->nama }}</h3>
                                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-neutral-100 text-neutral-700 font-medium">
                                        Format: {{ ucfirst(str_replace('_', ' ', $nl->format_pertandingan ?? 'gugur_tunggal')) }}
                                    </span>
                                </div>
                                <div class="text-xs text-neutral-500 mt-1">
                                    Gender: {{ $nl->gender === 'L' ? 'Putra' : ($nl->gender === 'P' ? 'Putri' : 'Campuran / Terbuka') }} &bull;
                                    Peserta: {{ $nl->pertandingan->count() }} Laga Terjadwal
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                @if($nl->status_bracket === 'tergenerate' || $nl->pertandingan->count() > 0)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        ✓ Bagan Siap
                                    </span>
                                    <a href="{{ route('admin.bracket.show', $nl) }}"
                                       class="px-3.5 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                                        Lihat Bagan & Bracket &rarr;
                                    </a>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-neutral-100 text-neutral-600">
                                        Belum Diundi
                                    </span>
                                    <form action="{{ route('admin.bracket.simpan', $nl) }}" method="POST"
                                          onsubmit="return confirm('Laksanakan undian (drawing) acak untuk nomor {{ $nl->nama }}?')">
                                        @csrf
                                        <button type="submit"
                                                class="px-3.5 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                                            🎲 Lakukan Drawing
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-xs text-neutral-400">
                            Belum ada nomor lomba yang ditambahkan pada cabor ini.
                        </div>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="p-12 text-center text-neutral-400 bg-white border border-neutral-200/80 rounded-xl">
                <p class="text-base font-semibold text-neutral-700">Belum ada cabang olahraga</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
