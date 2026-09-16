@extends('layouts.kontingen')

@section('title', 'Daftar Atlet')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Manajemen Atlet</h1>
            <p class="text-xs text-slate-500 mt-1">Daftar atlet resmi dari {{ $kontingen->nama }}.</p>
        </div>
        <a href="{{ route('kontingen.atlet.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-red-700 px-4 py-2.5 text-xs font-bold text-white shadow hover:bg-red-800 transition">
            <span>+</span> Tambah Atlet
        </a>
    </div>

    @if($atlet->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center text-slate-400">
            <p class="text-base font-semibold text-slate-600">Belum ada data atlet.</p>
            <p class="text-xs text-slate-400 mt-1">Silakan daftarkan atlet kontingen Anda melalui tombol di atas.</p>
        </div>
    @else
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-4 py-3">Atlet</th>
                            <th class="px-4 py-3">Gender / Usia</th>
                            <th class="px-4 py-3">Asal Kota</th>
                            <th class="px-4 py-3">Nomor Lomba Terdaftar</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($atlet as $a)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @if($a->foto_path)
                                            <img src="{{ Storage::url($a->foto_path) }}" alt="{{ $a->nama }}" class="h-9 w-9 rounded-full object-cover border border-slate-200">
                                        @else
                                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-200 font-bold text-slate-600 text-xs">
                                                {{ strtoupper(substr($a->nama, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-bold text-slate-800 text-sm">{{ $a->nama }}</p>
                                            <p class="text-[11px] text-slate-400 font-mono">NIK: {{ substr($a->nik, 0, 6) }}******</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-700">
                                    <span class="inline-block rounded px-1.5 py-0.5 text-[10px] font-bold {{ $a->gender === 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                        {{ $a->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </span>
                                    <p class="text-[11px] text-slate-400 mt-0.5">{{ $a->hitungUmurPada() }} tahun</p>
                                </td>
                                <td class="px-4 py-3 text-slate-700 font-medium">
                                    {{ $a->asal_kota }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($a->pendaftaran->isEmpty())
                                        <span class="text-slate-400 italic">Belum terdaftar lomba</span>
                                    @else
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($a->pendaftaran as $reg)
                                                <span class="rounded bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-700">
                                                    {{ $reg->nomorLomba?->nama }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-block rounded-full px-2.5 py-0.5 text-[10px] font-bold {{ $a->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($a->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('kontingen.atlet.edit', $a->id) }}" class="rounded-lg border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition">
                                            Edit / Berkas
                                        </a>
                                        <form method="POST" action="{{ route('kontingen.atlet.destroy', $a->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data atlet ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg border border-red-200 px-2.5 py-1 text-xs font-semibold text-red-600 hover:bg-red-50 transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100">
                {{ $atlet->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
