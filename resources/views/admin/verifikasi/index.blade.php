@extends('layouts.admin')

@section('title', 'Verifikasi Pendaftaran Atlet')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-800">Dashboard</a>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-400">Verifikasi</span>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-lg font-semibold text-slate-800">Verifikasi Pendaftaran & Keabsahan Atlet</h1>
            <p class="text-xs text-slate-500 mt-0.5">Antrean validasi dokumen atlet dari seluruh kontingen.</p>
        </div>
    </div>

    {{-- Filter Status Tabs --}}
    <div class="flex flex-wrap gap-2 border-b border-slate-200 pb-3">
        @php $currStatus = request('status'); @endphp
        <a href="{{ route('admin.verifikasi.index') }}" class="rounded-lg px-3 py-1.5 text-xs font-semibold {{ empty($currStatus) ? 'bg-red-700 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            Semua Perlu Verifikasi ({{ $counts['menunggu'] + $counts['diverifikasi_cabor'] }})
        </a>
        <a href="{{ route('admin.verifikasi.index', ['status' => 'menunggu']) }}" class="rounded-lg px-3 py-1.5 text-xs font-semibold {{ $currStatus === 'menunggu' ? 'bg-red-700 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            Menunggu ({{ $counts['menunggu'] }})
        </a>
        <a href="{{ route('admin.verifikasi.index', ['status' => 'diverifikasi_cabor']) }}" class="rounded-lg px-3 py-1.5 text-xs font-semibold {{ $currStatus === 'diverifikasi_cabor' ? 'bg-red-700 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            Verifikasi Cabor ({{ $counts['diverifikasi_cabor'] }})
        </a>
        <a href="{{ route('admin.verifikasi.index', ['status' => 'disetujui']) }}" class="rounded-lg px-3 py-1.5 text-xs font-semibold {{ $currStatus === 'disetujui' ? 'bg-red-700 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            Disetujui ({{ $counts['disetujui'] }})
        </a>
        <a href="{{ route('admin.verifikasi.index', ['status' => 'ditolak']) }}" class="rounded-lg px-3 py-1.5 text-xs font-semibold {{ $currStatus === 'ditolak' ? 'bg-red-700 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            Ditolak ({{ $counts['ditolak'] }})
        </a>
    </div>

    @if($pendaftaran->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center text-slate-400">
            <p class="text-base font-semibold text-slate-600">Tidak ada data pendaftaran.</p>
            <p class="text-xs text-slate-400 mt-1">Seluruh berkas dalam filter ini telah selesai diproses.</p>
        </div>
    @else
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-4 py-3">Atlet & Kontingen</th>
                            <th class="px-4 py-3">Nomor Lomba</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Tanggal Submit</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pendaftaran as $p)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-4 py-3">
                                    <p class="font-bold text-slate-800 text-sm">{{ $p->atlet?->nama ?? $p->timKontingen?->nama }}</p>
                                    <p class="text-[11px] text-slate-500 font-medium">{{ $p->kontingen?->nama }} ({{ $p->kontingen?->provinsi }})</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-block rounded px-2 py-0.5 text-[10px] font-bold bg-red-100 text-red-800 uppercase">
                                        {{ $p->nomorLomba?->cabangOlahraga?->nama }}
                                    </span>
                                    <p class="font-semibold text-slate-700 text-xs mt-0.5">{{ $p->nomorLomba?->nama }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $badgeClass = match($p->status) {
                                            'disetujui' => 'bg-green-100 text-green-800',
                                            'diverifikasi_cabor' => 'bg-blue-100 text-blue-800',
                                            'menunggu' => 'bg-amber-100 text-amber-800',
                                            'revisi' => 'bg-orange-100 text-orange-800',
                                            default => 'bg-red-100 text-red-800',
                                        };
                                    @endphp
                                    <span class="inline-block rounded-full px-2.5 py-0.5 text-[10px] font-bold {{ $badgeClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-500 text-[11px]">
                                    {{ $p->created_at->translatedFormat('d M Y, H:i') }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.verifikasi.show', $p->id) }}" class="rounded-lg bg-red-700 px-3 py-1.5 text-xs font-bold text-white hover:bg-red-800 transition">
                                        Periksa Berkas &rarr;
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100">
                {{ $pendaftaran->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
