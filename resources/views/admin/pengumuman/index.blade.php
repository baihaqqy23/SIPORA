@extends('layouts.admin')

@section('title', 'Manajemen Pengumuman')

@section('content')
<div class="p-6 sm:p-8 max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Papan Pengumuman</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Publikasikan informasi penting, surat edaran, dan pengumuman kepada kontingen dan panitia.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.pengumuman.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-slate-900 dark:bg-primary-600 hover:bg-slate-800 dark:hover:bg-primary-700 rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Pengumuman
            </a>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('admin.pengumuman.index') }}" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1 relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul pengumuman atau isi konten..." class="w-full pl-9 pr-4 py-2 text-sm rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            <div class="w-full md:w-52">
                <select name="target" onchange="this.form.submit()" class="w-full py-2 px-3 text-sm rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                    <option value="semua" {{ request('target') == 'semua' || !request('target') ? 'selected' : '' }}>Semua Target</option>
                    <option value="publik" {{ request('target') == 'publik' ? 'selected' : '' }}>Publik (Umum)</option>
                    <option value="kontingen" {{ request('target') == 'kontingen' ? 'selected' : '' }}>Kontingen / Official</option>
                    <option value="panitia" {{ request('target') == 'panitia' ? 'selected' : '' }}>Panitia / Wasit</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['q', 'target']))
                <a href="{{ route('admin.pengumuman.index') }}" class="px-4 py-2 text-sm font-medium text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 rounded-lg transition-colors flex items-center justify-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Notification / List of Announcements -->
    @if($pengumuman->isEmpty())
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-xl p-12 text-center shadow-sm">
            <div class="w-16 h-16 mx-auto bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center text-slate-400 mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-slate-900 dark:text-white">Belum Ada Pengumuman</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">Buat pengumuman pertama Anda untuk dibagikan kepada peserta, ofisial, atau panitia perlombaan.</p>
            <div class="mt-6">
                <a href="{{ route('admin.pengumuman.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Pengumuman Baru
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($pengumuman as $item)
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-xl p-5 shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
                    <div>
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium 
                                @if($item->target === 'publik') bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-300 border border-sky-200/60 dark:border-sky-800
                                @elseif($item->target === 'kontingen') bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800
                                @elseif($item->target === 'panitia') bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800
                                @else bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200/60 dark:border-purple-800
                                @endif">
                                Target: {{ ucfirst($item->target) }}
                            </span>
                            <span class="text-xs text-slate-400">
                                {{ $item->created_at->translatedFormat('d M Y, H:i') }}
                            </span>
                        </div>

                        <h2 class="text-base font-semibold text-slate-900 dark:text-white group-hover:text-primary-600 transition-colors">
                            {{ $item->judul }}
                        </h2>

                        @if($item->event)
                            <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 mt-1 mb-3">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>{{ $item->event->nama }}</span>
                            </div>
                        @endif

                        <p class="text-sm text-slate-600 dark:text-slate-300 line-clamp-3 mt-2 whitespace-pre-line leading-relaxed">
                            {{ $item->isi }}
                        </p>
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            @if($item->lampiran_path)
                                <a href="{{ asset('storage/' . $item->lampiran_path) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-medium text-primary-600 dark:text-primary-400 hover:underline">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                    Unduh Lampiran
                                </a>
                            @else
                                <span class="text-xs text-slate-400 italic">Tidak ada lampiran</span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.pengumuman.edit', $item) }}" class="p-1.5 text-slate-500 hover:text-primary-600 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form action="{{ route('admin.pengumuman.destroy', $item) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $pengumuman->links() }}
        </div>
    @endif
</div>
@endsection
