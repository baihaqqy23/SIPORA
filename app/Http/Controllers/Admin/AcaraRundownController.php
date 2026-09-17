<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcaraRundown;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcaraRundownController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('admin.rundown.index');
    }

    public function create(Request $request): View
    {
        $activeEvent = Event::whereIn('status', ['berlangsung', 'pendaftaran_dibuka'])->first() ?? Event::latest()->first();
        $events = Event::orderBy('nama')->get();
        $defaultDate = $request->get('tanggal', now()->format('Y-m-d'));

        return view('admin.rundown.create', compact('events', 'activeEvent', 'defaultDate'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'tanggal' => ['required', 'date'],
            'waktu_mulai' => ['required', 'date_format:H:i'],
            'waktu_selesai' => ['nullable', 'date_format:H:i', 'after:waktu_mulai'],
            'judul' => ['required', 'string', 'max:150'],
            'lokasi' => ['nullable', 'string', 'max:150'],
            'penanggung_jawab' => ['nullable', 'string', 'max:150'],
            'catatan' => ['nullable', 'string'],
            'dipublikasikan' => ['boolean'],
        ]);

        $validated['dipublikasikan'] = $request->boolean('dipublikasikan');

        $acara = AcaraRundown::create($validated);

        return redirect()->route('admin.rundown.index', ['tanggal' => $acara->tanggal->format('Y-m-d')])
            ->with('success', 'Acara rundown berhasil ditambahkan.');
    }

    public function edit(AcaraRundown $acara_rundown): View
    {
        $events = Event::orderBy('nama')->get();

        return view('admin.rundown.edit', [
            'acara' => $acara_rundown,
            'events' => $events,
        ]);
    }

    public function update(Request $request, AcaraRundown $acara_rundown): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'tanggal' => ['required', 'date'],
            'waktu_mulai' => ['required', 'date_format:H:i'],
            'waktu_selesai' => ['nullable', 'date_format:H:i'],
            'judul' => ['required', 'string', 'max:150'],
            'lokasi' => ['nullable', 'string', 'max:150'],
            'penanggung_jawab' => ['nullable', 'string', 'max:150'],
            'catatan' => ['nullable', 'string'],
            'dipublikasikan' => ['boolean'],
        ]);

        $validated['dipublikasikan'] = $request->boolean('dipublikasikan');

        $acara_rundown->update($validated);

        return redirect()->route('admin.rundown.index', ['tanggal' => $acara_rundown->tanggal->format('Y-m-d')])
            ->with('success', 'Acara rundown berhasil diperbarui.');
    }

    public function destroy(AcaraRundown $acara_rundown): RedirectResponse
    {
        $tanggal = $acara_rundown->tanggal->format('Y-m-d');
        $acara_rundown->delete();

        return redirect()->route('admin.rundown.index', ['tanggal' => $tanggal])
            ->with('success', 'Acara rundown berhasil dihapus.');
    }
}
