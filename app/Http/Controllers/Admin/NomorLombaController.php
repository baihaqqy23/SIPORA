<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CabangOlahraga;
use App\Models\NomorLomba;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NomorLombaController extends Controller
{
    public function index(CabangOlahraga $cabor)
    {
        $nomorLomba = $cabor->nomorLomba()->orderBy('nama')->paginate(20);

        return view('admin.nomor-lomba.index', compact('cabor', 'nomorLomba'));
    }

    public function create(CabangOlahraga $cabor)
    {
        return view('admin.nomor-lomba.create', compact('cabor'));
    }

    public function store(Request $request, CabangOlahraga $cabor)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'gender' => ['required', Rule::in(['putra', 'putri', 'campuran'])],
            'jenis' => ['required', Rule::in(['perorangan', 'beregu'])],
            'jumlah_anggota' => ['nullable', 'integer', 'min:1'],
            'jumlah_cadangan' => ['nullable', 'integer', 'min:0'],
            'umur_min' => ['nullable', 'integer', 'min:0'],
            'umur_maks' => ['nullable', 'integer', 'min:0'],
            'format_pertandingan' => ['required', Rule::in(['gugur_tunggal', 'round_robin', 'heat', 'penilaian'])],
            'kuota_per_kontingen' => ['nullable', 'integer', 'min:1'],
            'kapasitas_total' => ['nullable', 'integer', 'min:1'],
            'jumlah_perunggu' => ['nullable', 'integer', 'min:1'],
        ]);

        $data['cabang_olahraga_id'] = $cabor->id;

        NomorLomba::create($data);

        return redirect()->route('admin.cabor.show', $cabor)
            ->with('success', 'Nomor lomba berhasil ditambahkan.');
    }

    public function show(NomorLomba $nomorLomba)
    {
        return redirect()->route('admin.cabor.show', $nomorLomba->cabang_olahraga_id);
    }

    public function edit(NomorLomba $nomorLomba)
    {
        $cabor = $nomorLomba->cabangOlahraga;

        return view('admin.nomor-lomba.edit', compact('nomorLomba', 'cabor'));
    }

    public function update(Request $request, NomorLomba $nomorLomba)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'gender' => ['required', Rule::in(['putra', 'putri', 'campuran'])],
            'jenis' => ['required', Rule::in(['perorangan', 'beregu'])],
            'jumlah_anggota' => ['nullable', 'integer', 'min:1'],
            'jumlah_cadangan' => ['nullable', 'integer', 'min:0'],
            'umur_min' => ['nullable', 'integer', 'min:0'],
            'umur_maks' => ['nullable', 'integer', 'min:0'],
            'format_pertandingan' => ['required', Rule::in(['gugur_tunggal', 'round_robin', 'heat', 'penilaian'])],
            'kuota_per_kontingen' => ['nullable', 'integer', 'min:1'],
            'kapasitas_total' => ['nullable', 'integer', 'min:1'],
            'jumlah_perunggu' => ['nullable', 'integer', 'min:1'],
        ]);

        $nomorLomba->update($data);

        return redirect()->route('admin.cabor.show', $nomorLomba->cabangOlahraga)
            ->with('success', 'Nomor lomba berhasil diperbarui.');
    }

    public function destroy(NomorLomba $nomorLomba)
    {
        $cabor = $nomorLomba->cabangOlahraga;
        $nomorLomba->delete();

        return redirect()->route('admin.cabor.show', $cabor)
            ->with('success', 'Nomor lomba berhasil dihapus.');
    }
}
