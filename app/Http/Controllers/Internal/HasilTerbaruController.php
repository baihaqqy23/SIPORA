<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Pertandingan;

class HasilTerbaruController extends Controller
{
    public function index()
    {
        $hasil = Pertandingan::where('status', 'selesai')
            ->with([
                'nomorLomba.cabangOlahraga',
                'pesertaPertandingan.peserta',
                'hasilPertandingan',
            ])
            ->latest('updated_at')
            ->take(10)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'cabor' => $p->nomorLomba?->cabangOlahraga?->nama ?? '-',
                    'nomor_lomba' => $p->nomorLomba?->nama ?? '-',
                    'babak' => $p->babak,
                    'peserta' => $p->pesertaPertandingan->map(fn ($pp) => [
                        'nama' => $pp->peserta?->nama ?? 'Peserta',
                        'kontingen' => $pp->peserta?->kontingen?->nama ?? null,
                        'skor' => $pp->skor,
                        'hasil' => $pp->hasil,
                    ])->values()->all(),
                    'keterangan' => $p->hasilPertandingan?->keterangan,
                    'selesai_pada' => $p->updated_at?->toISOString(),
                ];
            });

        return response()->json(['data' => $hasil]);
    }
}
