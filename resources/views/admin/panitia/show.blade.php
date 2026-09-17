@extends('layouts.admin')

@section('title', $panitia->nama)

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-neutral-800">Dashboard</a>
    <span class="mx-1 text-neutral-300">/</span>
    <a href="{{ route('admin.panitia.index') }}" class="hover:text-neutral-800">Panitia</a>
    <span class="mx-1 text-neutral-300">/</span>
    <span class="text-neutral-800 font-medium">{{ $panitia->nama }}</span>
@endsection

@section('content')
@php
    $isPjCabor = str_contains(strtolower($panitia->jabatan), 'pj') || str_contains(strtolower($panitia->jabatan), 'penanggung jawab');
@endphp
<div class="space-y-6" x-data="{ buatAkunModal: false, editAkunModal: false }">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.panitia.index') }}" class="p-1.5 text-neutral-400 hover:text-neutral-700 rounded-lg hover:bg-neutral-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            @if($panitia->foto_path)
                <img src="{{ Storage::url($panitia->foto_path) }}" alt="{{ $panitia->nama }}"
                     class="w-14 h-14 rounded-full object-cover border border-neutral-200">
            @else
                <div class="w-14 h-14 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-lg">
                    {{ strtoupper(substr($panitia->nama, 0, 2)) }}
                </div>
            @endif
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-neutral-900">{{ $panitia->nama }}</h1>
                <p class="text-sm text-neutral-500 mt-0.5">
                    {{ $panitia->jabatan }}
                    @if($panitia->instansi) &bull; {{ $panitia->instansi }} @endif
                    &bull; {{ $panitia->event?->nama }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.panitia.edit', $panitia) }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors">
                <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Panitia
            </a>
            <form action="{{ route('admin.panitia.destroy', $panitia) }}" method="POST"
                  onsubmit="return confirm('Hapus data panitia {{ $panitia->nama }}?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus
                </button>
            </form>
        </div>
    </div>

    {{-- Grid Details --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kolom Kiri: Kontak & Akun --}}
        <div class="space-y-5">
            {{-- Info Profil --}}
            <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-5 space-y-4">
                <h2 class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">Informasi Panitia</h2>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-neutral-400 text-xs block">Jabatan Kepanitiaan</span>
                        <span class="text-neutral-800 font-semibold">{{ $panitia->jabatan }}</span>
                    </div>
                    <div>
                        <span class="text-neutral-400 text-xs block">Cabang Olahraga</span>
                        <span class="text-neutral-800 font-semibold">{{ $penugasan?->cabangOlahraga?->nama ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-neutral-400 text-xs block">Instansi / Asal</span>
                        <span class="text-neutral-800 font-medium">{{ $panitia->instansi ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-neutral-400 text-xs block">WhatsApp / HP</span>
                        <span class="text-neutral-800 font-medium">{{ $panitia->no_hp }}</span>
                    </div>
                    <div>
                        <span class="text-neutral-400 text-xs block">Email Panitia</span>
                        <span class="text-neutral-800 font-medium">{{ $panitia->email ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-neutral-400 text-xs block">Event</span>
                        <span class="text-neutral-800 font-medium">{{ $panitia->event?->nama ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Akun Login --}}
            <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">Akun Login Sistem</h2>
                    @if($panitia->user)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $panitia->user->status === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                            {{ $panitia->user->status }}
                        </span>
                    @endif
                </div>

                @if($panitia->user)
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                        <div>
                            <span class="text-[10px] text-slate-400 block uppercase font-bold">Email Login</span>
                            <span class="text-xs font-semibold text-slate-800 font-mono">{{ $panitia->user->email }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block uppercase font-bold">Role Akses</span>
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-100 text-blue-800 mt-0.5">
                                {{ strtoupper(str_replace('_', ' ', $panitia->user->role)) }}
                            </span>
                        </div>
                        @if($panitia->user->harus_ganti_password)
                            <div class="text-[11px] text-amber-600 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>Wajib ganti password saat login</span>
                            </div>
                        @endif
                        <div class="pt-2">
                            <button type="button" @click="editAkunModal = true"
                                    class="w-full px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                                Kelola / Reset Password
                            </button>
                        </div>
                    </div>
                @elseif($isPjCabor)
                    {{-- Hanya tampil jika jabatan PJ --}}
                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl space-y-2 text-center">
                        <p class="text-xs text-amber-800 font-medium">PJ Cabor belum memiliki akun login sistem.</p>
                        <button type="button" @click="buatAkunModal = true"
                                class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 rounded-lg shadow-sm transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            Buat Akun Login PJ Cabor
                        </button>
                    </div>
                @else
                    {{-- Jabatan non-PJ: hanya info --}}
                    <div class="p-4 bg-neutral-50 border border-neutral-200 rounded-xl text-center">
                        <p class="text-xs text-neutral-500">Panitia ini tidak memerlukan akun sistem.</p>
                        <p class="text-[11px] text-neutral-400 mt-1">Akun sistem hanya diperlukan untuk PJ Cabor.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Kolom Kanan: Ringkasan Penugasan --}}
        <div class="lg:col-span-2 bg-white border border-neutral-200 rounded-xl shadow-sm p-5 space-y-4">
            <div>
                <h2 class="text-base font-bold text-neutral-900">Ringkasan Penugasan</h2>
                <p class="text-xs text-neutral-500 mt-0.5">Detail afiliasi cabang olahraga dan jabatan panitia ini dalam event.</p>
            </div>

            @if($penugasan && $penugasan->cabangOlahraga)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Cabor Card --}}
                    <div class="p-4 rounded-xl border border-blue-100 bg-blue-50 space-y-1">
                        <span class="text-[10px] font-bold uppercase text-blue-500 tracking-wider">Cabang Olahraga</span>
                        <p class="text-lg font-bold text-blue-800">{{ $penugasan->cabangOlahraga->nama }}</p>
                        @if(isset($penugasan->cabangOlahraga->kategori))
                            <p class="text-xs text-blue-600">{{ $penugasan->cabangOlahraga->kategori }}</p>
                        @endif
                    </div>
                    {{-- Jabatan Card --}}
                    <div class="p-4 rounded-xl border border-neutral-200 bg-neutral-50 space-y-1">
                        <span class="text-[10px] font-bold uppercase text-neutral-400 tracking-wider">Jabatan</span>
                        <p class="text-lg font-bold text-neutral-800">{{ $panitia->jabatan }}</p>
                        @if($isPjCabor)
                            <span class="inline-block text-[10px] font-bold uppercase px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded">
                                Akun Sistem Diperlukan
                            </span>
                        @else
                            <span class="inline-block text-[10px] font-bold uppercase px-2 py-0.5 bg-neutral-200 text-neutral-600 rounded">
                                Data Informasi
                            </span>
                        @endif
                    </div>
                </div>

                <div class="mt-2 p-4 bg-neutral-50 border border-neutral-200 rounded-xl text-sm text-neutral-600">
                    <p class="text-xs text-neutral-500">
                        <span class="font-semibold text-neutral-700">{{ $panitia->nama }}</span>
                        bertugas sebagai <span class="font-semibold text-neutral-800">{{ $panitia->jabatan }}</span>
                        pada cabang olahraga <span class="font-semibold text-neutral-800">{{ $penugasan->cabangOlahraga->nama }}</span>
                        dalam event <span class="font-semibold text-neutral-800">{{ $panitia->event?->nama }}</span>.
                    </p>
                    @if(!$isPjCabor)
                        <p class="text-xs text-neutral-400 mt-2 italic">
                            Flow internal jabatan ini dikelola di luar sistem. Tidak ada akun login yang diperlukan.
                        </p>
                    @endif
                </div>
            @else
                <div class="p-8 text-center border border-dashed border-neutral-300 rounded-xl">
                    <p class="text-sm text-neutral-400">Belum ada cabang olahraga yang ditetapkan.</p>
                    <a href="{{ route('admin.panitia.edit', $panitia) }}"
                       class="inline-block mt-3 text-xs font-semibold text-blue-600 hover:underline">
                        Edit untuk menambahkan Cabang Olahraga →
                    </a>
                </div>
            @endif

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs text-emerald-700 font-medium">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-xs text-red-700 font-medium">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal: Buat Akun Baru (hanya untuk PJ Cabor) --}}
    @if($isPjCabor && !$panitia->user)
    <div x-show="buatAkunModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.outside="buatAkunModal = false" class="bg-white border border-slate-200 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900">Buat Akun Login PJ Cabor</h3>
                <button @click="buatAkunModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>
            <form action="{{ route('admin.panitia.buat-akun', $panitia) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-neutral-700 mb-1.5">Email Login <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $panitia->email) }}" required placeholder="pjcabor@example.com"
                           class="w-full text-sm border border-neutral-300 rounded-lg px-3 py-2.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-700 mb-1.5">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required minlength="8" placeholder="Min. 8 karakter"
                           class="w-full text-sm border border-neutral-300 rounded-lg px-3 py-2.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-700 mb-1.5">Konfirmasi Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" required minlength="8" placeholder="Ulangi password"
                           class="w-full text-sm border border-neutral-300 rounded-lg px-3 py-2.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition">
                </div>
                <input type="hidden" name="role" value="pj_cabor">
                <div>
                    <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-neutral-700">
                        <input type="checkbox" name="harus_ganti_password" value="1" class="rounded border-neutral-300 text-blue-600">
                        <span>Wajib ganti password pada login pertama</span>
                    </label>
                </div>
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="buatAkunModal = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition-colors">Batal</button>
                    <button type="submit"
                            class="px-5 py-2 text-xs font-semibold text-white bg-slate-900 rounded-xl shadow-sm hover:bg-slate-800 transition-colors">Simpan & Buat Akun</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Modal: Kelola / Reset Akun --}}
    @if($panitia->user)
    <div x-show="editAkunModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.outside="editAkunModal = false" class="bg-white border border-slate-200 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900">Kelola Akun Login & Password</h3>
                <button @click="editAkunModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>
            <form action="{{ route('admin.panitia.reset-password', $panitia) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-neutral-700 mb-1.5">Email Login <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $panitia->user->email) }}" required
                           class="w-full text-sm border border-neutral-300 rounded-lg px-3 py-2.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-700 mb-1.5">Password Baru (Opsional)</label>
                    <input type="password" name="password" minlength="8" placeholder="Biarkan kosong jika tidak diganti"
                           class="w-full text-sm border border-neutral-300 rounded-lg px-3 py-2.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-700 mb-1.5">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" minlength="8" placeholder="Ulangi password baru"
                           class="w-full text-sm border border-neutral-300 rounded-lg px-3 py-2.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 mb-1.5">Role Akun</label>
                        <select name="role" class="w-full text-sm border border-neutral-300 rounded-lg px-3 py-2.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition">
                            <option value="pj_cabor" {{ $panitia->user->role === 'pj_cabor' ? 'selected' : '' }}>PJ Cabor</option>
                            <option value="admin" {{ $panitia->user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 mb-1.5">Status Akun</label>
                        <select name="status" class="w-full text-sm border border-neutral-300 rounded-lg px-3 py-2.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition">
                            <option value="aktif" {{ $panitia->user->status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ $panitia->user->status === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-neutral-700">
                        <input type="checkbox" name="harus_ganti_password" value="1" {{ $panitia->user->harus_ganti_password ? 'checked' : '' }}
                               class="rounded border-neutral-300 text-blue-600">
                        <span>Wajib ganti password pada login berikutnya</span>
                    </label>
                </div>
                <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                    <form action="{{ route('admin.panitia.hapus-akun', $panitia) }}" method="POST"
                          onsubmit="return confirm('Hapus akun login untuk panitia ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-red-600 hover:underline">Hapus Akun</button>
                    </form>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="editAkunModal = false"
                                class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition-colors">Batal</button>
                        <button type="submit"
                                class="px-5 py-2 text-xs font-semibold text-white bg-slate-900 rounded-xl shadow-sm hover:bg-slate-800 transition-colors">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
