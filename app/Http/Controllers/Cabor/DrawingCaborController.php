<?php

namespace App\Http\Controllers\Cabor;

use App\Http\Controllers\Controller;
use App\Models\NomorLomba;
use App\Models\Pertandingan;
use App\Services\DrawingService;
use Illuminate\Http\Request;

class DrawingCaborController extends Controller
{
    public function __construct(protected DrawingService $drawingService) {}

    public function show(NomorLomba $nomorLomba)
    {
        $nomorLomba->load(['cabangOlahraga', 'pendaftaran' => function ($q) {
            $q->where('status', 'disetujui')->with(['atlet.kontingen', 'timKontingen.kontingen']);
        }]);

        $pertandingan = Pertandingan::with(['peserta.peserta', 'lapangan'])
            ->where('nomor_lomba_id', $nomorLomba->id)
            ->orderBy('babak')
            ->orderBy('urutan_di_babak')
            ->get();

        return view('cabor.bracket.show', compact('nomorLomba', 'pertandingan'));
    }

    public function preview(Request $request, NomorLomba $nomorLomba)
    {
        $tipeBagan = $request->get('tipe', 'standar');
        $bracket = $this->drawingService->generateSingleElimination($nomorLomba);

        return response()->json([
            'success' => true,
            'bracket' => $bracket,
        ]);
    }

    public function simpan(Request $request, NomorLomba $nomorLomba)
    {
        $this->drawingService->simpanBracket($nomorLomba);

        return back()->with('success', "Bagan drawing untuk nomor lomba '{$nomorLomba->nama}' berhasil disimpan.");
    }
}
