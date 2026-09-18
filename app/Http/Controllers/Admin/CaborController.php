<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CabangOlahraga;
use App\Models\Event;
use App\Models\Venue;
use Illuminate\Http\Request;

class CaborController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::orderBy('nama')->get();
        $eventId = $request->get('event_id');

        $event = null;
        if ($eventId) {
            $event = $events->firstWhere('id', $eventId);
        } else {
            $event = Event::whereIn('status', ['pendaftaran_dibuka', 'berlangsung', 'pendaftaran_ditutup'])->latest()->first() ?? $events->first();
        }

        $query = CabangOlahraga::with(['event', 'venues'])
            ->withCount('nomorLomba')
            ->when($request->filled('q'), fn ($q) => $q->where('nama', 'like', '%'.$request->q.'%'));

        if ($request->has('event_id') && $request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        } elseif ($event) {
            $query->where('event_id', $event->id);
        }

        $cabors = $query->orderBy('nama')->paginate(20)->withQueryString();

        return view('admin.cabor.index', compact('cabors', 'event', 'events'));
    }

    public function create(Request $request)
    {
        $events = Event::orderBy('nama')->get();
        $eventId = $request->get('event_id');
        $event = $eventId ? $events->firstWhere('id', $eventId) : (Event::whereIn('status', ['pendaftaran_dibuka', 'berlangsung', 'pendaftaran_ditutup', 'draft'])->latest()->first() ?? $events->first());
        $venues = Venue::when($event, fn ($q) => $q->where('event_id', $event->id))->orderBy('nama')->get();

        return view('admin.cabor.create', compact('event', 'events', 'venues'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'nama' => ['required', 'string', 'max:150'],
            'singkatan' => ['required', 'string', 'max:10'],
            'warna' => ['nullable', 'string', 'max:7'],
            'deskripsi' => ['nullable', 'string'],
            'durasi_default_menit' => ['nullable', 'integer', 'min:1'],
            'jeda_antar_tanding_menit' => ['nullable', 'integer', 'min:0'],
        ]);

        $venueIds = $request->input('venue_ids', []);

        $cabor = CabangOlahraga::create($data);

        if (! empty($venueIds)) {
            $cabor->venues()->attach($venueIds);
        }

        return redirect()->route('admin.cabor.show', $cabor)
            ->with('success', 'Cabang olahraga berhasil ditambahkan.');
    }

    public function show(CabangOlahraga $cabor)
    {
        $cabor->load(['nomorLomba', 'venues', 'event']);

        return view('admin.cabor.show', compact('cabor'));
    }

    public function edit(CabangOlahraga $cabor)
    {
        $cabor->load('venues');
        $event = $cabor->event;
        $venues = Venue::when($event, fn ($q) => $q->where('event_id', $event->id))->orderBy('nama')->get();

        return view('admin.cabor.edit', compact('cabor', 'venues'));
    }

    public function update(Request $request, CabangOlahraga $cabor)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'singkatan' => ['required', 'string', 'max:10'],
            'warna' => ['nullable', 'string', 'max:7'],
            'deskripsi' => ['nullable', 'string'],
            'durasi_default_menit' => ['nullable', 'integer', 'min:1'],
            'jeda_antar_tanding_menit' => ['nullable', 'integer', 'min:0'],
        ]);

        $cabor->update($data);

        $venueIds = $request->input('venue_ids', []);
        $cabor->venues()->sync($venueIds);

        return redirect()->route('admin.cabor.show', $cabor)
            ->with('success', 'Cabang olahraga berhasil diperbarui.');
    }

    public function destroy(CabangOlahraga $cabor)
    {
        $cabor->delete();

        return redirect()->route('admin.cabor.index')
            ->with('success', 'Cabang olahraga berhasil dihapus.');
    }
}
