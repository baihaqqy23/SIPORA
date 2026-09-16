<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::latest()->paginate(15);

        return view('admin.event.index', compact('events'));
    }

    public function create()
    {
        return view('admin.event.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:200'],
            'singkatan' => ['nullable', 'string', 'max:20'],
            'deskripsi' => ['nullable', 'string'],
            'tuan_rumah' => ['nullable', 'string', 'max:150'],
            'kota' => ['nullable', 'string', 'max:100'],
            'tanggal_mulai_event' => ['required', 'date'],
            'tanggal_selesai_event' => ['required', 'date', 'after_or_equal:tanggal_mulai_event'],
            'tanggal_mulai_pendaftaran' => ['nullable', 'date'],
            'tanggal_tutup_pendaftaran' => ['nullable', 'date'],
            'batas_usia_min' => ['nullable', 'integer', 'min:5'],
            'batas_usia_max' => ['nullable', 'integer', 'max:99'],
            'peraturan_umum' => ['nullable', 'string'],
        ]);

        $data['status'] = 'draft';

        $event = Event::create($data);

        return redirect()->route('admin.event.show', $event)
            ->with('success', 'Event berhasil dibuat.');
    }

    public function show(Event $event)
    {
        $event->loadCount(['cabangOlahraga', 'kontingen', 'pertandingan']);

        return view('admin.event.show', compact('event'));
    }

    public function edit(Event $event)
    {
        return view('admin.event.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:200'],
            'singkatan' => ['nullable', 'string', 'max:20'],
            'deskripsi' => ['nullable', 'string'],
            'tuan_rumah' => ['nullable', 'string', 'max:150'],
            'kota' => ['nullable', 'string', 'max:100'],
            'tanggal_mulai_event' => ['required', 'date'],
            'tanggal_selesai_event' => ['required', 'date', 'after_or_equal:tanggal_mulai_event'],
            'tanggal_mulai_pendaftaran' => ['nullable', 'date'],
            'tanggal_tutup_pendaftaran' => ['nullable', 'date'],
            'batas_usia_min' => ['nullable', 'integer', 'min:5'],
            'batas_usia_max' => ['nullable', 'integer', 'max:99'],
            'peraturan_umum' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(['draft', 'pendaftaran_dibuka', 'pendaftaran_ditutup', 'berlangsung', 'selesai'])],
        ]);

        $event->update($data);

        return redirect()->route('admin.event.show', $event)
            ->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('admin.event.index')
            ->with('success', 'Event berhasil dihapus.');
    }

    /**
     * Transition event to next valid status.
     */
    public function aktivasi(Request $request, Event $event)
    {
        $request->validate([
            'status' => ['required', Rule::in(['draft', 'pendaftaran_dibuka', 'pendaftaran_ditutup', 'berlangsung', 'selesai'])],
        ]);

        $event->update(['status' => $request->status]);

        return back()->with('success', 'Status event diperbarui menjadi '.$request->status);
    }
}
