<?php

namespace App\Http\Controllers\Cabor;

use App\Http\Controllers\Controller;
use App\Models\CabangOlahraga;
use App\Models\Lapangan;
use App\Models\Pertandingan;
use App\Services\PenjadwalanService;
use Illuminate\Http\Request;

class JadwalCaborController extends Controller
{
    public function __construct(protected PenjadwalanService $penjadwalanService) {}

    public function index(Request $request)
    {
        $user = auth()->user();
        $assignedCabor = $user->caborDitugaskan();

        if ($user->isAdmin() && $assignedCabor->isEmpty()) {
            $assignedCabor = CabangOlahraga::all();
        }

        $caborId = $request->get('cabor_id', $assignedCabor->first()?->id);
        $cabor = CabangOlahraga::with(['nomorLomba', 'venues.lapangan'])->find($caborId) ?? $assignedCabor->first();

        if (! $cabor) {
            return view('cabor.no-cabor');
        }

        $nomorLombaIds = $cabor->nomorLomba->pluck('id');

        $query = Pertandingan::with(['nomorLomba', 'lapangan.venue', 'peserta.peserta'])
            ->whereIn('nomor_lomba_id', $nomorLombaIds);

        if ($request->filled('nomor_lomba_id')) {
            $query->where('nomor_lomba_id', $request->nomor_lomba_id);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pertandingan = $query->orderBy('tanggal')
            ->orderBy('waktu_mulai')
            ->paginate(20)
            ->withQueryString();

        $lapangans = Lapangan::whereIn('venue_id', $cabor->venues->pluck('id'))->get();

        return view('cabor.jadwal.index', compact('cabor', 'assignedCabor', 'pertandingan', 'lapangans'));
    }

    public function pindah(Request $request)
    {
        $validated = $request->validate([
            'pertandingan_id' => ['required', 'exists:pertandingan,id'],
            'lapangan_id' => ['required', 'exists:lapangan,id'],
            'tanggal' => ['required', 'date'],
            'waktu_mulai' => ['required', 'date_format:H:i'],
            'durasi_menit' => ['nullable', 'integer', 'min:1'],
        ]);

        $pertandingan = Pertandingan::findOrFail($validated['pertandingan_id']);

        $pertandingan->update([
            'lapangan_id' => $validated['lapangan_id'],
            'tanggal' => $validated['tanggal'],
            'waktu_mulai' => $validated['waktu_mulai'],
            'durasi_menit' => $validated['durasi_menit'] ?? $pertandingan->durasi_menit,
        ]);

        return back()->with('success', 'Jadwal pertandingan berhasil dipindahkan.');
    }
}
