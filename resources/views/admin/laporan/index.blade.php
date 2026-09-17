@extends('layouts.admin')

@section('title', 'Laporan & Rekapitulasi Event')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-800 font-medium">Laporan</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900">Laporan & Rekapitulasi Event</h1>
            <p class="text-sm text-neutral-500 mt-1">Laporan komprehensif penyelenggaraan pekan olahraga, partisipasi kontingen, dan perolehan medali.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.laporan.export', ['event_id' => $selectedEventId]) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak / Ekspor Laporan
            </a>
        </div>
    </div>

    <!-- Filter Event -->
    <div class="p-4 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
        <form method="GET" action="{{ route('admin.laporan.index') }}" class="flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="w-full sm:w-80">
                <label class="block text-xs font-semibold text-neutral-700 uppercase mb-1">Pilih Event Laporan</label>
                <select name="event_id" onchange="this.form.submit()"
                        class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                    @foreach($events as $ev)
                        <option value="{{ $ev->id }}" {{ $selectedEventId == $ev->id ? 'selected' : '' }}>
                            {{ $ev->nama }} {{ $ev->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="text-xs text-neutral-500 text-right">
                Status Event: <span class="font-semibold text-neutral-800">{{ $event?->status ?? 'Aktif' }}</span> &bull;
                Rentang: {{ $event?->tanggal_mulai ? $event->tanggal_mulai->format('d/m/Y') : '-' }} s/d {{ $event?->tanggal_selesai ? $event->tanggal_selesai->format('d/m/Y') : '-' }}
            </div>
        </form>
    </div>

    <!-- Overview Stats Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-5 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
            <p class="text-xs font-medium text-neutral-500 uppercase">Kontingen Terdaftar</p>
            <p class="text-2xl font-bold text-neutral-900 mt-1">{{ $stats['total_kontingen'] }}</p>
            <p class="text-[11px] text-neutral-400 mt-1">Daerah / Kota Peserta</p>
        </div>

        <div class="p-5 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
            <p class="text-xs font-medium text-neutral-500 uppercase">Total Atlet</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['total_atlet'] }}</p>
            <p class="text-[11px] text-emerald-600 mt-1 font-medium">{{ $stats['total_atlet_terverifikasi'] }} Atlet Sah (Terverifikasi)</p>
        </div>

        <div class="p-5 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
            <p class="text-xs font-medium text-neutral-500 uppercase">Pertandingan</p>
            <p class="text-2xl font-bold text-neutral-900 mt-1">{{ $stats['pertandingan_selesai'] }} / {{ $stats['total_pertandingan'] }}</p>
            <p class="text-[11px] text-neutral-400 mt-1">Laga Telah Selesai</p>
        </div>

        <div class="p-5 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
            <p class="text-xs font-medium text-neutral-500 uppercase">Medali Terdistribusi</p>
            <div class="flex items-center gap-2 mt-1">
                <span class="text-sm font-bold text-amber-500">🥇 {{ $stats['total_emas'] }}</span>
                <span class="text-sm font-bold text-slate-400">🥈 {{ $stats['total_perak'] }}</span>
                <span class="text-sm font-bold text-amber-700">🥉 {{ $stats['total_perunggu'] }}</span>
            </div>
            <p class="text-[11px] text-neutral-400 mt-1">Total Medali Dikunci</p>
        </div>
    </div>

    <!-- Ringkasan Klasemen Medali Teratas -->
    <div class="bg-white border border-neutral-200/80 rounded-xl shadow-xs overflow-hidden">
        <div class="p-5 border-b border-neutral-200/80 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-neutral-900">Rekapitulasi Klasemen Medali Kontingen</h2>
                <p class="text-xs text-neutral-500">Peringkat perolehan medali berdasarkan jumlah emas, perak, dan perunggu.</p>
            </div>
            <a href="{{ route('admin.klasemen.index') }}" class="text-xs font-medium text-blue-600 hover:underline">
                Buka Papan Klasemen Penuh &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-neutral-600">
                <thead class="bg-neutral-50 text-xs font-semibold text-neutral-500 uppercase">
                    <tr>
                        <th class="px-6 py-3.5 w-16 text-center">Rank</th>
                        <th class="px-6 py-3.5">Kontingen Daerah</th>
                        <th class="px-6 py-3.5 text-center text-amber-600">🥇 Emas</th>
                        <th class="px-6 py-3.5 text-center text-slate-500">🥈 Perak</th>
                        <th class="px-6 py-3.5 text-center text-amber-800">🥉 Perunggu</th>
                        <th class="px-6 py-3.5 text-center font-bold text-neutral-900">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200/80">
                    @forelse($klasemen->take(10) as $row)
                        <tr class="hover:bg-neutral-50/50">
                            <td class="px-6 py-4 text-center font-bold text-neutral-800">
                                @if($loop->iteration == 1)
                                    <span class="w-6 h-6 rounded-full bg-amber-100 text-amber-700 inline-flex items-center justify-center text-xs">1</span>
                                @elseif($loop->iteration == 2)
                                    <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 inline-flex items-center justify-center text-xs">2</span>
                                @elseif($loop->iteration == 3)
                                    <span class="w-6 h-6 rounded-full bg-amber-50 text-amber-800 inline-flex items-center justify-center text-xs">3</span>
                                @else
                                    {{ $loop->iteration }}
                                @endif
                            </td>
                            <td class="px-6 py-4 font-semibold text-neutral-900">
                                {{ $row['kontingen']->nama ?? $row['nama_kontingen'] ?? 'Kontingen' }}
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-amber-600">{{ $row['emas'] }}</td>
                            <td class="px-6 py-4 text-center font-bold text-slate-500">{{ $row['perak'] }}</td>
                            <td class="px-6 py-4 text-center font-bold text-amber-800">{{ $row['perunggu'] }}</td>
                            <td class="px-6 py-4 text-center font-bold text-neutral-900">{{ $row['total'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-xs text-neutral-400">
                                Belum ada perolehan medali yang dicatat pada event ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
