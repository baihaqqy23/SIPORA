<?php

namespace App\Http\Controllers\Kontingen;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\NomorLomba;
use App\Models\Pendaftaran;
use App\Services\VerifikasiPendaftaranService;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    public function index()
    {
        $kontingen = auth()->user()->kontingen;
        if (! $kontingen) {
            return redirect()->route('login')->with('error', 'Data kontingen tidak ditemukan.');
        }

        $pendaftaran = Pendaftaran::where('kontingen_id', $kontingen->id)
            ->with(['atlet', 'timKontingen', 'nomorLomba.cabangOlahraga', 'riwayatVerifikasi'])
            ->latest()
            ->paginate(20);

        return view('kontingen.pendaftaran.index', compact('pendaftaran', 'kontingen'));
    }

    public function create(Request $request)
    {
        $event = Event::whereIn('status', ['pendaftaran_dibuka', 'berlangsung'])->latest()->first();

        if (! $event) {
            return back()->with('error', 'Pendaftaran belum dibuka atau sudah ditutup.');
        }

        $nomorLomba = NomorLomba::with('cabangOlahraga')
            ->whereHas('cabangOlahraga', fn ($q) => $q->where('event_id', $event->id))
            ->orderBy('nama')
            ->get();

        $kontingen = auth()->user()->kontingen;
        $atletTersedia = $kontingen ? $kontingen->atlet()->where('status', 'aktif')->get() : collect();

        return view('kontingen.pendaftaran.create', compact('event', 'nomorLomba', 'atletTersedia', 'kontingen'));
    }

    public function store(Request $request, VerifikasiPendaftaranService $verifikasiService)
    {
        $request->validate([
            'nomor_lomba_id' => ['required', 'exists:nomor_lomba,id'],
            'atlet_ids' => ['required', 'array', 'min:1'],
            'atlet_ids.*' => ['exists:atlet,id'],
            'nama_tim' => ['nullable', 'string', 'max:150'],
        ]);

        $kontingen = auth()->user()->kontingen;
        $nomorLomba = NomorLomba::findOrFail($request->nomor_lomba_id);

        // Verifikasi atlet milik kontingen ini
        $atletIds = $request->atlet_ids;
        $atletValid = $kontingen->atlet()->whereIn('id', $atletIds)->count();
        if ($atletValid !== count($atletIds)) {
            return back()->with('error', 'Beberapa atlet tidak valid atau bukan milik kontingen Anda.');
        }

        try {
            $pendaftaran = $verifikasiService->daftarkan(
                kontingen: $kontingen,
                nomorLomba: $nomorLomba,
                atletIds: $atletIds,
                namaTim: $request->nama_tim,
            );
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('kontingen.pendaftaran.index')
            ->with('success', 'Pendaftaran berhasil disubmit dan menunggu verifikasi.');
    }

    public function destroy(Pendaftaran $pendaftaran)
    {
        $kontingen = auth()->user()->kontingen;
        if ($pendaftaran->kontingen_id !== $kontingen->id) {
            abort(403, 'Anda tidak memiliki akses ke data pendaftaran ini.');
        }

        if (! in_array($pendaftaran->status, ['menunggu', 'draft', 'revisi', 'ditolak', 'ditolak_sistem'])) {
            return back()->with('error', 'Pendaftaran yang sudah diverifikasi tidak dapat dibatalkan.');
        }

        $pendaftaran->delete();

        return redirect()->route('kontingen.pendaftaran.index')
            ->with('success', 'Pendaftaran berhasil dibatalkan.');
    }
}
