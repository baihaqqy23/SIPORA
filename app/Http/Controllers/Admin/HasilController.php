<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HasilPertandingan;
use App\Models\Pertandingan;
use App\Services\KlasemenMedaliService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HasilController extends Controller
{
    public function index(Request $request)
    {
        $pertandingan = Pertandingan::with(['nomorLomba.cabangOlahraga', 'lapangan.venue'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when(! $request->filled('status'), fn ($q) => $q->whereIn('status', ['berlangsung', 'selesai', 'terjadwal']))
            ->latest('tanggal')
            ->paginate(25)->withQueryString();

        return view('admin.hasil.index', compact('pertandingan'));
    }

    public function show(Pertandingan $pertandingan)
    {
        $pertandingan->load([
            'nomorLomba.cabangOlahraga',
            'pesertaPertandingan.atlet',
            'pesertaPertandingan.kontingen',
            'hasilPertandingan',
            'medali',
        ]);

        return view('admin.hasil.show', compact('pertandingan'));
    }

    public function simpan(Request $request, Pertandingan $pertandingan, KlasemenMedaliService $klasemenService)
    {
        $request->validate([
            'peserta' => ['required', 'array'],
            'peserta.*.peserta_pertandingan_id' => ['required', 'exists:peserta_pertandingan,id'],
            'peserta.*.skor' => ['nullable', 'numeric'],
            'peserta.*.posisi' => ['nullable', 'integer', 'min:1'],
            'peserta.*.poin' => ['nullable', 'numeric'],
            'peserta.*.keterangan' => ['nullable', 'string', 'max:500'],
            'pemenang_id' => ['nullable', 'exists:peserta_pertandingan,id'],
            'status' => ['required', Rule::in(['berlangsung', 'selesai'])],
        ]);

        foreach ($request->peserta as $peserta) {
            HasilPertandingan::updateOrCreate(
                ['peserta_pertandingan_id' => $peserta['peserta_pertandingan_id']],
                [
                    'pertandingan_id' => $pertandingan->id,
                    'skor' => $peserta['skor'] ?? null,
                    'posisi' => $peserta['posisi'] ?? null,
                    'poin' => $peserta['poin'] ?? null,
                    'keterangan' => $peserta['keterangan'] ?? null,
                ]
            );
        }

        if ($request->pemenang_id) {
            $pertandingan->update(['pemenang_peserta_id' => $request->pemenang_id]);
        }

        $pertandingan->update(['status' => $request->status]);

        if ($request->status === 'selesai') {
            $klasemenService->prosesHasilPertandingan($pertandingan);
        }

        return redirect()->route('admin.hasil.show', $pertandingan)
            ->with('success', 'Hasil pertandingan berhasil disimpan.');
    }

    public function batalkan(Pertandingan $pertandingan)
    {
        $pertandingan->hasilPertandingan()->delete();
        $pertandingan->medali()->delete();
        $pertandingan->update([
            'status' => 'terjadwal',
            'pemenang_peserta_id' => null,
        ]);

        return back()->with('success', 'Hasil pertandingan berhasil dibatalkan.');
    }
}
