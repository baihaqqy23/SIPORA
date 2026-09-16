@extends('layouts.publik')

@section('title', 'Pengumuman Resmi')
@section('meta_description', 'Informasi dan pengumuman resmi terkait penyelenggaraan Pekan Olahraga Nasional')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-800">Pengumuman Resmi</h1>
        <p class="text-sm text-slate-500 mt-1">Daftar informasi resmi, edaran teknis, dan berita penyelenggaraan event.</p>
    </div>

    @if($pengumuman->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center text-slate-400">
            <p class="text-base font-semibold text-slate-600">Belum ada pengumuman.</p>
            <p class="text-xs text-slate-400 mt-1">Pengumuman terbaru akan segera ditampilkan di sini.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($pengumuman as $p)
                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-3 hover:border-slate-300 transition">
                    <div class="flex items-center justify-between gap-2">
                        <span class="inline-block rounded px-2.5 py-1 text-xs font-semibold bg-red-100 text-red-800 uppercase tracking-wide">
                            {{ $p->target }}
                        </span>
                        <time class="text-xs text-slate-400">{{ $p->created_at->translatedFormat('d F Y, H:i') }} WIB</time>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800">{{ $p->judul }}</h2>
                    <div class="text-sm text-slate-600 leading-relaxed prose prose-sm max-w-none">
                        {!! $p->isi !!}
                    </div>
                    @if($p->lampiran_path)
                        <div class="pt-2">
                            <a href="{{ Storage::url($p->lampiran_path) }}" target="_blank" class="inline-flex items-center gap-2 text-xs font-semibold text-red-700 hover:text-red-800 underline">
                                📎 Unduh Lampiran Berkas
                            </a>
                        </div>
                    @endif
                </article>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $pengumuman->links() }}
        </div>
    @endif
</div>
@endsection
