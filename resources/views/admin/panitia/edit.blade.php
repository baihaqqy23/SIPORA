@extends('layouts.admin')

@section('title', 'Edit Panitia - ' . $panitia->nama)

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <a href="{{ route('admin.panitia.index') }}" class="hover:text-neutral-800">Panitia</a>
    <span class="mx-1 text-neutral-300">/</span>
    <a href="{{ route('admin.panitia.show', $panitia) }}" class="hover:text-neutral-800">{{ $panitia->nama }}</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-800 font-medium">Edit</span>
@endsection

@section('content')
<div class="max-w-3xl" x-data="panitiaForm({
    events: {{ \Illuminate\Support\Js::from($events->map(fn($e) => ['id' => $e->id, 'nama' => $e->nama, 'cabang_olahraga' => $e->cabangOlahraga->map(fn($c) => ['id' => $c->id, 'nama' => $c->nama])])) }},
    selectedEventId: '{{ old('event_id', $panitia->event_id) }}',
    selectedCaborId: '{{ old('penugasan_cabor_id', $penugasan?->cabang_olahraga_id) }}'
})">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.panitia.show', $panitia) }}"
           class="p-1.5 text-neutral-400 hover:text-neutral-700 rounded-lg hover:bg-neutral-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900">Edit Data Panitia</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Perbarui profil, jabatan, cabor, atau kontak panitia.</p>
        </div>
    </div>

    <form action="{{ route('admin.panitia.update', $panitia) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="p-6 bg-white border border-neutral-200 rounded-xl shadow-sm space-y-5">
            {{-- Event Selection --}}
            <div>
                <label for="event_id" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-1.5">
                    Event <span class="text-red-500">*</span>
                </label>
                <select id="event_id" name="event_id" required x-model="selectedEventId" @change="onEventChange()"
                        class="w-full text-sm border border-neutral-300 rounded-lg px-3 py-2.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition @error('event_id') border-red-500 @enderror">
                    <option value="">Pilih Event...</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}" {{ old('event_id', $panitia->event_id) == $event->id ? 'selected' : '' }}>
                            {{ $event->nama }} {{ $event->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('event_id')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nama & Jabatan --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nama" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-1.5">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nama" name="nama" required
                           value="{{ old('nama', $panitia->nama) }}"
                           class="w-full text-sm border border-neutral-300 rounded-lg px-3 py-2.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition @error('nama') border-red-500 @enderror">
                    @error('nama')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jabatan: Combobox --}}
                <div x-data="jabatanCombobox('{{ old('jabatan', $panitia->jabatan) }}')" class="relative">
                    <label class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-1.5">
                        Jabatan Kepanitiaan <span class="text-red-500">*</span>
                    </label>
                    <input type="hidden" name="jabatan" :value="jabatan" required>

                    <div x-show="!isCustom">
                        <select @change="onSelectChange($event)"
                                class="w-full text-sm border border-neutral-300 rounded-lg px-3 py-2.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition @error('jabatan') border-red-500 @enderror">
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach(['Ketua Pelaksana','Sekretaris Pelaksana','Koordinator Pertandingan','PJ Cabang Olahraga','Wasit / Juri','Pencatat Skor / Operator','Liaison Officer (LO)','Tim Medis','Keamanan','Dokumentasi & Publikasi'] as $opt)
                                <option value="{{ $opt }}" :selected="jabatan === '{{ $opt }}'">{{ $opt }}</option>
                            @endforeach
                            <option value="__custom__">+ Ketik Jabatan Lainnya...</option>
                        </select>
                    </div>

                    <div x-show="isCustom" class="flex items-center gap-2">
                        <input type="text" x-model="jabatan" placeholder="Ketik jabatan..."
                               class="flex-1 text-sm border border-neutral-300 rounded-lg px-3 py-2.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition">
                        <button type="button" @click="resetToSelect()" title="Kembali ke pilihan"
                                class="shrink-0 p-2.5 text-neutral-500 hover:text-neutral-800 border border-neutral-300 rounded-lg hover:bg-neutral-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    @error('jabatan')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Cabang Olahraga (Cascading sesuai Event) --}}
            <div>
                <label for="penugasan_cabor_id" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-1.5">
                    Cabang Olahraga <span class="text-red-500">*</span>
                </label>
                <select id="penugasan_cabor_id" name="penugasan_cabor_id" required x-model="selectedCaborId"
                        :disabled="!selectedEventId"
                        class="w-full text-sm border border-neutral-300 rounded-lg px-3 py-2.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition disabled:bg-neutral-100 disabled:text-neutral-400 @error('penugasan_cabor_id') border-red-500 @enderror">
                    <option value="" x-text="selectedEventId ? '-- Pilih Cabang Olahraga --' : '-- Pilih Event Terlebih Dahulu --'">-- Pilih Cabang Olahraga --</option>
                    <template x-for="cabor in availableCabors" :key="cabor.id">
                        <option :value="cabor.id" x-text="cabor.nama" :selected="cabor.id == selectedCaborId"></option>
                    </template>
                </select>
                <p x-show="selectedEventId && availableCabors.length === 0" class="text-xs text-amber-600 mt-1" x-cloak>
                    Peringatan: Belum ada cabang olahraga yang didaftarkan pada event ini.
                </p>
                @error('penugasan_cabor_id')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Instansi & Kontak --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="instansi" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-1.5">
                        Instansi / Lembaga
                    </label>
                    <input type="text" id="instansi" name="instansi"
                           value="{{ old('instansi', $panitia->instansi) }}"
                           class="w-full text-sm border border-neutral-300 rounded-lg px-3 py-2.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition">
                </div>

                <div>
                    <label for="no_hp" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-1.5">
                        No. HP / WhatsApp <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="no_hp" name="no_hp" required
                           value="{{ old('no_hp', $panitia->no_hp) }}"
                           class="w-full text-sm border border-neutral-300 rounded-lg px-3 py-2.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition @error('no_hp') border-red-500 @enderror">
                    @error('no_hp')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-1.5">
                        Email
                    </label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email', $panitia->email) }}"
                           class="w-full text-sm border border-neutral-300 rounded-lg px-3 py-2.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Foto --}}
            <div>
                <label for="foto" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-1.5">
                    Ganti Foto Panitia (Opsional)
                </label>
                @if($panitia->foto_path)
                    <div class="mb-2 flex items-center gap-2">
                        <img src="{{ Storage::url($panitia->foto_path) }}" class="w-10 h-10 rounded-full object-cover border border-neutral-200">
                        <span class="text-xs text-neutral-500">Foto profil saat ini</span>
                    </div>
                @endif
                <input type="file" id="foto" name="foto" accept="image/*"
                       class="w-full text-xs text-neutral-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.panitia.show', $panitia) }}"
               class="px-4 py-2.5 text-sm font-medium text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm transition-colors">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function panitiaForm(config) {
        return {
            events: config.events || [],
            selectedEventId: config.selectedEventId || '',
            selectedCaborId: config.selectedCaborId || '',
            get availableCabors() {
                if (!this.selectedEventId) return [];
                const ev = this.events.find(e => String(e.id) === String(this.selectedEventId));
                return ev ? ev.cabang_olahraga : [];
            },
            onEventChange() {
                const exists = this.availableCabors.some(c => String(c.id) === String(this.selectedCaborId));
                if (!exists) {
                    this.selectedCaborId = '';
                }
            }
        };
    }

    function jabatanCombobox(initial) {
        const presets = [
            'Ketua Pelaksana','Sekretaris Pelaksana','Koordinator Pertandingan',
            'PJ Cabang Olahraga','Wasit / Juri','Pencatat Skor / Operator',
            'Liaison Officer (LO)','Tim Medis','Keamanan','Dokumentasi & Publikasi',
        ];
        const isPreset = initial && presets.includes(initial);
        return {
            jabatan: initial || '',
            isCustom: initial && !isPreset,
            onSelectChange(e) {
                const val = e.target.value;
                if (val === '__custom__') { this.isCustom = true; this.jabatan = ''; }
                else { this.jabatan = val; }
            },
            resetToSelect() { this.isCustom = false; this.jabatan = ''; }
        };
    }
</script>
@endpush
@endsection
