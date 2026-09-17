<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lapangan;
use App\Models\Venue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LapanganController extends Controller
{
    public function index(Venue $venue): RedirectResponse
    {
        return redirect()->route('admin.venue.show', $venue);
    }

    public function create(Venue $venue): RedirectResponse
    {
        return redirect()->route('admin.venue.show', $venue);
    }

    public function store(Request $request, Venue $venue): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
        ]);

        $venue->lapangan()->create($validated);

        return redirect()->route('admin.venue.show', $venue)
            ->with('success', "Lapangan '{$validated['nama']}' berhasil ditambahkan.");
    }

    public function update(Request $request, Lapangan $lapangan): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
        ]);

        $lapangan->update($validated);

        return redirect()->route('admin.venue.show', $lapangan->venue_id)
            ->with('success', "Lapangan '{$validated['nama']}' berhasil diperbarui.");
    }

    public function destroy(Lapangan $lapangan): RedirectResponse
    {
        $venueId = $lapangan->venue_id;
        $nama = $lapangan->nama;
        $lapangan->delete();

        return redirect()->route('admin.venue.show', $venueId)
            ->with('success', "Lapangan '{$nama}' berhasil dihapus.");
    }
}
