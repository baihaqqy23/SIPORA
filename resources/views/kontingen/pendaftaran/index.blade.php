@extends('layouts.kontingen')

@section('title', 'Pendaftaran Lomba')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Pendaftaran Nomor Lomba</h1>
            <p class="text-xs text-slate-500 mt-1">Status dan riwayat pendaftaran nomor tanding kontingen {{ $kontingen->nama }}.</p>
        </div>
        <a href="{{ route('kontingen.pendaftaran.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-red-700 px-4 py-2.5 text-xs font-bold text-white shadow hover:bg-red-800 transition">
            <span>+</span> Daftarkan Nomor Lomba
        </a>
    </div>

    @if($pendaftaran->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center text-slate-400">
            <p class="text-base font-semibold text-slate-600">Belum ada pendaftaran lomba.</p>
            <p class="text-xs text-slate-400 mt-1">Daftarkan atlet kontingen Anda ke cabang olahraga & nomor lomba yang tersedia.</p>
        </div>
    @else
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-4 py-3">Nomor Lomba & Cabor</th>
                            <th class="px-4 py-3">Peserta / Atlet</th>
                            <th class="px-4 py-3">Status Verifikasi</th>
                            <th class="px-4 py-3">Catatan Verifikator</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pendaftaran as $p)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-4 py-3">
                                    <span class="inline-block rounded px-2 py-0.5 text-[10px] font-bold bg-red-100 text-red-800 uppercase">
                                        {{ $p->nomorLomba?->cabangOlahraga?->nama }}
                                    </span>
                                    <p class="font-bold text-slate-800 text-sm mt-1">{{ $p->nomorLomba?->nama }}</p>
                                    <p class="text-[11px] text-slate-400 font-medium">Format: {{ str_replace('_', ' ', ucfirst($p->nomorLomba?->format_pertandingan)) }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    @if($p->atlet)
                                        <div class="flex items-center gap-2">
                                            <div class="h-6 w-6 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-600">
                                                {{ strtoupper(substr($p->atlet->nama, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="font-semibold text-slate-800">{{ $p->atlet->nama }}</p>
                                                <p class="text-[10px] text-slate-400">{{ $p->atlet->gender === 'L' ? 'Putra' : 'Putri' }} &bull; {{ $p->atlet->hitungUmurPada() }} thn</p>
                                            </div>
                                        </div>
                                    @elseif($p->timKontingen)
                                        <p class="font-semibold text-slate-800">{{ $p->timKontingen->nama }}</p>
                                        <p class="text-[10px] text-slate-400">Tim Beregu</p>
                                    @else
                                        <span class="text-slate-400 italic">-</span>
                                    @endif
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
                                <td class="px-4 py-3 text-slate-600 text-xs">
                                    {{ $p->catatan_terakhir ?: '-' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if(in_array($p->status, ['menunggu', 'draft', 'revisi']))
                                        <form method="POST" action="{{ route('kontingen.pendaftaran.destroy', $p->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pendaftaran ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg border border-red-200 px-2.5 py-1 text-xs font-semibold text-red-600 hover:bg-red-50 transition">
                                                Batalkan
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic">Terkunci</span>
                                    @endif
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
