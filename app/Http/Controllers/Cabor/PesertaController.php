<?php

namespace App\Http\Controllers\Cabor;

use App\Http\Controllers\Controller;
use App\Models\CabangOlahraga;
use App\Models\Notifikasi;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PesertaController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $assignedCabor = $user->caborDitugaskan();

        if ($user->isAdmin() && $assignedCabor->isEmpty()) {
            $assignedCabor = CabangOlahraga::all();
        }

        $caborId = $request->get('cabor_id', $assignedCabor->first()?->id);
        $cabor = CabangOlahraga::with('nomorLomba')->find($caborId) ?? $assignedCabor->first();

        if (! $cabor) {
            return view('cabor.no-cabor');
        }

        $nomorLombaIds = $cabor->nomorLomba->pluck('id');

        $query = Pendaftaran::with(['atlet.kontingen', 'nomorLomba', 'timKontingen.kontingen'])
            ->whereIn('nomor_lomba_id', $nomorLombaIds);

        if ($request->filled('nomor_lomba_id')) {
            $query->where('nomor_lomba_id', $request->nomor_lomba_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->whereHas('atlet', function ($qa) use ($search) {
                    $qa->where('nama', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%")
                        ->orWhereHas('kontingen', function ($qk) use ($search) {
                            $qk->where('nama', 'like', "%{$search}%");
                        });
                })->orWhereHas('timKontingen', function ($qt) use ($search) {
                    $qt->where('nama_tim', 'like', "%{$search}%")
                        ->orWhereHas('kontingen', function ($qk) use ($search) {
                            $qk->where('nama', 'like', "%{$search}%");
                        });
                });
            });
        }

        $pendaftaran = $query->latest()->paginate(15)->withQueryString();

        return view('cabor.peserta.index', compact('cabor', 'assignedCabor', 'pendaftaran'));
    }

    public function verifikasiShow(Pendaftaran $pendaftaran)
    {
        $pendaftaran->load(['atlet.kontingen', 'atlet.berkas', 'nomorLomba.cabangOlahraga', 'timKontingen.anggota.atlet.berkas']);

        return view('cabor.peserta.verifikasi', compact('pendaftaran'));
    }

    public function verifikasiProses(Request $request, Pendaftaran $pendaftaran)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['disetujui', 'ditolak'])],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        $pendaftaran->update([
            'status' => $validated['status'],
            'catatan' => $validated['catatan'] ?? null,
            'diverifikasi_pada' => now(),
            'diverifikasi_oleh' => auth()->id(),
        ]);

        // Send notification to kontingen user
        $kontingen = $pendaftaran->atlet?->kontingen ?? $pendaftaran->timKontingen?->kontingen;
        if ($kontingen && $kontingen->user) {
            $statusText = $validated['status'] === 'disetujui' ? 'Diverifikasi Absah (Disetujui)' : 'Ditolak / Belum Memenuhi Syarat';
            $namaPeserta = $pendaftaran->atlet?->nama ?? $pendaftaran->timKontingen?->nama_tim;

            Notifikasi::create([
                'user_id' => $kontingen->user->id,
                'tipe' => $validated['status'] === 'disetujui' ? 'sukses' : 'peringatan',
                'judul' => "Status Verifikasi: {$namaPeserta}",
                'pesan' => "Pendaftaran untuk nomor lomba {$pendaftaran->nomorLomba?->nama} telah berstatus {$statusText}. ".($validated['catatan'] ? "Catatan: {$validated['catatan']}" : ''),
                'url_tujuan' => route('kontingen.pendaftaran.index'),
            ]);
        }

        return redirect()->route('cabor.peserta.index')
            ->with('success', "Pendaftaran peserta berhasil diperbarui menjadi {$validated['status']}.");
    }
}
