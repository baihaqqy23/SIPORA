<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lapangan;
use App\Models\Pertandingan;
use App\Services\PenjadwalanService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PapanJadwalController extends Controller
{
    public function __construct(private readonly PenjadwalanService $penjadwalanService) {}

    public function index(Request $request)
    {
        $tanggal = $request->get('tanggal', today()->format('Y-m-d'));
        $matrix = $this->penjadwalanService->getMatrix($tanggal);
        $konflik = $this->penjadwalanService->deteksiKonflik($tanggal);
        $lapangan = Lapangan::with('venue')->orderBy('venue_id')->orderBy('nama')->get();

        return view('admin.papan-jadwal.index', compact('matrix', 'konflik', 'lapangan', 'tanggal'));
    }

    public function alokasi(Request $request)
    {
        $request->validate([
            'pertandingan_id' => ['required', 'exists:pertandingan,id'],
            'lapangan_id' => ['required', 'exists:lapangan,id'],
            'tanggal' => ['required', 'date'],
            'waktu_mulai' => ['required', 'date_format:H:i'],
        ]);

        try {
            $pertandingan = Pertandingan::findOrFail($request->pertandingan_id);
            $this->penjadwalanService->alokasiSlot(
                pertandingan: $pertandingan,
                lapanganId: $request->lapangan_id,
                tanggal: $request->tanggal,
                waktuMulai: $request->waktu_mulai,
            );
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true]);
    }

    public function pindah(Request $request)
    {
        $request->validate([
            'pertandingan_id' => ['required', 'exists:pertandingan,id'],
            'lapangan_id' => ['required', 'exists:lapangan,id'],
            'tanggal' => ['required', 'date'],
            'waktu_mulai' => ['required', 'date_format:H:i'],
        ]);

        try {
            $pertandingan = Pertandingan::findOrFail($request->pertandingan_id);
            $this->penjadwalanService->pindahSlot(
                pertandingan: $pertandingan,
                lapanganIdBaru: $request->lapangan_id,
                tanggalBaru: $request->tanggal,
                waktuMulaiBaru: $request->waktu_mulai,
            );
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true]);
    }

    public function publikasi(Pertandingan $pertandingan)
    {
        try {
            $this->penjadwalanService->publikasikan($pertandingan);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Jadwal pertandingan berhasil dipublikasikan.');
    }

    public function tunda(Request $request, Pertandingan $pertandingan)
    {
        $request->validate([
            'alasan' => ['nullable', 'string', 'max:500'],
        ]);

        $pertandingan->update([
            'status' => 'ditunda',
            'catatan' => $request->alasan,
        ]);

        return back()->with('success', 'Pertandingan berhasil ditandai sebagai ditunda.');
    }

    public function exportPdf(Request $request)
    {
        $tanggal = $request->get('tanggal', today()->format('Y-m-d'));
        $matrix = $this->penjadwalanService->getMatrix($tanggal);
        $lapangan = Lapangan::with('venue')->orderBy('venue_id')->get();

        $pdf = Pdf::loadView('pdf.jadwal', compact('matrix', 'lapangan', 'tanggal'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('jadwal-'.$tanggal.'.pdf');
    }
}
