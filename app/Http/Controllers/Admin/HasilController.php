<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CabangOlahraga;
use App\Models\HasilPertandingan;
use App\Models\Pertandingan;
use App\Models\PesertaPertandingan;
use App\Services\KlasemenMedaliService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HasilController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->get('status');
        $caborId = $request->get('cabor_id');
        $tanggal = $request->get('tanggal');

        $query = Pertandingan::with(['nomorLomba.cabangOlahraga', 'lapangan.venue', 'pesertaPertandingan.peserta'])
            ->when($status, fn ($q, $s) => $q->where('status', $s))
            ->when(! $status, fn ($q) => $q->whereIn('status', ['berlangsung', 'selesai', 'terjadwal']))
            ->when($caborId, fn ($q, $cId) => $q->whereHas('nomorLomba', fn ($n) => $n->where('cabang_olahraga_id', $cId)))
            ->when($tanggal, fn ($q, $tgl) => $q->whereDate('tanggal', $tgl))
            ->orderByDesc('tanggal')
            ->orderBy('waktu_mulai');

        $pertandingan = $query->paginate(20)->withQueryString();
        $caborList = CabangOlahraga::orderBy('nama')->get();

        $totalLaga = Pertandingan::count();
        $totalSelesai = Pertandingan::where('status', 'selesai')->count();
        $totalBerlangsung = Pertandingan::where('status', 'berlangsung')->count();

        return view('admin.hasil.index', compact(
            'pertandingan',
            'caborList',
            'status',
            'caborId',
            'tanggal',
            'totalLaga',
            'totalSelesai',
            'totalBerlangsung'
        ));
    }

    public function show(Pertandingan $pertandingan): View
    {
        $pertandingan->load([
            'nomorLomba.cabangOlahraga',
            'lapangan.venue',
            'pesertaPertandingan.peserta',
            'hasilPertandingan.penginput',
            'hasilPertandingan.pemenang',
        ]);

        return view('admin.hasil.show', compact('pertandingan'));
    }

    public function simpan(Request $request, Pertandingan $pertandingan, KlasemenMedaliService $klasemenService): RedirectResponse
    {
        $validated = $request->validate([
            'peserta' => ['required', 'array'],
            'peserta.*.id' => ['required', 'exists:peserta_pertandingan,id'],
            'peserta.*.skor' => ['nullable', 'integer'],
            'peserta.*.catatan_waktu' => ['nullable', 'numeric'],
            'peserta.*.nilai' => ['nullable', 'numeric'],
            'peserta.*.peringkat' => ['nullable', 'integer', 'min:1'],
            'pemenang_peserta_id' => ['nullable', 'exists:peserta_pertandingan,id'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(['terjadwal', 'berlangsung', 'selesai'])],
        ]);

        // Simpan skor / hasil individual peserta
        foreach ($request->peserta as $pData) {
            $peserta = PesertaPertandingan::find($pData['id']);
            if ($peserta) {
                $peserta->update([
                    'skor' => $pData['skor'] ?? $peserta->skor,
                    'catatan_waktu' => $pData['catatan_waktu'] ?? $peserta->catatan_waktu,
                    'nilai' => $pData['nilai'] ?? $peserta->nilai,
                    'peringkat' => $pData['peringkat'] ?? $peserta->peringkat,
                ]);
            }
        }

        // Simpan / update record hasil_pertandingan
        HasilPertandingan::updateOrCreate(
            ['pertandingan_id' => $pertandingan->id],
            [
                'pemenang_peserta_id' => $request->pemenang_peserta_id,
                'keterangan' => $request->keterangan,
                'diinput_oleh' => auth()->id(),
                'diinput_pada' => now(),
            ]
        );

        $pertandingan->update([
            'status' => $request->status,
        ]);

        return redirect()->route('admin.hasil.show', $pertandingan)
            ->with('success', 'Hasil pertandingan berhasil disimpan.');
    }

    public function batalkan(Pertandingan $pertandingan): RedirectResponse
    {
        $pertandingan->hasilPertandingan()->delete();

        // Reset skor peserta
        foreach ($pertandingan->pesertaPertandingan as $peserta) {
            $peserta->update([
                'skor' => null,
                'catatan_waktu' => null,
                'nilai' => null,
                'peringkat' => null,
            ]);
        }

        $pertandingan->update([
            'status' => 'terjadwal',
        ]);

        return back()->with('success', 'Hasil pertandingan berhasil dibatalkan dan status kembali ke terjadwal.');
    }
}
