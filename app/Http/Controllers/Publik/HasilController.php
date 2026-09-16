<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Pertandingan;
use Illuminate\Http\Request;

class HasilController extends Controller
{
    public function index(Request $request)
    {
        $pertandingan = Pertandingan::where('status', 'selesai')
            ->with([
                'nomorLomba.cabangOlahraga',
                'pesertaPertandingan.peserta',
                'hasilPertandingan',
            ])
            ->when($request->filled('cabor'), fn ($q) => $q->whereHas('nomorLomba', fn ($q2) => $q2->where('cabang_olahraga_id', $request->cabor)))
            ->latest('updated_at')
            ->paginate(20)->withQueryString();

        return view('publik.hasil', compact('pertandingan'));
    }
}
