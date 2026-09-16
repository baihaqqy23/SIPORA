<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PengumumanController extends Controller
{
    public function index()
    {
        $pengumuman = Pengumuman::latest()->paginate(20);

        return view('admin.pengumuman.index', compact('pengumuman'));
    }

    public function create()
    {
        return view('admin.pengumuman.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul' => ['required', 'string', 'max:250'],
            'isi' => ['required', 'string'],
            'tipe' => ['required', Rule::in(['informasi', 'peringatan', 'darurat'])],
            'target_role' => ['required', Rule::in(['semua', 'kontingen', 'pj_cabor', 'panitia'])],
            'tayang_mulai' => ['nullable', 'date'],
            'tayang_selesai' => ['nullable', 'date', 'after_or_equal:tayang_mulai'],
            'dipinkan' => ['boolean'],
        ]);

        $data['dipinkan'] = $request->boolean('dipinkan');
        $data['dibuat_oleh'] = auth()->id();
        $data['status'] = $request->boolean('langsung_publikasi') ? 'dipublikasikan' : 'draft';

        Pengumuman::create($data);

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil disimpan.');
    }

    public function edit(Pengumuman $pengumuman)
    {
        return view('admin.pengumuman.edit', compact('pengumuman'));
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        $data = $request->validate([
            'judul' => ['required', 'string', 'max:250'],
            'isi' => ['required', 'string'],
            'tipe' => ['required', Rule::in(['informasi', 'peringatan', 'darurat'])],
            'target_role' => ['required', Rule::in(['semua', 'kontingen', 'pj_cabor', 'panitia'])],
            'tayang_mulai' => ['nullable', 'date'],
            'tayang_selesai' => ['nullable', 'date'],
            'dipinkan' => ['boolean'],
            'status' => ['required', Rule::in(['draft', 'dipublikasikan', 'diarsipkan'])],
        ]);

        $data['dipinkan'] = $request->boolean('dipinkan');

        $pengumuman->update($data);

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        $pengumuman->delete();

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }
}
