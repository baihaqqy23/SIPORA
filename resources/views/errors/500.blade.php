<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - Terjadi Kesalahan Server | {{ config('app.name', 'SISPORA') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 flex items-center justify-center p-6">
    <div class="max-w-md w-full text-center space-y-6">
        <div class="w-20 h-20 mx-auto bg-rose-100 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 rounded-3xl flex items-center justify-center shadow-lg shadow-rose-500/10">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>

        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400">Error 500</span>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white mt-1">Terjadi Gangguan Server</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Maaf, sistem mengalami kendala teknis saat memproses permintaan Anda. Tim kami telah mencatat aktivitas ini.</p>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            <a href="javascript:history.back()" class="w-full sm:w-auto px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm transition-all text-center">
                &larr; Kembali
            </a>
            <a href="{{ url('/') }}" class="w-full sm:w-auto px-5 py-2.5 text-sm font-medium text-white bg-slate-900 dark:bg-primary-600 hover:bg-slate-800 dark:hover:bg-primary-700 rounded-xl shadow-sm transition-all text-center">
                Ke Beranda
            </a>
        </div>
    </div>
</body>
</html>
