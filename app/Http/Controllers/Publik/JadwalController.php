<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Pertandingan;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->get('tanggal', today()->format('Y-m-d'));

        $pertandingan = Pertandingan::whereDate('tanggal', $tanggal)
            ->whereIn('status', ['terjadwal', 'berlangsung', 'selesai'])
            ->with(['nomorLomba.cabangOlahraga', 'lapangan.venue'])
            ->orderBy('waktu_mulai')
            ->get()
            ->groupBy(fn ($p) => $p->lapangan?->venue?->nama ?? 'Venue Tidak Diketahui');

        return view('publik.jadwal', compact('pertandingan', 'tanggal'));
    }
}
