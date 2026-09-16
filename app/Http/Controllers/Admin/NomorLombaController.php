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
            'kode' => ['required', 'string', 'max:20'],
            'nama' => ['required', 'string', 'max:150'],
            'jenis_kelamin' => ['required', Rule::in(['putra', 'putri', 'campuran'])],
            'kelompok_umur' => ['nullable', 'string', 'max:50'],
            'durasi_pertandingan_menit' => ['nullable', 'integer', 'min:1'],
            'kuota_peserta' => ['nullable', 'integer', 'min:2'],
            'jumlah_atlet_per_tim' => ['nullable', 'integer', 'min:1'],
            'catatan_teknis' => ['nullable', 'string'],
        ]);

        $data['cabang_olahraga_id'] = $cabor->id;
        $data['status'] = 'aktif';

        NomorLomba::create($data);

        return redirect()->route('admin.cabor.show', $cabor)
            ->with('success', 'Nomor lomba berhasil ditambahkan.');
    }

    public function edit(NomorLomba $nomorLomba)
    {
        $cabor = $nomorLomba->cabangOlahraga;

        return view('admin.nomor-lomba.edit', compact('nomorLomba', 'cabor'));
    }

    public function update(Request $request, NomorLomba $nomorLomba)
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:20'],
            'nama' => ['required', 'string', 'max:150'],
            'jenis_kelamin' => ['required', Rule::in(['putra', 'putri', 'campuran'])],
            'kelompok_umur' => ['nullable', 'string', 'max:50'],
            'durasi_pertandingan_menit' => ['nullable', 'integer', 'min:1'],
            'kuota_peserta' => ['nullable', 'integer', 'min:2'],
            'jumlah_atlet_per_tim' => ['nullable', 'integer', 'min:1'],
            'catatan_teknis' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
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
