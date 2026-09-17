<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PengumumanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengumuman::with('event')->latest();

        if ($request->filled('target') && $request->target !== 'semua') {
            $query->where('target', $request->target);
        }

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('judul', 'like', '%'.$request->q.'%')
                    ->orWhere('isi', 'like', '%'.$request->q.'%');
            });
        }

        $pengumuman = $query->paginate(15)->withQueryString();

        return view('admin.pengumuman.index', compact('pengumuman'));
    }

    public function create()
    {
        $events = Event::orderBy('tanggal_mulai', 'desc')->get();

        return view('admin.pengumuman.create', compact('events'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'judul' => ['required', 'string', 'max:250'],
            'isi' => ['required', 'string'],
            'target' => ['required', Rule::in(['semua', 'publik', 'kontingen', 'panitia'])],
            'tayang_mulai' => ['nullable', 'date'],
            'lampiran' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx,zip', 'max:10240'],
        ]);

        if ($request->hasFile('lampiran')) {
            $data['lampiran_path'] = $request->file('lampiran')->store('pengumuman', 'public');
        }

        Pengumuman::create($data);

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil diterbitkan.');
    }

    public function edit(Pengumuman $pengumuman)
    {
        $events = Event::orderBy('tanggal_mulai', 'desc')->get();

        return view('admin.pengumuman.edit', compact('pengumuman', 'events'));
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        $data = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'judul' => ['required', 'string', 'max:250'],
            'isi' => ['required', 'string'],
            'target' => ['required', Rule::in(['semua', 'publik', 'kontingen', 'panitia'])],
            'tayang_mulai' => ['nullable', 'date'],
            'lampiran' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx,zip', 'max:10240'],
        ]);

        if ($request->hasFile('lampiran')) {
            if ($pengumuman->lampiran_path && Storage::disk('public')->exists($pengumuman->lampiran_path)) {
                Storage::disk('public')->delete($pengumuman->lampiran_path);
            }
            $data['lampiran_path'] = $request->file('lampiran')->store('pengumuman', 'public');
        }

        $pengumuman->update($data);

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        if ($pengumuman->lampiran_path && Storage::disk('public')->exists($pengumuman->lampiran_path)) {
            Storage::disk('public')->delete($pengumuman->lampiran_path);
        }

        $pengumuman->delete();

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }
}
