<?php

namespace App\Http\Controllers\Kontingen;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\KlasemenMedaliService;

class DashboardKontingenController extends Controller
{
    public function index(KlasemenMedaliService $klasemenService)
    {
        $kontingen = auth()->user()->kontingen;
        if (! $kontingen) {
            return redirect()->route('login')->with('error', 'Akun Anda belum terhubung dengan data kontingen.');
        }

        $event = Event::whereIn('status', ['pendaftaran_dibuka', 'berlangsung', 'pendaftaran_ditutup'])->latest()->first();

        $stats = [
            'jumlah_atlet' => $kontingen->atlet()->count(),
            'pendaftaran_disetujui' => $kontingen->pendaftaran()->where('status', 'disetujui')->count(),
            'pendaftaran_menunggu' => $kontingen->pendaftaran()->whereIn('status', ['menunggu', 'diverifikasi_cabor'])->count(),
            'pendaftaran_ditolak' => $kontingen->pendaftaran()->whereIn('status', ['ditolak', 'ditolak_sistem'])->count(),
        ];

        // Medali kontingen ini
        $medaliKontingen = ['emas' => 0, 'perak' => 0, 'perunggu' => 0];
        if ($event) {
            $row = $klasemenService->getKlasemen($event->id)
                ->firstWhere('kontingen_id', $kontingen->id);
            if ($row) {
                $medaliKontingen = [
                    'emas' => $row['emas'],
                    'perak' => $row['perak'],
                    'perunggu' => $row['perunggu'],
                ];
            }
        }

        $pendaftaranTerbaru = $kontingen->pendaftaran()
            ->with(['nomorLomba.cabangOlahraga', 'atlet'])
            ->latest()
            ->take(5)
            ->get();

        $berkasKurang = $kontingen->atlet()
            ->whereDoesntHave('berkas')
            ->count();

        return view('kontingen.dashboard', compact(
            'kontingen', 'event', 'stats', 'medaliKontingen', 'pendaftaranTerbaru', 'berkasKurang'
        ));
    }
}
