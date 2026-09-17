@extends('layouts.admin')

@section('title', $kontingen->nama)

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <a href="{{ route('admin.kontingen.index') }}" class="hover:text-neutral-800">Kontingen</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-800 font-medium">{{ $kontingen->nama }}</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.kontingen.index') }}" class="p-1 text-neutral-400 hover:text-neutral-700 rounded-lg hover:bg-neutral-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            @if($kontingen->logo_path)
                <img src="{{ Storage::url($kontingen->logo_path) }}" alt="{{ $kontingen->nama }}" class="w-14 h-14 rounded-full object-cover border border-neutral-200">
            @else
                <div class="w-14 h-14 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-lg">
                    {{ strtoupper(substr($kontingen->nama, 0, 2)) }}
                </div>
            @endif
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold tracking-tight text-neutral-900">{{ $kontingen->nama }}</h1>
                    @if($kontingen->status === 'disetujui')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Disetujui
                        </span>
                    @elseif($kontingen->status === 'menunggu')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                            Menunggu Verifikasi
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-neutral-100 text-neutral-600">
                            {{ ucfirst($kontingen->status) }}
                        </span>
                    @endif
                </div>
                <p class="text-sm text-neutral-500 mt-0.5">
                    {{ $kontingen->kota }}, {{ $kontingen->provinsi }} &bull; Event: {{ $kontingen->event?->nama }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.kontingen.edit', $kontingen) }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors">
                <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Kontingen
            </a>
            <form action="{{ route('admin.kontingen.destroy', $kontingen) }}" method="POST"
                  onsubmit="return confirm('Hapus kontingen {{ $kontingen->nama }}?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <!-- Grid Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Info Card -->
        <div class="bg-white border border-neutral-200/80 rounded-xl shadow-xs p-5 space-y-4">
            <h2 class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">Informasi Ofisial & Mandat</h2>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-neutral-400 text-xs block">Nama Ofisial / PIC:</span>
                    <span class="text-neutral-800 font-medium">{{ $kontingen->nama_ofisial }}</span>
                </div>
                <div>
                    <span class="text-neutral-400 text-xs block">No. HP / Kontak:</span>
                    <span class="text-neutral-800 font-medium">{{ $kontingen->no_hp_ofisial }}</span>
                </div>
                <div>
                    <span class="text-neutral-400 text-xs block">Email:</span>
                    <span class="text-neutral-800 font-medium">{{ $kontingen->email }}</span>
                </div>
                <div>
                    <span class="text-neutral-400 text-xs block">Akun Login Portal:</span>
                    <span class="text-neutral-800 font-medium">{{ $kontingen->user ? $kontingen->user->username . ' (' . $kontingen->user->email . ')' : 'Belum dibuatkan akun' }}</span>
                </div>
                <div>
                    <span class="text-neutral-400 text-xs block">Surat Mandat:</span>
                    @if($kontingen->surat_mandat_path)
                        <a href="{{ Storage::url($kontingen->surat_mandat_path) }}" target="_blank"
                           class="inline-flex items-center gap-1.5 text-xs text-blue-600 hover:underline mt-1 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Unduh Surat Mandat
                        </a>
                    @else
                        <span class="text-neutral-400 text-xs">Belum diunggah</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Daftar Atlet & Pendaftaran Tabbed/List -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Atlet Table -->
            <div class="bg-white border border-neutral-200/80 rounded-xl shadow-xs overflow-hidden">
                <div class="p-4 border-b border-neutral-200/80 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-neutral-900">Daftar Atlet Kontingen</h2>
                        <p class="text-xs text-neutral-500">Total {{ $kontingen->atlet->count() }} atlet terdaftar.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-neutral-600">
                        <thead class="bg-neutral-50 text-xs font-semibold text-neutral-500 uppercase">
                            <tr>
                                <th class="px-4 py-3">Nama Atlet</th>
                                <th class="px-4 py-3">Gender / Usia</th>
                                <th class="px-4 py-3">Status Berkas</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200/80">
                            @forelse($kontingen->atlet as $atlet)
                                <tr class="hover:bg-neutral-50/50">
                                    <td class="px-4 py-3 font-semibold text-neutral-900">
                                        <a href="{{ route('admin.atlet.show', $atlet) }}" class="hover:text-blue-600">
                                            {{ $atlet->nama }}
                                        </a>
                                        <div class="text-[11px] text-neutral-400 font-normal">Asal: {{ $atlet->asal_kota ?: '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-neutral-600">
                                        {{ $atlet->gender === 'L' ? 'Laki-laki' : 'Perempuan' }} &bull; {{ $atlet->hitungUmurPada() }} thn
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $atlet->status === 'terverifikasi' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                            {{ ucfirst($atlet->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('admin.atlet.show', $atlet) }}" class="text-xs text-blue-600 hover:underline">
                                            Detail &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-xs text-neutral-400">
                                        Belum ada atlet yang didaftarkan oleh kontingen ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pendaftaran Nomor Lomba -->
            <div class="bg-white border border-neutral-200/80 rounded-xl shadow-xs overflow-hidden">
                <div class="p-4 border-b border-neutral-200/80">
                    <h2 class="text-base font-bold text-neutral-900">Pendaftaran Nomor Lomba & Cabor</h2>
                    <p class="text-xs text-neutral-500">Total {{ $kontingen->pendaftaran->count() }} nomor pertandingan yang diikuti.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-neutral-600">
                        <thead class="bg-neutral-50 text-xs font-semibold text-neutral-500 uppercase">
                            <tr>
                                <th class="px-4 py-3">Cabor & Nomor Lomba</th>
                                <th class="px-4 py-3">Status Pendaftaran</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200/80">
                            @forelse($kontingen->pendaftaran as $pend)
                                <tr class="hover:bg-neutral-50/50">
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-neutral-900">{{ $pend->nomorLomba?->cabangOlahraga?->nama }}</div>
                                        <div class="text-xs text-neutral-500">{{ $pend->nomorLomba?->nama }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $pend->status === 'disetujui' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                            {{ ucfirst($pend->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('admin.verifikasi.show', $pend) }}" class="text-xs text-blue-600 hover:underline">
                                            Verifikasi &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-xs text-neutral-400">
                                        Belum ada pendaftaran nomor lomba.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
