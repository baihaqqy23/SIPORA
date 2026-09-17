<?php

namespace App\Http\Controllers\Cabor;

use App\Http\Controllers\Controller;
use App\Models\CabangOlahraga;
use App\Models\Pendaftaran;
use App\Models\Pertandingan;
use Illuminate\Http\Request;

class DashboardCaborController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $assignedCabor = $user->caborDitugaskan();

        // If admin, can view any cabor
        if ($user->isAdmin() && $assignedCabor->isEmpty()) {
            $assignedCabor = CabangOlahraga::all();
        }

        $caborId = $request->get('cabor_id', $assignedCabor->first()?->id);
        $cabor = CabangOlahraga::with(['nomorLomba', 'venues'])->find($caborId) ?? $assignedCabor->first();

        if (! $cabor) {
            return view('cabor.no-cabor');
        }

        $nomorLombaIds = $cabor->nomorLomba->pluck('id');

        // Statistics
        $totalNomorLomba = $cabor->nomorLomba->count();
        $totalPendaftaran = Pendaftaran::whereIn('nomor_lomba_id', $nomorLombaIds)->count();
        $menungguVerifikasi = Pendaftaran::whereIn('nomor_lomba_id', $nomorLombaIds)->where('status', 'menunggu')->count();
        $totalPertandingan = Pertandingan::whereIn('nomor_lomba_id', $nomorLombaIds)->count();
        $pertandinganSelesai = Pertandingan::whereIn('nomor_lomba_id', $nomorLombaIds)->where('status', 'selesai')->count();

        // Pertandingan Hari Ini / Berlangsung
        $pertandinganHariIni = Pertandingan::with(['nomorLomba', 'lapangan.venue', 'peserta.peserta'])
            ->whereIn('nomor_lomba_id', $nomorLombaIds)
            ->whereDate('tanggal', today())
            ->orderBy('waktu_mulai')
            ->get();

        if ($pertandinganHariIni->isEmpty()) {
            $pertandinganHariIni = Pertandingan::with(['nomorLomba', 'lapangan.venue', 'peserta.peserta'])
                ->whereIn('nomor_lomba_id', $nomorLombaIds)
                ->orderBy('tanggal')
                ->orderBy('waktu_mulai')
                ->take(5)
                ->get();
        }

        // Pendaftaran Terbaru yang butuh verifikasi
        $pendaftaranTerbaru = Pendaftaran::with(['atlet.kontingen', 'nomorLomba', 'timKontingen.kontingen'])
            ->whereIn('nomor_lomba_id', $nomorLombaIds)
            ->where('status', 'menunggu')
            ->latest()
            ->take(5)
            ->get();

        return view('cabor.dashboard', compact(
            'cabor',
            'assignedCabor',
            'totalNomorLomba',
            'totalPendaftaran',
            'menungguVerifikasi',
            'totalPertandingan',
            'pertandinganSelesai',
            'pertandinganHariIni',
            'pendaftaranTerbaru'
        ));
    }
}
