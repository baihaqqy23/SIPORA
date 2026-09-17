@extends('layouts.admin')

@section('title', 'Papan Klasemen Perolehan Medali')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-800 font-medium">Klasemen Medali</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900">Klasemen Perolehan Medali</h1>
            <p class="text-sm text-neutral-500 mt-1">Peringkat kontingen resmi berdasarkan akumulasi medali Emas, Perak, dan Perunggu.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.klasemen.export-pdf', ['event_id' => $event?->id, 'cabor_id' => $caborId]) }}" target="_blank"
               class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors">
                <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Ekspor PDF Klasemen
            </a>
        </div>
    </div>

    <!-- Stats Medali -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-5 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider">🥇 Medali Emas</p>
                    <p class="text-2xl font-bold text-neutral-900 mt-1">{{ $totalEmas }}</p>
                </div>
                <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center font-bold">1</div>
            </div>
        </div>

        <div class="p-5 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">🥈 Medali Perak</p>
                    <p class="text-2xl font-bold text-neutral-900 mt-1">{{ $totalPerak }}</p>
                </div>
                <div class="w-10 h-10 bg-slate-100 text-slate-600 rounded-lg flex items-center justify-center font-bold">2</div>
            </div>
        </div>

        <div class="p-5 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-amber-800 uppercase tracking-wider">🥉 Medali Perunggu</p>
                    <p class="text-2xl font-bold text-neutral-900 mt-1">{{ $totalPerunggu }}</p>
                </div>
                <div class="w-10 h-10 bg-amber-50/60 text-amber-800 rounded-lg flex items-center justify-center font-bold">3</div>
            </div>
        </div>

        <div class="p-5 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Total Medali</p>
                    <p class="text-2xl font-bold text-neutral-900 mt-1">{{ $totalMedali }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center font-bold">∑</div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="p-4 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
        <form method="GET" action="{{ route('admin.klasemen.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-xs font-medium text-neutral-700 mb-1.5">Pilih Event</label>
                <select name="event_id" onchange="this.form.submit()"
                        class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                    @foreach($events as $ev)
                        <option value="{{ $ev->id }}" {{ ($event?->id == $ev->id) ? 'selected' : '' }}>
                            {{ $ev->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-neutral-700 mb-1.5">Filter Cabang Olahraga</label>
                <select name="cabor_id" onchange="this.form.submit()"
                        class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Cabang Olahraga (Klasemen Umum)</option>
                    @foreach($caborList as $c)
                        <option value="{{ $c->id }}" {{ $caborId == $c->id ? 'selected' : '' }}>{{ $c->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                @if($caborId)
                    <a href="{{ route('admin.klasemen.index', ['event_id' => $event?->id]) }}"
                       class="inline-block px-3 py-2 text-xs font-medium text-neutral-600 bg-neutral-100 hover:bg-neutral-200 rounded-lg transition-colors">
                        Reset Filter Cabor
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Klasemen -->
    <div class="bg-white border border-neutral-200/80 rounded-xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-neutral-600">
                <thead class="bg-neutral-50/80 text-xs font-semibold text-neutral-500 uppercase tracking-wider border-b border-neutral-200/80">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 w-16 text-center">Rank</th>
                        <th scope="col" class="px-6 py-3.5">Kontingen Daerah</th>
                        <th scope="col" class="px-6 py-3.5 text-center text-amber-600 font-bold">🥇 Emas</th>
                        <th scope="col" class="px-6 py-3.5 text-center text-slate-500 font-bold">🥈 Perak</th>
                        <th scope="col" class="px-6 py-3.5 text-center text-amber-800 font-bold">🥉 Perunggu</th>
                        <th scope="col" class="px-6 py-3.5 text-center font-bold text-neutral-900">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200/80">
                    @forelse($klasemen as $row)
                        <tr class="hover:bg-neutral-50/50 transition-colors {{ $row['peringkat'] <= 3 ? 'bg-amber-50/10' : '' }}">
                            <td class="px-6 py-4 text-center font-bold text-neutral-800">
                                @if($row['peringkat'] == 1)
                                    <span class="w-7 h-7 rounded-full bg-amber-400 text-amber-950 inline-flex items-center justify-center font-bold text-xs shadow-xs">1</span>
                                @elseif($row['peringkat'] == 2)
                                    <span class="w-7 h-7 rounded-full bg-slate-200 text-slate-900 inline-flex items-center justify-center font-bold text-xs shadow-xs">2</span>
                                @elseif($row['peringkat'] == 3)
                                    <span class="w-7 h-7 rounded-full bg-amber-700 text-white inline-flex items-center justify-center font-bold text-xs shadow-xs">3</span>
                                @else
                                    <span class="text-neutral-500">{{ $row['peringkat'] }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-semibold text-neutral-900">
                                <div class="flex items-center gap-3">
                                    @if(!empty($row['logo_path']))
                                        <img src="{{ Storage::url($row['logo_path']) }}" class="w-7 h-7 rounded-full object-cover border">
                                    @else
                                        <div class="w-7 h-7 rounded-full bg-neutral-100 text-neutral-600 font-bold flex items-center justify-center text-[10px]">
                                            {{ strtoupper(substr($row['kontingen_nama'], 0, 2)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <span>{{ $row['kontingen_nama'] }}</span>
                                        <div class="text-[11px] text-neutral-400 font-normal">{{ $row['provinsi'] ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-amber-600 text-base font-mono">{{ $row['emas'] }}</td>
                            <td class="px-6 py-4 text-center font-bold text-slate-500 text-base font-mono">{{ $row['perak'] }}</td>
                            <td class="px-6 py-4 text-center font-bold text-amber-800 text-base font-mono">{{ $row['perunggu'] }}</td>
                            <td class="px-6 py-4 text-center font-extrabold text-neutral-900 text-base font-mono">{{ $row['total'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-neutral-400">
                                <p class="text-base font-medium text-neutral-700">Belum ada data perolehan medali</p>
                                <p class="text-xs text-neutral-400 mt-1">Perolehan medali akan otomatis terupdate saat hasil pertandingan selesai diinput.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
