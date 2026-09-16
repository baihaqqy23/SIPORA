@extends('layouts.kontingen')

@section('title', 'Daftarkan Atlet ke Nomor Lomba')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Form Pendaftaran Lomba</h1>
            <p class="text-xs text-slate-500 mt-1">Event: {{ $event->nama }}</p>
        </div>
        <a href="{{ route('kontingen.pendaftaran.index') }}" class="text-xs font-semibold text-slate-600 hover:underline">
            &larr; Kembali
        </a>
    </div>

    @if(session('error'))
        <div class="rounded-xl bg-red-50 border border-red-200 p-4 text-xs font-medium text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl bg-red-50 border border-red-200 p-4">
            <ul class="list-disc list-inside text-xs text-red-700 space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm" x-data="pendaftaranForm()">
        <form method="POST" action="{{ route('kontingen.pendaftaran.store') }}" class="space-y-5">
            @csrf

            {{-- Pilih Nomor Lomba --}}
            <div>
                <label for="nomor_lomba_id" class="block text-xs font-semibold text-slate-700 mb-1">
                    Pilih Nomor Lomba <span class="text-red-500">*</span>
                </label>
                <select id="nomor_lomba_id" name="nomor_lomba_id" required x-model="selectedNomorId" @change="onNomorChange()"
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-red-600 focus:outline-none bg-white">
                    <option value="">-- Pilih Cabang & Nomor Lomba --</option>
                    @foreach($nomorLomba as $nl)
                        <option value="{{ $nl->id }}" data-jenis="{{ $nl->jenis }}" data-gender="{{ $nl->gender }}" data-anggota="{{ $nl->jumlah_anggota ?? 1 }}">
                            [{{ $nl->cabangOlahraga?->nama }}] {{ $nl->nama }} ({{ $nl->gender === 'putra' ? 'Putra' : ($nl->gender === 'putri' ? 'Putri' : 'Campuran') }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Nama Tim jika Beregu --}}
            <div x-show="isBeregu" x-cloak>
                <label for="nama_tim" class="block text-xs font-semibold text-slate-700 mb-1">
                    Nama Tim / Regu <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nama_tim" name="nama_tim" x-bind:required="isBeregu"
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 focus:outline-none"
                    placeholder="Contoh: Regu A {{ $kontingen->nama }}">
            </div>

            {{-- Pilih Atlet --}}
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-2">
                    Pilih Atlet Kontingen <span class="text-red-500">*</span>
                </label>

                @if($atletTersedia->isEmpty())
                    <div class="rounded-xl border border-dashed border-slate-200 p-6 text-center text-xs text-slate-400">
                        Belum ada atlet yang terdaftar di kontingen ini.
                        <a href="{{ route('kontingen.atlet.create') }}" class="block font-semibold text-red-700 hover:underline mt-1">Tambah atlet terlebih dahulu</a>
                    </div>
                @else
                    <div class="space-y-2 max-h-60 overflow-y-auto rounded-xl border border-slate-200 p-3">
                        @foreach($atletTersedia as $a)
                            <label class="flex items-center justify-between rounded-lg p-2 hover:bg-slate-50 cursor-pointer text-xs">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" name="atlet_ids[]" value="{{ $a->id }}" class="rounded text-red-600 focus:ring-red-500">
                                    <div>
                                        <p class="font-bold text-slate-800">{{ $a->nama }}</p>
                                        <p class="text-[11px] text-slate-400">{{ $a->gender === 'L' ? 'Laki-laki (Putra)' : 'Perempuan (Putri)' }} &bull; {{ $a->hitungUmurPada() }} thn</p>
                                    </div>
                                </div>
                                <span class="rounded bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-600">
                                    {{ $a->asal_kota }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('kontingen.pendaftaran.index') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="rounded-xl bg-red-700 px-5 py-2.5 text-xs font-bold text-white shadow hover:bg-red-800 transition">
                    Submit Pendaftaran
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function pendaftaranForm() {
    return {
        selectedNomorId: '',
        isBeregu: false,
        onNomorChange() {
            const select = document.getElementById('nomor_lomba_id');
            const selectedOpt = select.options[select.selectedIndex];
            if (selectedOpt) {
                this.isBeregu = selectedOpt.getAttribute('data-jenis') === 'beregu';
            }
        }
    }
}
</script>
@endpush
@endsection
