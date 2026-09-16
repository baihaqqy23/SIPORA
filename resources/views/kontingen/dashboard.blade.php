@extends('layouts.kontingen')

@section('title', 'Dashboard Kontingen')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-lg font-semibold text-slate-800">{{ $kontingen->nama }}</h1>
            <p class="text-sm text-slate-500">{{ $kontingen->kota }}, {{ $kontingen->provinsi }}</p>
        </div>
        <x-status-badge :status="$kontingen->status" />
    </div>

    {{-- Berkas Warning --}}
    @if($berkasKurang > 0)
    <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 flex items-start gap-3">
        <svg class="h-5 w-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.962-.833-2.732 0L3.07 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
        <div>
            <p class="text-sm font-semibold text-amber-700">{{ $berkasKurang }} Atlet Belum Upload Berkas</p>
            <p class="text-xs text-amber-600 mt-0.5">Berkas wajib dipenuhi sebelum pendaftaran dapat diverifikasi.</p>
            <a href="{{ route('kontingen.atlet.index') }}" class="text-xs font-medium text-amber-700 underline mt-1 inline-block">Kelola Atlet →</a>
        </div>
    </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="rounded-xl bg-white border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Jumlah Atlet</p>
            <p class="text-2xl font-bold text-slate-800 tabular-nums">{{ $stats['jumlah_atlet'] }}</p>
            <a href="{{ route('kontingen.atlet.index') }}" class="text-xs text-brand hover:underline">Kelola →</a>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Pendaftaran Disetujui</p>
            <p class="text-2xl font-bold text-emerald-600 tabular-nums">{{ $stats['pendaftaran_disetujui'] }}</p>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Menunggu Verifikasi</p>
            <p class="text-2xl font-bold {{ $stats['pendaftaran_menunggu'] > 0 ? 'text-amber-600' : 'text-slate-800' }} tabular-nums">{{ $stats['pendaftaran_menunggu'] }}</p>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Ditolak/Revisi</p>
            <p class="text-2xl font-bold {{ $stats['pendaftaran_ditolak'] > 0 ? 'text-red-600' : 'text-slate-800' }} tabular-nums">{{ $stats['pendaftaran_ditolak'] }}</p>
        </div>
    </div>

    {{-- Medali & Pendaftaran Terbaru --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Medali --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <h2 class="text-sm font-semibold text-slate-700 mb-4">Perolehan Medali</h2>
            <div class="flex items-center gap-6 justify-center py-4">
                <div class="text-center">
                    <div class="text-3xl">🥇</div>
                    <div class="text-2xl font-bold text-amber-600 tabular-nums mt-1">{{ $medaliKontingen['emas'] }}</div>
                    <div class="text-xs text-slate-400">Emas</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl">🥈</div>
                    <div class="text-2xl font-bold text-slate-500 tabular-nums mt-1">{{ $medaliKontingen['perak'] }}</div>
                    <div class="text-xs text-slate-400">Perak</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl">🥉</div>
                    <div class="text-2xl font-bold text-amber-800 tabular-nums mt-1">{{ $medaliKontingen['perunggu'] }}</div>
                    <div class="text-xs text-slate-400">Perunggu</div>
                </div>
            </div>
        </div>

        {{-- Pendaftaran Terbaru --}}
        <div class="md:col-span-2 rounded-xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-700">Pendaftaran Terbaru</h2>
                <a href="{{ route('kontingen.pendaftaran.index') }}" class="text-xs text-brand hover:underline">Lihat semua</a>
            </div>
            @if($pendaftaranTerbaru->count())
            <div class="divide-y divide-slate-50">
                @foreach($pendaftaranTerbaru as $p)
                <div class="flex items-center gap-3 px-5 py-3">
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-medium text-slate-800 truncate">
                            {{ $p->atlet?->nama_lengkap ?? 'Tim' }}
                        </div>
                        <div class="text-[11px] text-slate-400 truncate">
                            {{ $p->nomorLomba?->cabangOlahraga?->nama }} • {{ $p->nomorLomba?->nama }}
                        </div>
                    </div>
                    <x-status-badge :status="$p->status" />
                </div>
                @endforeach
            </div>
            @else
            <x-empty-state
                title="Belum ada pendaftaran"
                description="Tambahkan atlet terlebih dahulu, lalu daftarkan ke nomor lomba."
                action-href="{{ route('kontingen.pendaftaran.create') }}"
                action-text="+ Daftar Sekarang"
            />
            @endif
        </div>
    </div>

    {{-- Aksi Cepat --}}
    <div class="rounded-xl bg-white border border-slate-200 p-5">
        <h2 class="text-sm font-semibold text-slate-700 mb-3">Aksi Cepat</h2>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('kontingen.atlet.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-brand px-4 py-2 text-sm font-medium text-white hover:bg-brand/90">
                + Tambah Atlet
            </a>
            <a href="{{ route('kontingen.pendaftaran.create') }}" class="inline-flex items-center gap-2 rounded-lg border border-brand text-brand px-4 py-2 text-sm font-medium hover:bg-brand/5">
                + Daftar Nomor Lomba
            </a>
            <a href="{{ route('kontingen.jadwal.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 text-slate-600 px-4 py-2 text-sm font-medium hover:bg-slate-50">
                Lihat Jadwal
            </a>
        </div>
    </div>
</div>
@endsection
