<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Pengumuman;
use App\Models\Pertandingan;

class BerandaController extends Controller
{
    public function index()
    {
        $event = Event::whereIn('status', ['pendaftaran_dibuka', 'berlangsung', 'pendaftaran_ditutup'])->latest()->first();

        $pengumuman = Pengumuman::whereIn('target', ['publik', 'semua'])
            ->where(fn ($q) => $q->whereNull('tayang_mulai')->orWhere('tayang_mulai', '<=', now()))
            ->latest()
            ->take(5)
            ->get();

        $pertandinganHariIni = Pertandingan::whereDate('tanggal', today())
            ->whereIn('status', ['terjadwal', 'berlangsung', 'dipublikasikan'])
            ->with(['nomorLomba.cabangOlahraga', 'lapangan.venue'])
            ->orderBy('waktu_mulai')
            ->take(10)
            ->get();

        $hasilTerbaru = Pertandingan::where('status', 'selesai')
            ->with([
                'nomorLomba.cabangOlahraga',
                'pesertaPertandingan',
                'hasilPertandingan',
            ])
            ->latest('updated_at')
            ->take(5)
            ->get();

        $hasilTerbaruArray = $hasilTerbaru->map(function ($p) {
            return [
                'id' => $p->id,
                'cabor' => $p->nomorLomba?->cabangOlahraga?->nama ?? '-',
                'nomor_lomba' => $p->nomorLomba?->nama ?? '-',
                'babak' => $p->babak,
                'peserta' => $p->pesertaPertandingan->map(function ($pp) {
                    return [
                        'nama' => $pp->peserta?->nama ?? 'Peserta',
                        'skor' => $pp->skor,
                    ];
                })->values()->all(),
            ];
        })->values()->all();

        return view('publik.beranda', compact('event', 'pengumuman', 'pertandinganHariIni', 'hasilTerbaru', 'hasilTerbaruArray'));
    }
}
