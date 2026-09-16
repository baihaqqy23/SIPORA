<?php

namespace App\Http\Controllers\Kontingen;

use App\Http\Controllers\Controller;
use App\Models\Atlet;
use App\Models\BerkasAtlet;
use App\Models\Kontingen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AtletController extends Controller
{
    private function kontingen(): ?Kontingen
    {
        return auth()->user()->kontingen;
    }

    public function index()
    {
        $kontingen = $this->kontingen();
        if (! $kontingen) {
            return redirect()->route('login')->with('error', 'Data kontingen tidak ditemukan.');
        }

        $atlet = $kontingen->atlet()
            ->with(['pendaftaran.nomorLomba.cabangOlahraga'])
            ->latest()
            ->paginate(20);

        return view('kontingen.atlet.index', compact('atlet', 'kontingen'));
    }

    public function create()
    {
        $kontingen = $this->kontingen();

        return view('kontingen.atlet.create', compact('kontingen'));
    }

    public function store(Request $request)
    {
        $kontingen = $this->kontingen();

        $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'nik' => ['required', 'string', 'max:50'],
            'gender' => ['required', Rule::in(['L', 'P'])],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'asal_kota' => ['required', 'string', 'max:100'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('foto_atlet', 'public');
        }

        Atlet::create([
            'kontingen_id' => $kontingen->id,
            'nama' => $request->nama,
            'nik' => $request->nik,
            'gender' => $request->gender,
            'tanggal_lahir' => $request->tanggal_lahir,
            'asal_kota' => $request->asal_kota,
            'foto_path' => $fotoPath,
            'status' => 'aktif',
        ]);

        return redirect()->route('kontingen.atlet.index')
            ->with('success', 'Data atlet berhasil ditambahkan.');
    }

    public function edit(Atlet $atlet)
    {
        $kontingen = $this->kontingen();
        if ($atlet->kontingen_id !== $kontingen->id) {
            abort(403, 'Anda tidak memiliki akses ke data atlet ini.');
        }

        $atlet->load('berkas');

        return view('kontingen.atlet.edit', compact('atlet', 'kontingen'));
    }

    public function update(Request $request, Atlet $atlet)
    {
        $kontingen = $this->kontingen();
        if ($atlet->kontingen_id !== $kontingen->id) {
            abort(403, 'Anda tidak memiliki akses ke data atlet ini.');
        }

        $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'nik' => ['required', 'string', 'max:50'],
            'gender' => ['required', Rule::in(['L', 'P'])],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'asal_kota' => ['required', 'string', 'max:100'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        $data = $request->only(['nama', 'nik', 'gender', 'tanggal_lahir', 'asal_kota']);

        if ($request->hasFile('foto')) {
            if ($atlet->foto_path) {
                Storage::disk('public')->delete($atlet->foto_path);
            }
            $data['foto_path'] = $request->file('foto')->store('foto_atlet', 'public');
        }

        $atlet->update($data);

        return redirect()->route('kontingen.atlet.index')
            ->with('success', 'Data atlet berhasil diperbarui.');
    }

    public function destroy(Atlet $atlet)
    {
        $kontingen = $this->kontingen();
        if ($atlet->kontingen_id !== $kontingen->id) {
            abort(403, 'Anda tidak memiliki akses ke data atlet ini.');
        }

        if ($atlet->pendaftaran()->whereIn('status', ['disetujui', 'diverifikasi_cabor'])->exists()) {
            return back()->with('error', 'Atlet tidak dapat dihapus karena memiliki pendaftaran yang sudah diverifikasi.');
        }

        $atlet->delete();

        return redirect()->route('kontingen.atlet.index')
            ->with('success', 'Data atlet berhasil dihapus.');
    }

    public function uploadBerkas(Request $request, Atlet $atlet)
    {
        $kontingen = $this->kontingen();
        if ($atlet->kontingen_id !== $kontingen->id) {
            abort(403, 'Anda tidak memiliki akses ke data atlet ini.');
        }

        $request->validate([
            'jenis' => ['required', Rule::in(['akta', 'kk', 'kartu_pelajar', 'surat_sehat', 'lainnya'])],
            'berkas' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);

        $file = $request->file('berkas');
        $path = $file->store('berkas_atlet', 'private');

        BerkasAtlet::create([
            'atlet_id' => $atlet->id,
            'jenis' => $request->jenis,
            'file_path' => $path,
            'nama_file_asli' => $file->getClientOriginalName(),
            'ukuran_byte' => $file->getSize(),
            'mime' => $file->getMimeType() ?? 'application/octet-stream',
        ]);

        return back()->with('success', 'Berkas berhasil diunggah.');
    }

    public function deleteBerkas(BerkasAtlet $berkas)
    {
        $kontingen = $this->kontingen();
        if ($berkas->atlet?->kontingen_id !== $kontingen->id) {
            abort(403, 'Anda tidak memiliki akses ke berkas ini.');
        }

        Storage::disk('private')->delete($berkas->file_path);
        $berkas->delete();

        return back()->with('success', 'Berkas berhasil dihapus.');
    }
}
