<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\NomorLomba;

class AtletEligibleController extends Controller
{
    public function index()
    {
        $nomorLombaId = request('nomor_lomba_id');
        $q = request('q', '');

        if (! $nomorLombaId) {
            return response()->json(['atlet' => []]);
        }

        $kontingen = auth()->user()->kontingen;
        $nomorLomba = NomorLomba::find($nomorLombaId);

        if (! $nomorLomba || ! $kontingen) {
            return response()->json(['atlet' => []]);
        }

        $atlet = $kontingen->atlet()
            ->where('status', 'aktif')
            ->when($nomorLomba->jenis_kelamin !== 'campuran', function ($query) use ($nomorLomba) {
                $jk = $nomorLomba->jenis_kelamin === 'putra' ? 'L' : 'P';
                $query->where('jenis_kelamin', $jk);
            })
            ->when($q, fn ($query) => $query->where('nama_lengkap', 'like', '%'.$q.'%'))
            ->whereDoesntHave('pendaftaran', function ($query) use ($nomorLombaId) {
                $query->where('nomor_lomba_id', $nomorLombaId)
                    ->whereIn('status', ['menunggu', 'diverifikasi_cabor', 'disetujui']);
            })
            ->orderBy('nama_lengkap')
            ->take(30)
            ->get(['id', 'nama_lengkap', 'jenis_kelamin', 'tanggal_lahir'])
            ->map(fn ($a) => [
                'id' => $a->id,
                'nama' => $a->nama_lengkap,
                'jk' => $a->jenis_kelamin,
                'umur' => $a->hitungUmurPada(now()),
            ]);

        return response()->json(['atlet' => $atlet]);
    }
}
