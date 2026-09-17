@extends('layouts.admin')

@section('title', 'Tambah Panitia Baru')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <a href="{{ route('admin.panitia.index') }}" class="hover:text-neutral-800">Panitia</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-800 font-medium">Tambah</span>
@endsection

@section('content')
<div class="max-w-3xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-neutral-900">Tambah Panitia / Perangkat Lomba</h1>
        <p class="text-sm text-neutral-500 mt-1">Daftarkan panitia pelaksana, penanggung jawab cabang olahraga (PJ Cabor), atau wasit.</p>
    </div>

    <form action="{{ route('admin.panitia.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="p-6 bg-white border border-neutral-200 rounded-xl shadow-sm space-y-5">
            {{-- Event Selection --}}
            <div>
                <label for="event_id" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-1.5">
                    Event <span class="text-red-500">*</span>
                </label>
                <select id="event_id" name="event_id" required
                        class="w-full text-sm border border-neutral-300 rounded-lg px-3 py-2.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition @error('event_id') border-red-500 @enderror">
                    <option value="">Pilih Event...</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}" {{ old('event_id', $activeEvent?->id) == $event->id ? 'selected' : '' }}>
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
                    <input type="text" id="nama" name="nama" required placeholder="Nama dan gelar..."
                           value="{{ old('nama') }}"
                           class="w-full text-sm border border-neutral-300 rounded-lg px-3 py-2.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition @error('nama') border-red-500 @enderror">
                    @error('nama')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jabatan: Combobox (select preset + custom input) --}}
                <div x-data="jabatanCombobox('{{ old('jabatan') }}')" class="relative">
                    <label class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-1.5">
                        Jabatan Kepanitiaan <span class="text-red-500">*</span>
                    </label>

                    {{-- Hidden input yang dikirim ke server --}}
                    <input type="hidden" name="jabatan" :value="jabatan" required>

                    {{-- Select dropdown --}}
                    <div x-show="!isCustom">
                        <select @change="onSelectChange($event)"
                                class="w-full text-sm border border-neutral-300 rounded-lg px-3 py-2.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition @error('jabatan') border-red-500 @enderror">
                            <option value="">-- Pilih Jabatan --</option>
                            <option value="Ketua Pelaksana">Ketua Pelaksana</option>
                            <option value="Sekretaris Pelaksana">Sekretaris Pelaksana</option>
                            <option value="Koordinator Pertandingan">Koordinator Pertandingan</option>
                            <option value="PJ Cabang Olahraga">PJ Cabang Olahraga</option>
                            <option value="Wasit / Juri">Wasit / Juri</option>
                            <option value="Pencatat Skor / Operator">Pencatat Skor / Operator</option>
                            <option value="Liaison Officer (LO)">Liaison Officer (LO)</option>
                            <option value="Tim Medis">Tim Medis</option>
                            <option value="Keamanan">Keamanan</option>
                            <option value="Dokumentasi & Publikasi">Dokumentasi & Publikasi</option>
                            <option value="__custom__">+ Ketik Jabatan Lainnya...</option>
                        </select>
                    </div>

                    {{-- Custom text input --}}
                    <div x-show="isCustom" class="flex items-center gap-2">
                        <input type="text" x-model="jabatan" placeholder="Ketik jabatan..."
                               class="flex-1 text-sm border border-neutral-300 rounded-lg px-3 py-2.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition @error('jabatan') border-red-500 @enderror">
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

            {{-- Instansi & Kontak --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="instansi" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-1.5">
                        Instansi / Asal Lembaga
                    </label>
                    <input type="text" id="instansi" name="instansi" placeholder="Dispora / KONI / Pengcab"
                           value="{{ old('instansi') }}"
                           class="w-full text-sm border border-neutral-300 rounded-lg px-3 py-2.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition">
                </div>

                <div>
                    <label for="no_hp" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-1.5">
                        No. WhatsApp / HP <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="no_hp" name="no_hp" required placeholder="08123456789"
                           value="{{ old('no_hp') }}"
                           class="w-full text-sm border border-neutral-300 rounded-lg px-3 py-2.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition @error('no_hp') border-red-500 @enderror">
                    @error('no_hp')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-1.5">
                        Email
                    </label>
                    <input type="email" id="email" name="email" placeholder="panitia@example.com"
                           value="{{ old('email') }}"
                           class="w-full text-sm border border-neutral-300 rounded-lg px-3 py-2.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Penugasan Cabor --}}
            <div>
                <label for="penugasan_cabor_id" class="block text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-1.5">
                    Cabang Olahraga <span class="text-red-500">*</span>
                </label>
                <select id="penugasan_cabor_id" name="penugasan_cabor_id" required
                        class="w-full text-sm border border-neutral-300 rounded-lg px-3 py-2.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition @error('penugasan_cabor_id') border-red-500 @enderror">
                    <option value="">-- Pilih Cabang Olahraga --</option>
                    @foreach($caborList as $cabor)
                        <option value="{{ $cabor->id }}" {{ old('penugasan_cabor_id') == $cabor->id ? 'selected' : '' }}>
                            {{ $cabor->nama }}
                        </option>
                    @endforeach
                </select>
                @error('penugasan_cabor_id')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Buat Akun Login --}}
            <div x-data="{ makeUser: {{ old('create_user') ? 'true' : 'false' }} }" class="pt-4 border-t border-neutral-100">
                <label class="inline-flex items-center gap-2 cursor-pointer mb-3">
                    <input type="checkbox" name="create_user" value="1" x-model="makeUser" class="rounded border-neutral-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm font-semibold text-neutral-900">Buatkan Akun Login Sistem (PJ Cabor / Admin)</span>
                </label>

                <div x-show="makeUser" x-cloak
                     class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-blue-50 rounded-xl border border-blue-200">
                    <div class="sm:col-span-2">
                        <label for="user_email" class="block text-xs font-semibold text-neutral-700 mb-1.5">Email Login <span class="text-red-500">*</span></label>
                        <input type="email" id="user_email" name="user_email" placeholder="pjcabor@example.com" value="{{ old('user_email') }}"
                               class="w-full text-sm border border-neutral-300 rounded-lg px-3 py-2.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition @error('user_email') border-red-500 @enderror">
                        @error('user_email')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password" class="block text-xs font-semibold text-neutral-700 mb-1.5">Password <span class="text-red-500">*</span></label>
                        <input type="password" id="password" name="password" placeholder="Min. 8 karakter"
                               class="w-full text-sm border border-neutral-300 rounded-lg px-3 py-2.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-neutral-700 mb-1.5">Konfirmasi Password <span class="text-red-500">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password"
                               class="w-full text-sm border border-neutral-300 rounded-lg px-3 py-2.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition">
                    </div>
                    <div>
                        <label for="role" class="block text-xs font-semibold text-neutral-700 mb-1.5">Role Akun</label>
                        <select id="role" name="role"
                                class="w-full text-sm border border-neutral-300 rounded-lg px-3 py-2.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition">
                            <option value="pj_cabor" {{ old('role') == 'pj_cabor' ? 'selected' : '' }}>PJ Cabang Olahraga</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                        </select>
                    </div>
                    <div class="flex items-center pt-4">
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-neutral-700">
                            <input type="checkbox" name="harus_ganti_password" value="1" {{ old('harus_ganti_password') ? 'checked' : '' }}
                                   class="rounded border-neutral-300 text-blue-600">
                            <span>Wajib ganti password pada login pertama</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.panitia.index') }}"
               class="px-4 py-2.5 text-sm font-medium text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm transition-colors">
                Simpan Panitia
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function jabatanCombobox(initial) {
        const presets = [
            'Ketua Pelaksana', 'Sekretaris Pelaksana', 'Koordinator Pertandingan',
            'PJ Cabang Olahraga', 'Wasit / Juri', 'Pencatat Skor / Operator',
            'Liaison Officer (LO)', 'Tim Medis', 'Keamanan', 'Dokumentasi & Publikasi',
        ];

        const isPreset = initial && presets.includes(initial);

        return {
            jabatan: initial || '',
            isCustom: initial && !isPreset,
            onSelectChange(e) {
                const val = e.target.value;
                if (val === '__custom__') {
                    this.isCustom = true;
                    this.jabatan = '';
                } else {
                    this.jabatan = val;
                }
            },
            resetToSelect() {
                this.isCustom = false;
                this.jabatan = '';
            }
        };
    }
</script>
@endpush
@endsection
