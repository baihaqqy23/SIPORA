@extends('layouts.admin')

@section('title', 'Data Panitia Pelaksana')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-800 font-medium">Panitia</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-white">Panitia Pelaksana & Akun PJ Cabor</h1>
            <p class="text-sm text-neutral-500 mt-1">Kelola data panitia, penugasan teknis cabor, serta pembuatan akun login PJ Cabor & Admin.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.panitia.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-slate-900 dark:bg-primary-600 rounded-lg hover:bg-slate-800 shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                + Tambah Panitia / Buat Akun PJ Cabor
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wider">Total Panitia</p>
                    <p class="text-2xl font-bold text-neutral-900 mt-1">{{ $totalPanitia }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="p-5 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wider">PJ Cabor</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $totalPjCabor }}</p>
                </div>
                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="p-5 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wider">Wasit & Juri</p>
                    <p class="text-2xl font-bold text-purple-600 mt-1">{{ $totalWasitJuri }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="p-4 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
        <form method="GET" action="{{ route('admin.panitia.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium text-neutral-700 mb-1.5">Cari Panitia</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Nama, jabatan, instansi..."
                           class="w-full text-sm pl-9 border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                    <svg class="w-4 h-4 text-neutral-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-neutral-700 mb-1.5">Event</label>
                <select name="event_id" class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Event</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}" {{ $eventId == $event->id ? 'selected' : '' }}>{{ $event->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-neutral-900 rounded-lg hover:bg-neutral-800 transition-colors">
                    Filter
                </button>
                @if($search || $eventId)
                    <a href="{{ route('admin.panitia.index') }}" class="px-3 py-2 text-sm font-medium text-neutral-600 bg-neutral-100 hover:bg-neutral-200 rounded-lg transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white border border-neutral-200/80 rounded-xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-neutral-600">
                <thead class="bg-neutral-50/80 text-xs font-semibold text-neutral-500 uppercase tracking-wider border-b border-neutral-200/80">
                    <tr>
                        <th scope="col" class="px-6 py-3.5">Nama Panitia</th>
                        <th scope="col" class="px-6 py-3.5">Jabatan & Instansi</th>
                        <th scope="col" class="px-6 py-3.5">Kontak</th>
                        <th scope="col" class="px-6 py-3.5">Akun Login</th>
                        <th scope="col" class="px-6 py-3.5">Penugasan Peran</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200/80">
                    @forelse($panitiaList as $panitia)
                        <tr class="hover:bg-neutral-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($panitia->foto_path)
                                        <img src="{{ Storage::url($panitia->foto_path) }}" alt="{{ $panitia->nama }}" class="w-9 h-9 rounded-full object-cover border border-neutral-200">
                                    @else
                                        <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-xs">
                                            {{ strtoupper(substr($panitia->nama, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('admin.panitia.show', $panitia) }}" class="font-semibold text-neutral-900 hover:text-blue-600">
                                            {{ $panitia->nama }}
                                        </a>
                                        <div class="text-xs text-neutral-400 mt-0.5">{{ $panitia->event?->nama }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                <div class="font-semibold text-neutral-900">{{ $panitia->jabatan }}</div>
                                <div class="text-neutral-500">{{ $panitia->instansi ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                <div class="text-neutral-900">{{ $panitia->no_hp }}</div>
                                <div class="text-neutral-500">{{ $panitia->email ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                @if($panitia->user)
                                    <div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-100 text-blue-800 border border-blue-200">
                                            {{ strtoupper(str_replace('_', ' ', $panitia->user->role)) }}
                                        </span>
                                        <div class="font-mono text-[11px] text-slate-700 dark:text-slate-300 font-medium mt-1 truncate max-w-[180px]">
                                            {{ $panitia->user->email }}
                                        </div>
                                    </div>
                                @else
                                    <a href="{{ route('admin.panitia.show', $panitia) }}" class="inline-flex items-center gap-1 text-[11px] font-semibold text-blue-600 hover:underline">
                                        + Buat Akun
                                    </a>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1 max-w-xs">
                                    @forelse($panitia->penugasan as $pen)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                            {{ strtoupper(str_replace('_', ' ', $pen->peran)) }}
                                            @if($pen->cabangOlahraga) ({{ $pen->cabangOlahraga->singkatan ?: $pen->cabangOlahraga->nama }}) @endif
                                        </span>
                                    @empty
                                        <span class="text-neutral-400 text-xs">-</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.panitia.show', $panitia) }}"
                                       class="p-1.5 text-neutral-500 hover:text-blue-600 hover:bg-neutral-100 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.panitia.edit', $panitia) }}"
                                       class="p-1.5 text-neutral-500 hover:text-amber-600 hover:bg-neutral-100 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.panitia.destroy', $panitia) }}" method="POST"
                                          onsubmit="return confirm('Hapus data panitia {{ $panitia->nama }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 text-neutral-500 hover:text-red-600 hover:bg-neutral-100 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-neutral-400">
                                <p class="text-base font-medium text-neutral-700">Belum ada data panitia</p>
                                <a href="{{ route('admin.panitia.create') }}" class="mt-3 inline-block px-4 py-2 text-xs font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                    + Tambah Panitia Baru
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($panitiaList->hasPages())
            <div class="px-6 py-4 border-t border-neutral-200/80">
                {{ $panitiaList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
