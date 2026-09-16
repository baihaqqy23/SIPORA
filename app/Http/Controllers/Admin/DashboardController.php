<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Atlet;
use App\Models\Event;
use App\Models\Pendaftaran;
use App\Models\Pertandingan;
use App\Services\KlasemenMedaliService;
use App\Services\PenjadwalanService;

class DashboardController extends Controller
{
    public function index(KlasemenMedaliService $klasemenService)
    {
        $event = Event::whereIn('status', ['pendaftaran_dibuka', 'berlangsung', 'pendaftaran_ditutup'])->latest()->first();

        $stats = [
            'atlet_terdaftar' => Atlet::count(),
            'atlet_terverifikasi' => Atlet::whereHas('pendaftaran', fn ($q) => $q->where('status', 'disetujui'))->count(),
            'menunggu_verifikasi' => Pendaftaran::where('status', 'menunggu')->count()
                + Pendaftaran::where('status', 'diverifikasi_cabor')->count(),
            'pertandingan_hari_ini' => Pertandingan::whereDate('tanggal', today())
                ->whereNotIn('status', ['dibatalkan'])
                ->count(),
        ];

        $konflikHariIni = [];
        $totalKonflikKeras = 0;

        if ($event) {
            $tanggalHariIni = today()->format('Y-m-d');
            try {
                $penjadwalanService = app(PenjadwalanService::class);
                $deteksi = $penjadwalanService->deteksiKonflik($tanggalHariIni);
                $konflikHariIni = $deteksi['konflik_keras'];
                $totalKonflikKeras = count($deteksi['konflik_keras']);
            } catch (\Throwable $e) {
                // Jika belum ada jadwal, abaikan
            }
        }

        $pertandinganHariIni = Pertandingan::whereDate('tanggal', today())
            ->whereNotIn('status', ['dibatalkan'])
            ->with(['nomorLomba.cabangOlahraga', 'lapangan.venue', 'penugasanPanitia.panitia'])
            ->orderBy('waktu_mulai')
            ->take(20)
            ->get();

        $klasemenTop10 = $event
            ? $klasemenService->getKlasemen($event->id)->take(10)
            : collect();

        return view('admin.dashboard', compact(
            'event',
            'stats',
            'totalKonflikKeras',
            'konflikHariIni',
            'pertandinganHariIni',
            'klasemenTop10'
        ));
    }
}
