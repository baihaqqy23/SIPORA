<?php

namespace App\Http\Controllers\Kontingen;

use App\Http\Controllers\Controller;
use App\Models\Atlet;
use App\Models\Pertandingan;

class JadwalKontingenController extends Controller
{
    public function index()
    {
        $kontingen = auth()->user()->kontingen;
        if (! $kontingen) {
            return redirect()->route('login')->with('error', 'Data kontingen tidak ditemukan.');
        }

        $atletIds = $kontingen->atlet()->pluck('id')->all();

        $pertandingan = Pertandingan::whereHas('pesertaPertandingan', function ($q) use ($atletIds) {
            $q->where('peserta_type', Atlet::class)->whereIn('peserta_id', $atletIds);
        })
            ->whereIn('status', ['terjadwal', 'berlangsung', 'selesai', 'dipublikasikan'])
            ->with(['nomorLomba.cabangOlahraga', 'lapangan.venue', 'pesertaPertandingan.peserta', 'hasilPertandingan'])
            ->orderBy('tanggal')
            ->orderBy('waktu_mulai')
            ->get()
            ->groupBy(fn ($p) => $p->tanggal?->format('Y-m-d') ?? 'Belum Ditentukan');

        return view('kontingen.jadwal.index', compact('pertandingan', 'kontingen'));
    }
}
