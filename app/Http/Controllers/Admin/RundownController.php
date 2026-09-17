<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcaraRundown;
use App\Models\Event;
use App\Models\Pertandingan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RundownController extends Controller
{
    public function index(Request $request): View
    {
        $activeEvent = Event::whereIn('status', ['berlangsung', 'pendaftaran_dibuka'])->first() ?? Event::latest()->first();
        $selectedEventId = $request->get('event_id', $activeEvent?->id);

        $tanggal = $request->get('tanggal');

        $query = AcaraRundown::with('event')
            ->when($selectedEventId, fn ($q) => $q->where('event_id', $selectedEventId))
            ->when($tanggal, fn ($q) => $q->whereDate('tanggal', $tanggal))
            ->orderBy('tanggal')
            ->orderBy('waktu_mulai');

        $rundownList = $query->paginate(15)->withQueryString();

        $events = Event::orderBy('nama')->get();

        // Get distinct dates for quick navigation
        $availableDates = AcaraRundown::when($selectedEventId, fn ($q) => $q->where('event_id', $selectedEventId))
            ->distinct()
            ->orderBy('tanggal')
            ->pluck('tanggal');

        $totalAcara = AcaraRundown::when($selectedEventId, fn ($q) => $q->where('event_id', $selectedEventId))->count();
        $totalPublikasi = AcaraRundown::when($selectedEventId, fn ($q) => $q->where('event_id', $selectedEventId))
            ->where('dipublikasikan', true)
            ->count();

        return view('admin.rundown.index', compact(
            'rundownList',
            'events',
            'selectedEventId',
            'tanggal',
            'availableDates',
            'totalAcara',
            'totalPublikasi'
        ));
    }

    public function show(string $tanggal, Request $request): View
    {
        $activeEvent = Event::whereIn('status', ['berlangsung', 'pendaftaran_dibuka'])->first() ?? Event::latest()->first();
        $selectedEventId = $request->get('event_id', $activeEvent?->id);

        $parsedDate = Carbon::parse($tanggal);

        $acaraList = AcaraRundown::whereDate('tanggal', $parsedDate)
            ->when($selectedEventId, fn ($q) => $q->where('event_id', $selectedEventId))
            ->orderBy('waktu_mulai')
            ->get();

        $pertandinganList = Pertandingan::with(['nomorLomba.cabangOlahraga', 'lapangan.venue', 'pesertaPertandingan.peserta'])
            ->whereDate('tanggal', $parsedDate)
            ->orderBy('waktu_mulai')
            ->get();

        return view('admin.rundown.show', compact(
            'tanggal',
            'parsedDate',
            'acaraList',
            'pertandinganList',
            'activeEvent'
        ));
    }

    public function exportPdf(string $tanggal, Request $request)
    {
        $parsedDate = Carbon::parse($tanggal);
        $activeEvent = Event::whereIn('status', ['berlangsung', 'pendaftaran_dibuka'])->first() ?? Event::latest()->first();

        $acaraList = AcaraRundown::whereDate('tanggal', $parsedDate)
            ->orderBy('waktu_mulai')
            ->get();

        $pertandinganList = Pertandingan::with(['nomorLomba.cabangOlahraga', 'lapangan.venue'])
            ->whereDate('tanggal', $parsedDate)
            ->orderBy('waktu_mulai')
            ->get();

        return view('admin.rundown.pdf', compact('tanggal', 'parsedDate', 'acaraList', 'pertandinganList', 'activeEvent'));
    }
}
