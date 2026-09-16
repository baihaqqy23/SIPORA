<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Halaman Tidak Ditemukan | SIPORNAS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center bg-slate-50 p-6 font-sans antialiased text-slate-800">
    <div class="max-w-md w-full text-center space-y-6 rounded-2xl bg-white p-8 shadow-xl border border-slate-100">
        <div class="inline-flex h-20 w-20 items-center justify-center rounded-2xl bg-slate-100 text-slate-600 text-3xl font-black">
            404
        </div>
        <div class="space-y-2">
            <h1 class="text-xl font-bold text-slate-900">Halaman Tidak Ditemukan</h1>
            <p class="text-sm text-slate-500 leading-relaxed">
                Halaman atau tautan yang Anda cari tidak tersedia atau telah dipindahkan.
            </p>
        </div>
        <div class="pt-2 flex justify-center">
            <a href="{{ url('/') }}" class="rounded-xl bg-red-700 px-5 py-2.5 text-xs font-bold text-white shadow hover:bg-red-800 transition">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>
