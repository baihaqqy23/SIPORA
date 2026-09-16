<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\KlasemenMedaliService;

class KlasemenController extends Controller
{
    public function index(KlasemenMedaliService $klasemenService)
    {
        $event = Event::whereIn('status', ['berlangsung', 'selesai'])->latest()->first();
        $klasemen = $event ? $klasemenService->getKlasemen($event->id) : collect();

        return view('publik.klasemen', compact('klasemen', 'event'));
    }
}
