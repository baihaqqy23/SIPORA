@extends('layouts.admin')

@section('title', 'Nomor Lomba - ' . $cabor->nama)

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <a href="{{ route('admin.cabor.index') }}" class="hover:text-neutral-800">Cabor</a>
    <span class="mx-1 text-neutral-300">/</span>
    <a href="{{ route('admin.cabor.show', $cabor) }}" class="hover:text-neutral-800">{{ $cabor->singkatan }}</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-400">Nomor Lomba</span>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Nomor Lomba: {{ $cabor->nama }}</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola nomor pertandingan, kategori umur, kuota, dan format pertandingan.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.cabor.show', $cabor) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 transition-colors">
                &larr; Detail Cabor
            </a>
            <a href="{{ route('admin.cabor.nomor-lomba.create', $cabor) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 dark:bg-primary-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-slate-800 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Nomor Lomba
            </a>
        </div>
    </div>

    @if($nomorLomba->isEmpty())
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-12 text-center shadow-sm">
            <p class="text-base font-semibold text-slate-800 dark:text-slate-200">Belum ada nomor lomba</p>
            <p class="text-xs text-slate-400 mt-1">Tambahkan nomor lomba untuk cabang olahraga ini.</p>
            <a href="{{ route('admin.cabor.nomor-lomba.create', $cabor) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 dark:bg-primary-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-slate-800 transition-colors mt-4">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Nomor Lomba
            </a>
        </div>
    @else
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-semibold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-4 py-3">Nama Nomor Lomba</th>
                            <th class="px-4 py-3">Gender</th>
                            <th class="px-4 py-3">Jenis</th>
                            <th class="px-4 py-3">Format</th>
                            <th class="px-4 py-3 text-center">Kuota</th>
                            <th class="px-4 py-3">Umur</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($nomorLomba as $nl)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                                <td class="px-4 py-3 font-bold text-slate-900 dark:text-white text-sm">{{ $nl->nama }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-block rounded px-2 py-0.5 text-[10px] font-bold uppercase
                                        @if($nl->gender === 'putra') bg-blue-100 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300
                                        @elseif($nl->gender === 'putri') bg-pink-100 dark:bg-pink-950/50 text-pink-700 dark:text-pink-300
                                        @else bg-purple-100 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300
                                        @endif">
                                        {{ $nl->gender }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300 capitalize">
                                    {{ $nl->jenis }}
                                </td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                    {{ str_replace('_', ' ', ucfirst($nl->format_pertandingan)) }}
                                </td>
                                <td class="px-4 py-3 text-center font-semibold text-slate-700 dark:text-slate-300">{{ $nl->kuota_per_kontingen ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                    @if($nl->umur_min || $nl->umur_maks)
                                        {{ $nl->umur_min ?? '?' }}-{{ $nl->umur_maks ?? '?' }} th
                                    @else
                                        <span class="text-slate-400">Umum</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-block rounded px-2 py-0.5 text-[10px] font-bold capitalize
                                        @if($nl->status_bracket === 'terkunci') bg-emerald-100 text-emerald-800
                                        @elseif($nl->status_bracket === 'tergenerate') bg-amber-100 text-amber-800
                                        @else bg-slate-100 text-slate-600
                                        @endif">
                                        {{ $nl->status_bracket }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.nomor-lomba.edit', $nl) }}" class="rounded-lg p-1.5 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 transition-colors" title="Edit">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.nomor-lomba.destroy', $nl) }}" onsubmit="return confirm('Hapus nomor lomba {{ $nl->nama }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg p-1.5 text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-colors" title="Hapus">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($nomorLomba->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                    {{ $nomorLomba->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
