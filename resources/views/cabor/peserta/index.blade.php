@extends('layouts.cabor')

@section('title', 'Data Peserta & Verifikasi — ' . $cabor->nama)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Peserta & Verifikasi Berkas</h1>
            <p class="text-xs text-slate-500 mt-0.5">Verifikasi keabsahan atlet dan pendaftaran nomor lomba untuk cabor {{ $cabor->nama }}.</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm">
        <form method="GET" action="{{ route('cabor.peserta.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atlet, NIK, atau kontingen..." class="w-full text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2">
            </div>
            <div class="w-full sm:w-48">
                <select name="nomor_lomba_id" onchange="this.form.submit()" class="w-full text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2">
                    <option value="">Semua Nomor Lomba</option>
                    @foreach($cabor->nomorLomba as $nl)
                        <option value="{{ $nl->id }}" {{ request('nomor_lomba_id') == $nl->id ? 'selected' : '' }}>
                            {{ $nl->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-40">
                <select name="status" onchange="this.form.submit()" class="w-full text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 rounded-lg">
                Filter
            </button>
        </form>
    </div>

    {{-- Peserta Table --}}
    @if($pendaftaran->isEmpty())
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-12 text-center shadow-sm">
            <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Tidak ada data pendaftaran</p>
            <p class="text-xs text-slate-400 mt-1">Pendaftaran atlet yang masuk pada nomor lomba ini akan muncul di sini.</p>
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-semibold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-4 py-3">Nama Atlet / Tim</th>
                            <th class="px-4 py-3">Kontingen</th>
                            <th class="px-4 py-3">Nomor Lomba</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Catatan</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($pendaftaran as $p)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                                <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">
                                    {{ $p->atlet?->nama ?? $p->timKontingen?->nama_tim }}
                                    @if($p->atlet?->nik)
                                        <div class="text-[10px] text-slate-400 font-normal">NIK: {{ $p->atlet->nik }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                    {{ $p->atlet?->kontingen?->nama ?? $p->timKontingen?->kontingen?->nama }}
                                </td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                    {{ $p->nomorLomba?->nama }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2.5 py-0.5 rounded text-[10px] font-bold uppercase
                                        @if($p->status === 'disetujui') bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300
                                        @elseif($p->status === 'ditolak') bg-rose-100 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300
                                        @else bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300
                                        @endif">
                                        {{ $p->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-500 text-[11px] max-w-xs truncate">
                                    {{ $p->catatan ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('cabor.peserta.verifikasi', $p) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white bg-slate-900 dark:bg-primary-600 hover:bg-slate-800 rounded-lg shadow-xs transition-colors">
                                        Verifikasi
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($pendaftaran->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                    {{ $pendaftaran->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
