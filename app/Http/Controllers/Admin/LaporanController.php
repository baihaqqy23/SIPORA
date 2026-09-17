<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Atlet;
use App\Models\CabangOlahraga;
use App\Models\Event;
use App\Models\Kontingen;
use App\Models\Medali;
use App\Models\Pertandingan;
use App\Services\KlasemenMedaliService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function __construct(
        protected KlasemenMedaliService $klasemenService
    ) {}

    public function index(Request $request): View
    {
        $events = Event::orderBy('nama')->get();
        $activeEvent = Event::whereIn('status', ['berlangsung', 'pendaftaran_dibuka'])->first() ?? $events->first();
        $selectedEventId = $request->get('event_id', $activeEvent?->id);

        $event = Event::find($selectedEventId) ?? $activeEvent;

        $stats = [
            'total_cabor' => CabangOlahraga::when($event, fn ($q) => $q->where('event_id', $event->id))->count(),
            'total_kontingen' => Kontingen::when($event, fn ($q) => $q->where('event_id', $event->id))->count(),
            'total_atlet' => Atlet::whereHas('kontingen', fn ($q) => $q->when($event, fn ($e) => $e->where('event_id', $event->id)))->count(),
            'total_atlet_terverifikasi' => Atlet::where('status', 'terverifikasi')
                ->whereHas('kontingen', fn ($q) => $q->when($event, fn ($e) => $e->where('event_id', $event->id)))->count(),
            'total_pertandingan' => Pertandingan::whereHas('nomorLomba.cabangOlahraga', fn ($q) => $q->when($event, fn ($e) => $e->where('event_id', $event->id)))->count(),
            'pertandingan_selesai' => Pertandingan::where('status', 'selesai')
                ->whereHas('nomorLomba.cabangOlahraga', fn ($q) => $q->when($event, fn ($e) => $e->where('event_id', $event->id)))->count(),
            'total_emas' => Medali::where('jenis', 'emas')
                ->whereHas('nomorLomba.cabangOlahraga', fn ($q) => $q->when($event, fn ($e) => $e->where('event_id', $event->id)))->count(),
            'total_perak' => Medali::where('jenis', 'perak')
                ->whereHas('nomorLomba.cabangOlahraga', fn ($q) => $q->when($event, fn ($e) => $e->where('event_id', $event->id)))->count(),
            'total_perunggu' => Medali::where('jenis', 'perunggu')
                ->whereHas('nomorLomba.cabangOlahraga', fn ($q) => $q->when($event, fn ($e) => $e->where('event_id', $event->id)))->count(),
        ];

        $klasemen = $event ? $this->klasemenService->getKlasemen($event->id) : collect();

        $caborProgress = CabangOlahraga::when($event, fn ($q) => $q->where('event_id', $event->id))
            ->withCount([
                'nomorLomba',
            ])
            ->get();

        return view('admin.laporan.index', compact(
            'events',
            'event',
            'selectedEventId',
            'stats',
            'klasemen',
            'caborProgress'
        ));
    }

    public function export(Request $request)
    {
        $events = Event::orderBy('nama')->get();
        $activeEvent = Event::whereIn('status', ['berlangsung', 'pendaftaran_dibuka'])->first() ?? $events->first();
        $selectedEventId = $request->get('event_id', $activeEvent?->id);

        $event = Event::find($selectedEventId) ?? $activeEvent;

        $stats = [
            'total_cabor' => CabangOlahraga::when($event, fn ($q) => $q->where('event_id', $event->id))->count(),
            'total_kontingen' => Kontingen::when($event, fn ($q) => $q->where('event_id', $event->id))->count(),
            'total_atlet' => Atlet::whereHas('kontingen', fn ($q) => $q->when($event, fn ($e) => $e->where('event_id', $event->id)))->count(),
            'total_pertandingan' => Pertandingan::whereHas('nomorLomba.cabangOlahraga', fn ($q) => $q->when($event, fn ($e) => $e->where('event_id', $event->id)))->count(),
            'pertandingan_selesai' => Pertandingan::where('status', 'selesai')
                ->whereHas('nomorLomba.cabangOlahraga', fn ($q) => $q->when($event, fn ($e) => $e->where('event_id', $event->id)))->count(),
        ];

        $klasemen = $event ? $this->klasemenService->getKlasemen($event->id) : collect();

        return view('admin.laporan.print', compact('event', 'stats', 'klasemen'));
    }
}
