<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PenugasanPanitia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PenugasanPanitiaController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'panitia_id' => ['required', 'exists:panitia,id'],
            'cabang_olahraga_id' => ['nullable', 'exists:cabang_olahraga,id'],
            'pertandingan_id' => ['nullable', 'exists:pertandingan,id'],
            'peran' => ['required', 'in:ketua_pelaksana,pj_cabor,wasit,juri,pencatat_skor,lo_kontingen,medis,keamanan,dokumentasi'],
        ]);

        PenugasanPanitia::create($validated);

        return redirect()->route('admin.panitia.show', $validated['panitia_id'])
            ->with('success', 'Penugasan panitia berhasil ditambahkan.');
    }

    public function destroy(PenugasanPanitia $penugasan): RedirectResponse
    {
        $panitiaId = $penugasan->panitia_id;
        $penugasan->delete();

        return redirect()->route('admin.panitia.show', $panitiaId)
            ->with('success', 'Penugasan berhasil dihapus.');
    }
}
