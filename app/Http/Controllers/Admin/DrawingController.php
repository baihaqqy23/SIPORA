<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CabangOlahraga;
use App\Models\NomorLomba;
use App\Services\DrawingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DrawingController extends Controller
{
    public function __construct(private readonly DrawingService $drawingService) {}

    public function index(Request $request): View
    {
        $caborId = $request->get('cabor_id');

        $cabors = CabangOlahraga::with(['nomorLomba.pertandingan'])
            ->when($caborId, fn ($q) => $q->where('id', $caborId))
            ->orderBy('nama')
            ->get();

        $caborFilterList = CabangOlahraga::orderBy('nama')->get();

        return view('admin.drawing.index', compact('cabors', 'caborFilterList', 'caborId'));
    }

    public function show(NomorLomba $nomorLomba): View
    {
        $nomorLomba->load([
            'cabangOlahraga',
            'pertandingan.pesertaPertandingan.peserta',
            'pertandingan.hasilPertandingan',
        ]);

        $bracket = $this->drawingService->getBracket($nomorLomba);

        return view('admin.drawing.show', compact('nomorLomba', 'bracket'));
    }

    public function preview(Request $request, NomorLomba $nomorLomba)
    {
        $request->validate([
            'seed' => ['nullable', 'string', Rule::in(['acak', 'ranking'])],
        ]);

        try {
            $preview = $this->drawingService->previewDrawing($nomorLomba, $request->seed ?? 'acak');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return view('admin.drawing.preview', compact('nomorLomba', 'preview'));
    }

    public function simpan(Request $request, NomorLomba $nomorLomba): RedirectResponse
    {
        $request->validate([
            'seed' => ['nullable', 'string'],
        ]);

        try {
            $this->drawingService->laksanakanDrawing($nomorLomba, $request->seed ?? 'acak');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.bracket.show', $nomorLomba)
            ->with('success', 'Drawing berhasil dilaksanakan. Bracket telah digenerate.');
    }

    public function reset(Request $request, NomorLomba $nomorLomba): RedirectResponse
    {
        try {
            $this->drawingService->resetBracket($nomorLomba);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Bracket berhasil direset.');
    }
}
