<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $events = Event::withCount(['cabangOlahraga', 'kontingen', 'panitia'])
            ->latest('tanggal_mulai')
            ->paginate(15);

        return view('admin.event.index', compact('events'));
    }

    public function create(): View
    {
        return view('admin.event.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'kategori_usia' => ['required', Rule::in(array_keys(Event::daftarKategoriUsia()))],
            'pendaftaran_mulai' => ['required', 'date'],
            'pendaftaran_selesai' => ['required', 'date', 'after:pendaftaran_mulai'],
            'maks_nomor_lomba_per_atlet' => ['nullable', 'integer', 'min:1', 'max:10'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', Rule::in(['draft', 'pendaftaran_dibuka', 'pendaftaran_ditutup', 'berlangsung', 'selesai'])],
        ]);

        $data['slug'] = Str::slug($data['nama']);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('event/logo', 'public');
        }

        $event = Event::create($data);

        return redirect()->route('admin.event.show', $event)
            ->with('success', 'Event berhasil dibuat.');
    }

    public function show(Event $event): View
    {
        $event->loadCount(['cabangOlahraga', 'kontingen', 'panitia', 'venues']);
        $event->load(['cabangOlahraga', 'kontingen', 'venues']);

        return view('admin.event.show', compact('event'));
    }

    public function edit(Event $event): View
    {
        return view('admin.event.edit', compact('event'));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'kategori_usia' => ['required', Rule::in(array_keys(Event::daftarKategoriUsia()))],
            'pendaftaran_mulai' => ['required', 'date'],
            'pendaftaran_selesai' => ['required', 'date'],
            'maks_nomor_lomba_per_atlet' => ['nullable', 'integer', 'min:1', 'max:10'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', Rule::in(['draft', 'pendaftaran_dibuka', 'pendaftaran_ditutup', 'berlangsung', 'selesai'])],
        ]);

        if ($request->hasFile('logo')) {
            if ($event->logo_path) {
                Storage::disk('public')->delete($event->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('event/logo', 'public');
        }

        $event->update($data);

        return redirect()->route('admin.event.show', $event)
            ->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $nama = $event->nama;
        $event->delete();

        return redirect()->route('admin.event.index')
            ->with('success', "Event '{$nama}' berhasil dihapus.");
    }

    public function aktivasi(Request $request, Event $event): RedirectResponse
    {
        $request->validate([
            'status' => ['required', Rule::in(['draft', 'pendaftaran_dibuka', 'pendaftaran_ditutup', 'berlangsung', 'selesai'])],
        ]);

        $event->update(['status' => $request->status]);

        return back()->with('success', 'Status event diperbarui menjadi '.$request->status);
    }
}
