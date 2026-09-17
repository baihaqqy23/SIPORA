<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CabangOlahraga;
use App\Models\Event;
use App\Models\Lapangan;
use App\Models\Venue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VenueController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $eventId = $request->get('event_id');

        $query = Venue::with(['event', 'lapangan', 'cabangOlahraga'])
            ->withCount('lapangan')
            ->when($search, function ($q, $search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            })
            ->when($eventId, fn ($q, $id) => $q->where('event_id', $id))
            ->orderBy('nama');

        $venues = $query->paginate(10)->withQueryString();
        $events = Event::orderBy('nama')->get();

        $totalVenues = Venue::count();
        $totalLapangan = Lapangan::count();

        return view('admin.venue.index', compact(
            'venues',
            'events',
            'search',
            'eventId',
            'totalVenues',
            'totalLapangan'
        ));
    }

    public function create(): View
    {
        $events = Event::orderBy('nama')->get();
        $activeEvent = Event::whereIn('status', ['berlangsung', 'pendaftaran_dibuka'])->first() ?? $events->first();
        $caborList = CabangOlahraga::orderBy('nama')->get();

        return view('admin.venue.create', compact('events', 'activeEvent', 'caborList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'kapasitas' => ['nullable', 'integer', 'min:0'],
            'jam_operasional_mulai' => ['required', 'date_format:H:i'],
            'jam_operasional_selesai' => ['required', 'date_format:H:i', 'after:jam_operasional_mulai'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'cabor_ids' => ['nullable', 'array'],
            'cabor_ids.*' => ['exists:cabang_olahraga,id'],
            'lapangan_nama' => ['nullable', 'array'],
            'lapangan_nama.*' => ['nullable', 'string', 'max:100'],
        ]);

        $venue = Venue::create($validated);

        if (! empty($validated['cabor_ids'])) {
            $venue->cabangOlahraga()->sync($validated['cabor_ids']);
        }

        if (! empty($validated['lapangan_nama'])) {
            foreach ($validated['lapangan_nama'] as $lapanganName) {
                if (filled($lapanganName)) {
                    $venue->lapangan()->create(['nama' => $lapanganName]);
                }
            }
        }

        return redirect()->route('admin.venue.show', $venue)
            ->with('success', "Venue '{$venue->nama}' berhasil dibuat.");
    }

    public function show(Venue $venue): View
    {
        $venue->load(['event', 'lapangan', 'cabangOlahraga']);

        return view('admin.venue.show', compact('venue'));
    }

    public function edit(Venue $venue): View
    {
        $events = Event::orderBy('nama')->get();
        $caborList = CabangOlahraga::orderBy('nama')->get();
        $selectedCaborIds = $venue->cabangOlahraga()->pluck('cabang_olahraga.id')->toArray();

        return view('admin.venue.edit', compact('venue', 'events', 'caborList', 'selectedCaborIds'));
    }

    public function update(Request $request, Venue $venue): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'kapasitas' => ['nullable', 'integer', 'min:0'],
            'jam_operasional_mulai' => ['required', 'date_format:H:i'],
            'jam_operasional_selesai' => ['required', 'date_format:H:i'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'cabor_ids' => ['nullable', 'array'],
            'cabor_ids.*' => ['exists:cabang_olahraga,id'],
        ]);

        $venue->update($validated);

        $venue->cabangOlahraga()->sync($validated['cabor_ids'] ?? []);

        return redirect()->route('admin.venue.show', $venue)
            ->with('success', "Venue '{$venue->nama}' berhasil diperbarui.");
    }

    public function destroy(Venue $venue): RedirectResponse
    {
        $nama = $venue->nama;
        $venue->delete();

        return redirect()->route('admin.venue.index')
            ->with('success', "Venue '{$nama}' berhasil dihapus.");
    }
}
