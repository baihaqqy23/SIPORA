<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Atlet;
use App\Models\Kontingen;
use App\Models\Pertandingan;

class ProfilKontingenController extends Controller
{
    public function show(string $slug)
    {
        $kontingen = Kontingen::where('slug', $slug)
            ->with(['atlet', 'medali'])
            ->firstOrFail();

        $atletIds = $kontingen->atlet->pluck('id')->all();

        $pertandingan = Pertandingan::whereHas('pesertaPertandingan', function ($q) use ($atletIds) {
            $q->where('peserta_type', Atlet::class)->whereIn('peserta_id', $atletIds);
        })
            ->with(['nomorLomba.cabangOlahraga', 'hasilPertandingan', 'lapangan.venue'])
            ->whereIn('status', ['selesai', 'terjadwal', 'berlangsung', 'dipublikasikan'])
            ->orderBy('tanggal')
            ->get();

        $medaliCount = [
            'emas' => $kontingen->medali->where('jenis', 'emas')->count(),
            'perak' => $kontingen->medali->where('jenis', 'perak')->count(),
            'perunggu' => $kontingen->medali->where('jenis', 'perunggu')->count(),
        ];

        return view('publik.kontingen', compact('kontingen', 'pertandingan', 'medaliCount'));
    }
}
