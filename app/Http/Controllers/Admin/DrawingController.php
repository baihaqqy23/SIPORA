<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CabangOlahraga;
use App\Models\NomorLomba;
use App\Services\DrawingService;
use Illuminate\Http\Request;

class DrawingController extends Controller
{
    public function __construct(private readonly DrawingService $drawingService) {}

    public function index()
    {
        $cabors = CabangOlahraga::with('nomorLomba')->get();

        return view('admin.drawing.index', compact('cabors'));
    }

    public function show(NomorLomba $nomorLomba)
    {
        $nomorLomba->load(['cabangOlahraga', 'pertandingan.pesertaPertandingan']);

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

    public function simpan(Request $request, NomorLomba $nomorLomba)
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

    public function reset(Request $request, NomorLomba $nomorLomba)
    {
        try {
            $this->drawingService->resetBracket($nomorLomba);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Bracket berhasil direset.');
    }
}

// Fix missing Rule import
use Illuminate\Validation\Rule;
