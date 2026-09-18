<?php

namespace App\Http\Controllers\Cabor;

use App\Http\Controllers\Controller;
use App\Models\HasilPertandingan;
use App\Models\Medali;
use App\Models\Pertandingan;
use App\Models\PesertaPertandingan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HasilCaborController extends Controller
{
    public function show(Pertandingan $pertandingan)
    {
        $pertandingan->load([
            'nomorLomba.cabangOlahraga.event',
            'lapangan.venue',
            'peserta.peserta.kontingen',
            'hasil',
        ]);

        if ($pertandingan->nomorLomba?->cabangOlahraga) {
            session([
                'pj_cabor_active_event_id' => $pertandingan->nomorLomba->cabangOlahraga->event_id,
                'pj_cabor_active_cabor_id' => $pertandingan->nomorLomba->cabang_olahraga_id,
            ]);
            view()->share('currentEvent', $pertandingan->nomorLomba->cabangOlahraga->event);
            view()->share('currentCabor', $pertandingan->nomorLomba->cabangOlahraga);
        }

        return view('cabor.hasil.show', compact('pertandingan'));
    }

    public function simpan(Request $request, Pertandingan $pertandingan)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:selesai,berlangsung,ditunda,dibatalkan'],
            'pemenang_id' => ['nullable', 'exists:peserta_pertandingan,id'],
            'skor' => ['nullable', 'array'],
            'skor.*' => ['nullable', 'string', 'max:50'],
            'catatan' => ['nullable', 'string', 'max:500'],
            'medali' => ['nullable', 'in:emas,perak,perunggu'],
        ]);

        DB::transaction(function () use ($pertandingan, $validated) {
            // Update skor tiap peserta
            if (! empty($validated['skor'])) {
                foreach ($validated['skor'] as $pesertaId => $skorVal) {
                    PesertaPertandingan::where('id', $pesertaId)
                        ->where('pertandingan_id', $pertandingan->id)
                        ->update([
                            'skor' => $skorVal,
                            'hasil' => ($validated['pemenang_id'] == $pesertaId) ? 'menang' : (($validated['pemenang_id']) ? 'kalah' : 'belum_tanding'),
                        ]);
                }
            }

            // Update status pertandingan
            $pertandingan->update([
                'status' => $validated['status'],
                'catatan' => $validated['catatan'] ?? null,
            ]);

            // Save hasil ringkasan — kolom sesuai skema DB
            $existingHasil = HasilPertandingan::where('pertandingan_id', $pertandingan->id)->first();
            $now = now();
            $userId = auth()->id();

            if ($existingHasil) {
                $existingHasil->update([
                    'pemenang_peserta_id' => $validated['pemenang_id'] ?? null,
                    'keterangan' => $validated['catatan'] ?? null,
                    'diubah_oleh' => $userId,
                    'alasan_perubahan' => 'Diperbarui via sistem PJ Cabor',
                ]);
            } else {
                HasilPertandingan::create([
                    'pertandingan_id' => $pertandingan->id,
                    'pemenang_peserta_id' => $validated['pemenang_id'] ?? null,
                    'keterangan' => $validated['catatan'] ?? null,
                    'diinput_oleh' => $userId,
                    'diinput_pada' => $now,
                ]);
            }

            // If final or medali awarded
            if ($validated['status'] === 'selesai' && ! empty($validated['medali']) && ! empty($validated['pemenang_id'])) {
                $pemenangPeserta = PesertaPertandingan::find($validated['pemenang_id']);
                if ($pemenangPeserta && $pemenangPeserta->peserta) {
                    $kontingenId = $pemenangPeserta->peserta->kontingen_id ?? $pemenangPeserta->peserta->id;
                    if ($kontingenId) {
                        Medali::updateOrCreate(
                            [
                                'event_id' => $pertandingan->nomorLomba->cabangOlahraga->event_id,
                                'nomor_lomba_id' => $pertandingan->nomor_lomba_id,
                                'jenis' => $validated['medali'],
                            ],
                            [
                                'kontingen_id' => $kontingenId,
                                'atlet_id' => $pemenangPeserta->peserta_type === 'App\\Models\\Atlet' ? $pemenangPeserta->peserta_id : null,
                                'tim_kontingen_id' => $pemenangPeserta->peserta_type === 'App\\Models\\TimKontingen' ? $pemenangPeserta->peserta_id : null,
                            ]
                        );
                    }
                }
            }
        });

        return redirect()->route('cabor.jadwal.index')
            ->with('success', 'Hasil dan skor pertandingan berhasil disimpan.');
    }
}
