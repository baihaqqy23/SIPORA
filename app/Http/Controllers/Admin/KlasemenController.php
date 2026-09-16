<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\KlasemenMedaliService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class KlasemenController extends Controller
{
    public function __construct(private readonly KlasemenMedaliService $klasemenService) {}

    public function index(Request $request)
    {
        $event = Event::whereIn('status', ['berlangsung', 'selesai'])->latest()->first();

        if (! $event) {
            return view('admin.klasemen.index', ['klasemen' => collect(), 'event' => null]);
        }

        $klasemen = $this->klasemenService->getKlasemen($event->id);

        return view('admin.klasemen.index', compact('klasemen', 'event'));
    }

    public function exportPdf(Request $request)
    {
        $event = Event::whereIn('status', ['berlangsung', 'selesai'])->latest()->first();
        $klasemen = $event ? $this->klasemenService->getKlasemen($event->id) : collect();

        $pdf = Pdf::loadView('pdf.klasemen', compact('klasemen', 'event'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('klasemen-medali.pdf');
    }
}
