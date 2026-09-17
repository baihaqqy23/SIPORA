@extends('layouts.admin')

@section('title', 'Papan Jadwal & Manajemen Alokasi Lapangan')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-800 font-medium">Papan Jadwal</span>
@endsection

@section('content')
<div class="space-y-6" x-data="{
    showAlokasiModal: false,
    selectedMatch: null,
    selectedLapanganId: '',
    selectedWaktu: '08:00',
    selectedTanggal: '{{ $tanggal }}',
    openModal(match) {
        this.selectedMatch = match;
        this.selectedLapanganId = match.lapangan_id || '';
        this.selectedWaktu = match.waktu_mulai ? match.waktu_mulai.substring(0, 5) : '08:00';
        this.selectedTanggal = match.tanggal || '{{ $tanggal }}';
        this.showAlokasiModal = true;
    }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900">Papan Jadwal Pertandingan</h1>
            <p class="text-sm text-neutral-500 mt-1">Kelola matriks alokasi lapangan, deteksi bentrok jadwal, dan publikasi waktu pertandingan.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.papan-jadwal.export-pdf', ['tanggal' => $tanggal]) }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors">
                <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Ekspor PDF Jadwal
            </a>
        </div>
    </div>

    <!-- Peringatan Konflik Jika Ada -->
    @if($konflik['ada_konflik_keras'])
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl space-y-2">
            <div class="flex items-center gap-2 text-red-800 font-semibold text-sm">
                <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Terdeteksi {{ count($konflik['konflik_keras']) }} Bentrok Jadwal Keras (Overlap)!</span>
            </div>
            <ul class="list-disc list-inside text-xs text-red-700 space-y-1 pl-1">
                @foreach($konflik['konflik_keras'] as $k)
                    <li>{{ $k['pesan'] }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($konflik['ada_konflik_lunak'])
        <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl space-y-2">
            <div class="flex items-center gap-2 text-amber-800 font-semibold text-sm">
                <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Peringatan Jeda / Beban Tugas ({{ count($konflik['konflik_lunak']) }} Catatan)</span>
            </div>
            <ul class="list-disc list-inside text-xs text-amber-700 space-y-1 pl-1">
                @foreach($konflik['konflik_lunak'] as $k)
                    <li>{{ $k['pesan'] }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Filter Tanggal Bar -->
    <div class="p-4 bg-white border border-neutral-200/80 rounded-xl shadow-xs">
        <form method="GET" action="{{ route('admin.papan-jadwal.index') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <label for="tanggal" class="text-xs font-semibold text-neutral-700 uppercase">Pilih Tanggal Pertandingan:</label>
                <input type="date" id="tanggal" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()"
                       class="text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="text-xs text-neutral-500">
                Menampilkan jadwal: <span class="font-bold text-neutral-900">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</span>
            </div>
        </form>
    </div>

    <!-- Layout: Matriks Jadwal + Pertandingan Belum Terjadwal -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Main: Timeline Matrix -->
        <div class="lg:col-span-3 space-y-4">
            <div class="bg-white border border-neutral-200/80 rounded-xl shadow-xs overflow-hidden">
                <div class="p-4 border-b border-neutral-200/80 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-neutral-900">Matriks Lapangan & Jam Pertandingan</h2>
                        <p class="text-xs text-neutral-500">Klik slot jadwal atau tombol pindah untuk mengubah alokasi lapangan.</p>
                    </div>
                    <span class="text-xs font-medium text-neutral-500">{{ $lapangan->count() }} Lapangan Terdaftar</span>
                </div>

                <div class="overflow-x-auto max-h-[650px] overflow-y-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-neutral-50 sticky top-0 z-10 text-neutral-500 uppercase tracking-wider font-semibold border-b border-neutral-200">
                            <tr>
                                <th class="p-3 w-20 text-center border-r border-neutral-200 bg-neutral-100">Jam</th>
                                @foreach($lapangan as $lap)
                                    <th class="p-3 min-w-[200px] border-r border-neutral-200 last:border-r-0">
                                        <div class="font-bold text-neutral-800">{{ $lap->nama }}</div>
                                        <div class="text-[10px] text-neutral-400 font-normal lowercase">{{ $lap->venue?->nama }}</div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200">
                            @foreach($matrix['slots'] as $slot)
                                <tr class="hover:bg-neutral-50/50">
                                    <td class="p-2 text-center font-mono font-semibold text-neutral-500 border-r border-neutral-200 bg-neutral-50/50">
                                        {{ $slot }}
                                    </td>
                                    @foreach($lapangan as $lap)
                                        @php
                                            $match = $matrix['matrix'][$lap->id][$slot] ?? null;
                                        @endphp
                                        <td class="p-1.5 border-r border-neutral-200 last:border-r-0 align-top h-16">
                                            @if($match)
                                                <div class="p-2 bg-blue-50/90 border border-blue-200 rounded-lg text-neutral-800 shadow-xs hover:shadow transition-shadow">
                                                    <div class="flex items-center justify-between text-[11px] font-bold text-blue-700">
                                                        <span>{{ $match->nomorLomba?->cabangOlahraga?->nama }}</span>
                                                        <span class="text-[10px] bg-blue-100 px-1 rounded">{{ $match->babak }}</span>
                                                    </div>
                                                    <div class="text-[11px] font-semibold text-neutral-900 mt-0.5 truncate">
                                                        {{ $match->nomorLomba?->nama }}
                                                    </div>
                                                    <div class="text-[11px] text-neutral-600 mt-1 font-medium bg-white/80 px-1.5 py-0.5 rounded border border-blue-100">
                                                        @if($match->pesertaPertandingan->count() >= 2)
                                                            {{ $match->pesertaPertandingan[0]->peserta?->nama ?? 'TBD' }}
                                                            <span class="text-neutral-400">vs</span>
                                                            {{ $match->pesertaPertandingan[1]->peserta?->nama ?? 'TBD' }}
                                                        @else
                                                            <span class="italic text-neutral-400">Peserta TBD</span>
                                                        @endif
                                                    </div>
                                                    <div class="mt-2 flex items-center justify-between pt-1 border-t border-blue-100/60">
                                                        <span class="text-[10px] text-neutral-500">{{ $match->durasi_menit ?? 60 }} mnt</span>
                                                        <div class="flex items-center gap-1">
                                                            <button type="button" @click="openModal({{ json_encode($match) }})"
                                                                    title="Pindah Slot / Lapangan"
                                                                    class="text-[10px] text-blue-600 hover:text-blue-800 font-semibold px-1 py-0.5 rounded hover:bg-blue-100">
                                                                Edit
                                                            </button>
                                                            @if($match->status === 'draft')
                                                                <form action="{{ route('admin.papan-jadwal.publikasi', $match) }}" method="POST">
                                                                    @csrf
                                                                    <button type="submit" title="Publikasikan Jadwal"
                                                                            class="text-[10px] text-emerald-600 hover:text-emerald-800 font-semibold px-1 py-0.5 rounded hover:bg-emerald-100">
                                                                        Publikasi
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar: Laga Belum Dialokasikan -->
        <div class="space-y-4">
            <div class="bg-white border border-neutral-200/80 rounded-xl shadow-xs p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-bold text-neutral-900">Laga Belum Terjadwal</h2>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-semibold">
                        {{ $matrix['unallocated']->count() }} Laga
                    </span>
                </div>
                <p class="text-xs text-neutral-500">Daftar pertandingan babak yang belum dialokasikan lapangan atau jam mulainya.</p>

                <div class="space-y-2 max-h-[500px] overflow-y-auto pr-1">
                    @forelse($matrix['unallocated'] as $unMatch)
                        <div class="p-3 bg-neutral-50 border border-neutral-200 rounded-lg text-xs space-y-1 hover:border-blue-300 transition-colors">
                            <div class="flex items-center justify-between font-bold text-neutral-800">
                                <span>{{ $unMatch->nomorLomba?->cabangOlahraga?->nama }}</span>
                                <span class="text-[10px] bg-neutral-200 text-neutral-700 px-1 rounded">{{ $unMatch->babak }}</span>
                            </div>
                            <div class="text-neutral-600 font-medium truncate">{{ $unMatch->nomorLomba?->nama }}</div>
                            <div class="text-neutral-500 text-[11px] pt-1 border-t border-neutral-200">
                                @if($unMatch->pesertaPertandingan->count() >= 2)
                                    {{ $unMatch->pesertaPertandingan[0]->peserta?->nama ?? 'TBD' }} vs {{ $unMatch->pesertaPertandingan[1]->peserta?->nama ?? 'TBD' }}
                                @else
                                    <span class="italic text-neutral-400">Peserta Bracket</span>
                                @endif
                            </div>
                            <button type="button" @click="openModal({{ json_encode($unMatch) }})"
                                    class="w-full mt-2 py-1 text-center font-semibold text-blue-600 bg-white border border-blue-200 rounded hover:bg-blue-50 transition-colors">
                                + Jadwalkan Laga Ini
                            </button>
                        </div>
                    @empty
                        <div class="text-center py-6 text-neutral-400 text-xs">
                            Semua pertandingan telah dijadwalkan.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Alokasi / Pindah Slot -->
    <div x-show="showAlokasiModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-neutral-900/50 flex items-center justify-center p-4"
         style="display: none;">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 space-y-4" @click.outside="showAlokasiModal = false">
            <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                <h3 class="text-base font-bold text-neutral-900">Atur Jadwal Pertandingan</h3>
                <button type="button" @click="showAlokasiModal = false" class="text-neutral-400 hover:text-neutral-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.papan-jadwal.alokasi') }}" method="POST" class="space-y-4"
                  @submit.prevent="
                    fetch('{{ route('admin.papan-jadwal.alokasi') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            pertandingan_id: selectedMatch.id,
                            lapangan_id: selectedLapanganId,
                            tanggal: selectedTanggal,
                            waktu_mulai: selectedWaktu
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.error) {
                            alert('Gagal: ' + data.error);
                        } else {
                            window.location.reload();
                        }
                    })
                    .catch(err => alert('Terjadi kesalahan jaringan'))
                  ">
                <div>
                    <label class="block text-xs font-semibold text-neutral-700 uppercase mb-1">Pertandingan</label>
                    <div class="p-2.5 bg-neutral-50 rounded-lg text-xs font-medium text-neutral-800"
                         x-text="selectedMatch ? (selectedMatch.nomor_lomba ? selectedMatch.nomor_lomba.nama : 'Pertandingan #' + selectedMatch.id) + ' (' + selectedMatch.babak + ')' : ''"></div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-neutral-700 uppercase mb-1">Tanggal</label>
                    <input type="date" x-model="selectedTanggal" required class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-neutral-700 uppercase mb-1">Pilih Lapangan / Venue</label>
                    <select x-model="selectedLapanganId" required class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih Lapangan...</option>
                        @foreach($lapangan as $lap)
                            <option value="{{ $lap->id }}">{{ $lap->nama }} - {{ $lap->venue?->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-neutral-700 uppercase mb-1">Jam Mulai</label>
                    <select x-model="selectedWaktu" required class="w-full text-sm border-neutral-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                        @foreach($matrix['slots'] as $slot)
                            <option value="{{ $slot }}">{{ $slot }} WIB</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-neutral-100">
                    <button type="button" @click="showAlokasiModal = false" class="px-4 py-2 text-xs font-semibold text-neutral-600 hover:bg-neutral-100 rounded-lg">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg">
                        Simpan Alokasi Slot
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
