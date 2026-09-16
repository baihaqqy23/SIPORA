@extends('layouts.publik')
@section('title', 'Klasemen Medali')

@section('content')
<div>
    <h1 class="text-xl font-bold text-slate-800 mb-1">Klasemen Medali</h1>
    <p class="text-sm text-slate-500 mb-6">{{ $event?->nama ?? 'Data klasemen sementara' }}</p>

    @if($klasemen->count())
    <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 w-12">#</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Kontingen</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-amber-600">🥇 Emas</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 hidden sm:table-cell">🥈 Perak</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-amber-800 hidden sm:table-cell">🥉 Perunggu</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 hidden md:table-cell">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($klasemen as $row)
                <tr class="hover:bg-slate-50 transition-colors {{ $row['peringkat'] <= 3 ? 'bg-amber-50/40' : '' }}">
                    <td class="px-4 py-3">
                        @if($row['peringkat'] === 1)
                            <span class="text-base">🥇</span>
                        @elseif($row['peringkat'] === 2)
                            <span class="text-base">🥈</span>
                        @elseif($row['peringkat'] === 3)
                            <span class="text-base">🥉</span>
                        @else
                            <span class="text-xs font-semibold text-slate-400 tabular-nums">{{ $row['peringkat'] }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('publik.kontingen', $row['kontingen_slug'] ?? $row['slug']) }}" class="font-medium text-slate-800 hover:text-red-700">{{ $row['kontingen_nama'] }}</a>
                        <div class="text-[11px] text-slate-400">{{ $row['provinsi'] }}</div>
                    </td>
                    <td class="px-4 py-3 text-center font-bold text-amber-600 tabular-nums">{{ $row['emas'] }}</td>
                    <td class="px-4 py-3 text-center font-semibold text-slate-500 tabular-nums hidden sm:table-cell">{{ $row['perak'] }}</td>
                    <td class="px-4 py-3 text-center font-semibold text-amber-800 tabular-nums hidden sm:table-cell">{{ $row['perunggu'] }}</td>
                    <td class="px-4 py-3 text-center text-xs text-slate-500 tabular-nums hidden md:table-cell">{{ $row['emas'] + $row['perak'] + $row['perunggu'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <p class="text-xs text-slate-400 mt-3 text-center">Urutan berdasarkan: Jumlah Emas → Perak → Perunggu. Data diperbarui secara real-time.</p>
    @else
    <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center text-slate-400">
        <p class="text-base font-semibold text-slate-600">Belum ada data medali</p>
        <p class="text-xs text-slate-400 mt-1">Klasemen akan muncul setelah pertandingan pertama selesai.</p>
    </div>
    @endif
</div>
@endsection
