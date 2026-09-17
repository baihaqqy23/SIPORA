<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Atlet;
use App\Models\Kontingen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AtletController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $kontingenId = $request->get('kontingen_id');
        $status = $request->get('status');
        $gender = $request->get('gender');

        $query = Atlet::with(['kontingen.event', 'berkas'])
            ->when($search, function ($q, $search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('asal_kota', 'like', "%{$search}%");
            })
            ->when($kontingenId, fn ($q, $id) => $q->where('kontingen_id', $id))
            ->when($status, fn ($q, $status) => $q->where('status', $status))
            ->when($gender, fn ($q, $gender) => $q->where('gender', $gender))
            ->orderBy('nama');

        $atlets = $query->paginate(20)->withQueryString();
        $kontingens = Kontingen::orderBy('nama')->get();

        $totalAtlet = Atlet::count();
        $totalTerverifikasi = Atlet::where('status', 'terverifikasi')->count();
        $totalMenunggu = Atlet::where('status', 'draft')->orWhere('status', 'menunggu')->count();

        return view('admin.atlet.index', compact(
            'atlets',
            'kontingens',
            'search',
            'kontingenId',
            'status',
            'gender',
            'totalAtlet',
            'totalTerverifikasi',
            'totalMenunggu'
        ));
    }

    public function show(Atlet $atlet): View
    {
        $atlet->load([
            'kontingen.event',
            'berkas',
            'pendaftaran.nomorLomba.cabangOlahraga',
            'pesertaPertandingan.pertandingan.nomorLomba.cabangOlahraga',
            'medali.nomorLomba.cabangOlahraga',
        ]);

        return view('admin.atlet.show', compact('atlet'));
    }

    public function edit(Atlet $atlet): View
    {
        $kontingens = Kontingen::orderBy('nama')->get();

        return view('admin.atlet.edit', compact('atlet', 'kontingens'));
    }

    public function update(Request $request, Atlet $atlet): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kontingen_id' => ['required', 'exists:kontingen,id'],
            'nik' => ['nullable', 'string', 'size:16'],
            'tanggal_lahir' => ['required', 'date'],
            'gender' => ['required', 'in:L,P'],
            'asal_kota' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:draft,menunggu,terverifikasi,ditolak,perlu_perbaikan'],
            'catatan_status' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            if ($atlet->foto_path) {
                Storage::disk('public')->delete($atlet->foto_path);
            }
            $validated['foto_path'] = $request->file('foto')->store('atlet/foto', 'public');
        }

        $atlet->update($validated);

        return redirect()->route('admin.atlet.show', $atlet)
            ->with('success', "Data atlet '{$atlet->nama}' berhasil diperbarui.");
    }

    public function destroy(Atlet $atlet): RedirectResponse
    {
        $nama = $atlet->nama;
        $atlet->delete();

        return redirect()->route('admin.atlet.index')
            ->with('success', "Data atlet '{$nama}' berhasil dihapus.");
    }
}
