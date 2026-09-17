@extends('layouts.admin')

@section('title', 'Log Aktivitas Sistem (Audit Trail)')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-800 font-medium">Audit Log</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900">Audit Log & Aktivitas Sistem</h1>
            <p class="text-sm text-neutral-500 mt-1">Rekam jejak aktivitas penting, perubahan data, manipulasi jadwal, dan otentikasi pengguna.</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="p-5 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wider">Total Catatan Log</p>
                    <p class="text-2xl font-bold text-neutral-900 mt-1">{{ number_format($totalLogs) }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="p-5 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-neutral-500 uppercase tracking-wider">Aktivitas Hari Ini</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($totalAktivitasHariIni) }}</p>
                </div>
                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="p-4 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
        <form method="GET" action="{{ route('admin.audit-log.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-neutral-700 mb-1.5">Cari Aktivitas / IP</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Aksi, modul, IP address..."
                           class="w-full text-sm pl-9 border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                    <svg class="w-4 h-4 text-neutral-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-neutral-700 mb-1.5">Pengguna</label>
                <select name="user_id" class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Pengguna</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ $userId == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->username }})</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-neutral-900 rounded-lg hover:bg-neutral-800 transition-colors">
                    Filter
                </button>
                @if($search || $userId || $aksi)
                    <a href="{{ route('admin.audit-log.index') }}" class="px-3 py-2 text-sm font-medium text-neutral-600 bg-neutral-100 hover:bg-neutral-200 rounded-lg transition-colors">
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
                        <th scope="col" class="px-6 py-3.5">Waktu</th>
                        <th scope="col" class="px-6 py-3.5">Pengguna</th>
                        <th scope="col" class="px-6 py-3.5">Aksi / Operasi</th>
                        <th scope="col" class="px-6 py-3.5">Target Data</th>
                        <th scope="col" class="px-6 py-3.5">IP & Perangkat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200/80">
                    @forelse($logs as $log)
                        <tr class="hover:bg-neutral-50/50 transition-colors text-xs">
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <div class="font-medium text-neutral-900">{{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '-' }}</div>
                                <div class="text-[11px] text-neutral-400">{{ $log->created_at ? $log->created_at->diffForHumans() : '' }}</div>
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                @if($log->user)
                                    <div class="font-semibold text-neutral-800">{{ $log->user->name }}</div>
                                    <div class="text-[11px] text-neutral-400">{{ $log->user->username }} ({{ ucfirst($log->user->role) }})</div>
                                @else
                                    <span class="text-neutral-400 italic">Sistem / Anonim</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-neutral-100 text-neutral-800">
                                    {{ $log->aksi }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5">
                                @if($log->model_type)
                                    <div class="font-mono text-[11px] text-neutral-700">{{ class_basename($log->model_type) }} #{{ $log->model_id }}</div>
                                @else
                                    <span class="text-neutral-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap text-neutral-500">
                                <div>IP: {{ $log->ip ?: '-' }}</div>
                                <div class="text-[10px] text-neutral-400 truncate max-w-xs">{{ $log->user_agent ?: '-' }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-neutral-400">
                                <p class="text-base font-medium text-neutral-700">Belum ada riwayat log aktivitas</p>
                                <p class="text-xs text-neutral-400 mt-1">Seluruh aktivitas perubahan data oleh user akan otomatis tercatat di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-neutral-200/80">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
