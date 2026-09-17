<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CabangOlahraga;
use App\Models\Event;
use App\Services\KlasemenMedaliService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KlasemenController extends Controller
{
    public function __construct(private readonly KlasemenMedaliService $klasemenService) {}

    public function index(Request $request): View
    {
        $events = Event::orderBy('nama')->get();
        $selectedEventId = $request->get('event_id');
        $caborId = $request->get('cabor_id');

        $event = $selectedEventId
            ? Event::find($selectedEventId)
            : (Event::whereIn('status', ['berlangsung', 'pendaftaran_dibuka'])->first() ?? $events->first());

        $caborList = CabangOlahraga::when($event, fn ($q) => $q->where('event_id', $event->id))->orderBy('nama')->get();

        $klasemen = $event ? $this->klasemenService->getKlasemen($event->id, $caborId, false) : collect();

        $totalEmas = $klasemen->sum('emas');
        $totalPerak = $klasemen->sum('perak');
        $totalPerunggu = $klasemen->sum('perunggu');
        $totalMedali = $totalEmas + $totalPerak + $totalPerunggu;

        return view('admin.klasemen.index', compact(
            'klasemen',
            'event',
            'events',
            'caborList',
            'selectedEventId',
            'caborId',
            'totalEmas',
            'totalPerak',
            'totalPerunggu',
            'totalMedali'
        ));
    }

    public function exportPdf(Request $request)
    {
        $selectedEventId = $request->get('event_id');
        $caborId = $request->get('cabor_id');

        $event = $selectedEventId
            ? Event::find($selectedEventId)
            : (Event::whereIn('status', ['berlangsung', 'pendaftaran_dibuka'])->first() ?? Event::latest()->first());

        $klasemen = $event ? $this->klasemenService->getKlasemen($event->id, $caborId, false) : collect();

        $pdf = Pdf::loadView('pdf.klasemen', compact('klasemen', 'event'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('klasemen-medali-'.($event->slug ?? 'porprov').'.pdf');
    }
}
