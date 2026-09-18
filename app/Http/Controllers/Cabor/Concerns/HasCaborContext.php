<?php

namespace App\Http\Controllers\Cabor\Concerns;

use App\Models\CabangOlahraga;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

trait HasCaborContext
{
    /**
     * Resolusi konteks Event dan Cabor aktif untuk PJ Cabor.
     * Mengatur session secara konsisten dan men-share variabel ke view.
     *
     * @return array{
     *     currentEvent: ?Event,
     *     currentCabor: ?CabangOlahraga,
     *     assignedEvents: Collection,
     *     assignedCabors: Collection
     * }
     */
    protected function resolveCaborContext(Request $request): array
    {
        $user = auth()->user();

        // 1. Ambil seluruh event yang ditugaskan ke user
        $assignedEvents = $user->eventsDitugaskan();

        // Jika user adalah admin dan tidak ada event spesifik, ambil semua event
        if ($user->isAdmin() && $assignedEvents->isEmpty()) {
            $assignedEvents = Event::orderBy('nama')->get();
        }

        // 2. Tentukan Event Aktif
        $reqEventId = $request->get('event_id');
        $sessionEventId = session('pj_cabor_active_event_id');
        $eventId = $reqEventId ?: $sessionEventId;

        $currentEvent = null;
        if ($assignedEvents->isNotEmpty()) {
            $currentEvent = $assignedEvents->firstWhere('id', (int) $eventId)
                ?? $assignedEvents->firstWhere('status', 'berlangsung')
                ?? $assignedEvents->firstWhere('status', 'pendaftaran_dibuka')
                ?? $assignedEvents->first();

            $eventId = $currentEvent?->id;
            session(['pj_cabor_active_event_id' => $eventId]);
        }

        // 3. Ambil seluruh cabor yang ditugaskan pada event aktif
        $assignedCabors = $user->caborDitugaskan($eventId);

        if ($user->isAdmin() && $assignedCabors->isEmpty() && $eventId) {
            $assignedCabors = CabangOlahraga::where('event_id', $eventId)->orderBy('nama')->get();
        }

        // 4. Tentukan Cabor Aktif
        $reqCaborId = $request->get('cabor_id');
        $sessionCaborId = session('pj_cabor_active_cabor_id');
        $caborId = $reqCaborId ?: $sessionCaborId;

        $currentCabor = null;
        if ($assignedCabors->isNotEmpty()) {
            $currentCabor = $assignedCabors->firstWhere('id', (int) $caborId) ?? $assignedCabors->first();

            // Load relasi standar cabor
            if ($currentCabor && ! $currentCabor->relationLoaded('nomorLomba')) {
                $currentCabor->load(['nomorLomba', 'venues.lapangan', 'event']);
            }

            session(['pj_cabor_active_cabor_id' => $currentCabor?->id]);
        }

        $context = [
            'currentEvent' => $currentEvent,
            'currentCabor' => $currentCabor,
            'assignedEvents' => $assignedEvents,
            'assignedCabors' => $assignedCabors,
        ];

        // Share ke view secara otomatis agar layout & navigasi selalu mendapatkan data konteks
        view()->share('currentEvent', $currentEvent);
        view()->share('currentCabor', $currentCabor);
        view()->share('assignedEvents', $assignedEvents);
        view()->share('assignedCabors', $assignedCabors);

        return $context;
    }
}
