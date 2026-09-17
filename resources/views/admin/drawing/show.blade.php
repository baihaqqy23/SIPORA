@extends('layouts.admin')

@section('title', 'Bagan Bracket - ' . $nomorLomba->nama)

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <a href="{{ route('admin.drawing.index') }}" class="hover:text-neutral-800">Drawing</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-800 font-medium">{{ $nomorLomba->nama }}</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.drawing.index') }}" class="p-1 text-neutral-400 hover:text-neutral-700 rounded-lg hover:bg-neutral-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-neutral-900">
                    Bagan Bracket: {{ $nomorLomba->nama }}
                </h1>
            </div>
            <p class="text-sm text-neutral-500 mt-1 pl-7">
                Cabor: <span class="font-semibold text-neutral-800">{{ $nomorLomba->cabangOlahraga?->nama }}</span> &bull;
                Format: <span class="font-semibold text-neutral-800">{{ ucfirst(str_replace('_', ' ', $nomorLomba->format_pertandingan ?? 'gugur_tunggal')) }}</span> &bull;
                Total Peserta Sah: <span class="font-semibold text-neutral-800">{{ count($bracket['peserta_sah']) }} Peserta</span>
            </p>
        </div>

        <div class="flex items-center gap-2">
            <form action="{{ route('admin.bracket.reset', $nomorLomba) }}" method="POST"
                  onsubmit="return confirm('Apakah Anda yakin ingin mereset bagan ini? Semua jadwal pertandingan nomor ini akan dihapus.')">
                @csrf
                <button type="submit"
                        class="px-3.5 py-2 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                    Reset Bracket
                </button>
            </form>

            <form action="{{ route('admin.bracket.simpan', $nomorLomba) }}" method="POST"
                  onsubmit="return confirm('Undi ulang (re-draw) bagan ini?')">
                @csrf
                <button type="submit"
                        class="px-3.5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                    🎲 Undi Ulang
                </button>
            </form>
        </div>
    </div>

    <!-- Bracket Rounds Visualizer -->
    @if($bracket['total_pertandingan'] > 0)
        <div class="bg-white border border-neutral-200/80 rounded-xl shadow-xs p-6 overflow-x-auto">
            <h2 class="text-sm font-bold text-neutral-900 mb-4">Struktur Bagan Pertandingan</h2>

            <div class="flex gap-8 min-w-[700px] items-stretch">
                @foreach($bracket['rounds'] as $namaBabak => $matches)
                    <div class="flex-1 space-y-4 min-w-[220px]">
                        <div class="p-2.5 bg-neutral-100 rounded-lg text-center font-bold text-xs uppercase tracking-wider text-neutral-700 border border-neutral-200">
                            {{ $namaBabak }}
                        </div>

                        <div class="space-y-4">
                            @foreach($matches as $m)
                                <div class="p-3 bg-white border-2 {{ $m->status === 'selesai' ? 'border-emerald-400 bg-emerald-50/10' : 'border-neutral-200' }} rounded-xl shadow-xs text-xs space-y-2">
                                    <div class="flex items-center justify-between text-[10px] text-neutral-400 pb-1 border-b border-neutral-100">
                                        <span>Laga #{{ $m->id }}</span>
                                        <span>{{ $m->waktu_mulai ? substr($m->waktu_mulai, 0, 5) . ' WIB' : 'Belum Terjadwal' }}</span>
                                    </div>

                                    <div class="space-y-1.5">
                                        @forelse($m->pesertaPertandingan as $peserta)
                                            <div class="flex items-center justify-between p-1.5 rounded {{ $peserta->hasil === 'menang' ? 'bg-blue-100/70 font-bold text-blue-900' : 'bg-neutral-50 text-neutral-800' }}">
                                                <span class="truncate">{{ $peserta->nama }}</span>
                                                <span class="font-mono text-xs font-bold">{{ $peserta->skor ?? '-' }}</span>
                                            </div>
                                        @empty
                                            <div class="text-[11px] text-neutral-400 italic p-1">Menunggu pemenang babak sebelumnya</div>
                                        @endforelse
                                    </div>

                                    <div class="pt-1 flex items-center justify-between text-[10px]">
                                        <span class="text-neutral-500">{{ $m->lapangan?->nama ?: 'Lapangan TBD' }}</span>
                                        <a href="{{ route('admin.hasil.show', $m) }}" class="text-blue-600 hover:underline font-semibold">
                                            Kelola Skor &rarr;
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-white border border-neutral-200/80 rounded-xl shadow-xs p-12 text-center text-neutral-400">
            <p class="text-base font-bold text-neutral-700">Bagan belum digenerate</p>
            <p class="text-xs text-neutral-400 mt-1">Lakukan pengundian (drawing) untuk membuat susunan bagan dan babak pertandingan.</p>
            <form action="{{ route('admin.bracket.simpan', $nomorLomba) }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    🎲 Laksanakan Drawing Sekarang
                </button>
            </form>
        </div>
    @endif
</div>
@endsection
