@extends(auth()->user()?->role === 'kontingen' ? 'layouts.kontingen' : 'layouts.admin')

@section('title', 'Pusat Notifikasi')

@section('content')
<div class="p-6 sm:p-8 max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Pusat Pemberitahuan</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Daftar semua notifikasi, update pertandingan, dan aktivitas akun Anda.</p>
        </div>
        @if($notifikasi->whereNull('dibaca_pada')->count() > 0)
            <form action="{{ route('notifikasi.baca-semua') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm transition-all">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Tandai Semua Sudah Dibaca
                </button>
            </form>
        @endif
    </div>

    <!-- Notification List -->
    @if($notifikasi->isEmpty())
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-xl p-12 text-center shadow-sm">
            <div class="w-16 h-16 mx-auto bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center text-slate-400 mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-slate-900 dark:text-white">Tidak Ada Notifikasi</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">Anda belum memiliki pemberitahuan baru saat ini.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($notifikasi as $notif)
                <div class="bg-white dark:bg-slate-900 border {{ $notif->dibaca_pada ? 'border-slate-200/70 dark:border-slate-800 opacity-80' : 'border-primary-200 dark:border-primary-800/60 shadow-sm bg-primary-50/10' }} rounded-xl p-4 transition-all hover:border-slate-300 dark:hover:border-slate-700 flex items-start gap-4">
                    <div class="p-2.5 rounded-xl flex-shrink-0 
                        @if($notif->tipe === 'peringatan' || $notif->tipe === 'warning') bg-amber-100 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400
                        @elseif($notif->tipe === 'darurat' || $notif->tipe === 'danger') bg-rose-100 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400
                        @elseif($notif->tipe === 'sukses' || $notif->tipe === 'success') bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400
                        @else bg-blue-100 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400
                        @endif">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                                {{ $notif->judul }}
                            </h2>
                            <span class="text-xs text-slate-400 flex-shrink-0">
                                {{ $notif->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">
                            {{ $notif->pesan }}
                        </p>
                        @if($notif->url_tujuan)
                            <div class="mt-2.5">
                                <a href="{{ $notif->url_tujuan }}" class="inline-flex items-center gap-1 text-xs font-semibold text-primary-600 dark:text-primary-400 hover:underline">
                                    Buka Halaman Terkait &rarr;
                                </a>
                            </div>
                        @endif
                    </div>

                    @if(!$notif->dibaca_pada)
                        <div class="flex-shrink-0 self-center">
                            <span class="w-2.5 h-2.5 rounded-full bg-primary-600 block ring-4 ring-primary-100 dark:ring-primary-950/50"></span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $notifikasi->links() }}
        </div>
    @endif
</div>
@endsection
