@extends('layouts.admin')

@section('title', 'Cabang Olahraga')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-400">Cabor & Nomor Lomba</span>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-lg font-semibold text-neutral-800">Cabang Olahraga</h1>
            <p class="text-xs text-neutral-500 mt-0.5">
                @if($event)
                    Event aktif: {{ $event->nama }}
                @else
                    Belum ada event aktif.
                @endif
            </p>
        </div>
        <a href="{{ route('admin.cabor.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-neutral-900 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-neutral-800 transition-colors">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            Tambah Cabor
        </a>
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('admin.cabor.index') }}" class="flex gap-2">
        <div class="relative flex-1 max-w-sm">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-neutral-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama cabor..." class="w-full rounded-lg border border-neutral-200 bg-white py-2 pl-9 pr-3 text-sm text-neutral-900 placeholder:text-neutral-400 focus:border-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-400">
        </div>
        <button type="submit" class="rounded-lg border border-neutral-200 bg-white px-3.5 py-2 text-xs font-medium text-neutral-700 hover:bg-neutral-50 transition-colors">Cari</button>
        @if(request('q'))
            <a href="{{ route('admin.cabor.index') }}" class="rounded-lg border border-neutral-200 bg-white px-3.5 py-2 text-xs font-medium text-neutral-500 hover:bg-neutral-50 transition-colors">Reset</a>
        @endif
    </form>

    {{-- Table --}}
    @if($cabors->isEmpty())
        <div class="rounded-2xl border border-neutral-200 bg-white p-12 text-center">
            <svg class="mx-auto h-10 w-10 text-neutral-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.45 1-1 1H7v4h10v-4h-2c-.55 0-1-.45-1-1v-2.34"/><path d="M6 4h12a2 2 0 0 1 2 2v3a6 6 0 0 1-6 6h0a6 6 0 0 1-6-6V6a2 2 0 0 1 2-2Z"/></svg>
            <p class="text-base font-semibold text-neutral-600 mt-4">Belum ada cabang olahraga</p>
            <p class="text-xs text-neutral-400 mt-1">Tambahkan cabang olahraga pertama untuk event ini.</p>
        </div>
    @else
        <div class="rounded-2xl border border-neutral-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-neutral-50 border-b border-neutral-200 text-neutral-600 font-semibold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-4 py-3">Cabang Olahraga</th>
                            <th class="px-4 py-3">Singkatan</th>
                            <th class="px-4 py-3 text-center">Nomor Lomba</th>
                            <th class="px-4 py-3">Venue</th>
                            <th class="px-4 py-3">Durasi</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @foreach($cabors as $cabor)
                            <tr class="hover:bg-neutral-50/70 transition">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="h-3 w-3 rounded-full shrink-0" style="background-color: {{ $cabor->warna ?? '#C8102E' }}"></div>
                                        <div>
                                            <a href="{{ route('admin.cabor.show', $cabor) }}" class="font-bold text-neutral-800 text-sm hover:underline">{{ $cabor->nama }}</a>
                                            @if($cabor->deskripsi)
                                                <p class="text-[11px] text-neutral-400 mt-0.5 line-clamp-1">{{ $cabor->deskripsi }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-block rounded px-2 py-0.5 text-[10px] font-bold bg-neutral-100 text-neutral-700 uppercase">{{ $cabor->singkatan }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center h-6 min-w-[1.5rem] rounded-full bg-neutral-900 text-white text-[10px] font-bold px-1.5">{{ $cabor->nomor_lomba_count }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($cabor->venues->isNotEmpty())
                                        <span class="text-neutral-700">{{ $cabor->venues->pluck('nama')->join(', ') }}</span>
                                    @else
                                        <span class="text-neutral-400 italic">Belum ditentukan</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-neutral-600">{{ $cabor->durasi_default_menit }} mnt</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.cabor.show', $cabor) }}" class="rounded-lg p-1.5 text-neutral-500 hover:bg-neutral-100 hover:text-neutral-900 transition-colors" title="Detail">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </a>
                                        <a href="{{ route('admin.cabor.edit', $cabor) }}" class="rounded-lg p-1.5 text-neutral-500 hover:bg-neutral-100 hover:text-neutral-900 transition-colors" title="Edit">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.cabor.destroy', $cabor) }}" onsubmit="return confirm('Yakin ingin menghapus cabor {{ $cabor->nama }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg p-1.5 text-neutral-400 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($cabors->hasPages())
                <div class="border-t border-neutral-200 px-4 py-3 text-xs">
                    {{ $cabors->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
