<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Services\VerifikasiPendaftaranService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VerifikasiController extends Controller
{
    public function __construct(private readonly VerifikasiPendaftaranService $service) {}

    public function index(Request $request)
    {
        $query = Pendaftaran::with([
            'atlet',
            'nomorLomba.cabangOlahraga',
            'kontingen',
            'riwayatVerifikasi',
        ])->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('cabor'), fn ($q) => $q->whereHas('nomorLomba', fn ($q2) => $q2->where('cabang_olahraga_id', $request->cabor)))
            ->when($request->filled('q'), fn ($q) => $q->whereHas('atlet', fn ($q2) => $q2->where('nama', 'like', '%'.$request->q.'%')));

        // Default: tampilkan yang perlu aksi admin
        if (! $request->filled('status')) {
            $query->whereIn('status', ['menunggu', 'diverifikasi_cabor']);
        }

        $pendaftaran = $query->latest()->paginate(25)->withQueryString();

        $counts = [
            'menunggu' => Pendaftaran::where('status', 'menunggu')->count(),
            'diverifikasi_cabor' => Pendaftaran::where('status', 'diverifikasi_cabor')->count(),
            'disetujui' => Pendaftaran::where('status', 'disetujui')->count(),
            'ditolak' => Pendaftaran::whereIn('status', ['ditolak', 'ditolak_sistem'])->count(),
        ];

        return view('admin.verifikasi.index', compact('pendaftaran', 'counts'));
    }

    public function show(Pendaftaran $pendaftaran)
    {
        $pendaftaran->load([
            'atlet.berkasAtlet',
            'nomorLomba.cabangOlahraga',
            'kontingen',
            'riwayatVerifikasi.verifikator',
        ]);

        $validasiOtomatis = $this->service->validasiOtomatis($pendaftaran);

        return view('admin.verifikasi.show', compact('pendaftaran', 'validasiOtomatis'));
    }

    public function proses(Request $request, Pendaftaran $pendaftaran)
    {
        $request->validate([
            'aksi' => ['required', Rule::in(['setujui', 'tolak', 'minta_perbaikan'])],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            match ($request->aksi) {
                'setujui' => $this->service->setujuiOlehAdmin($pendaftaran, $request->catatan),
                'tolak' => $this->service->tolak($pendaftaran, $request->catatan, auth()->user()),
                'minta_perbaikan' => $this->service->mintaPerbaikan($pendaftaran, $request->catatan, auth()->user()),
            };
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.verifikasi.index')
            ->with('success', 'Keputusan verifikasi berhasil disimpan.');
    }

    public function massal(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:pendaftaran,id'],
            'aksi' => ['required', Rule::in(['setujui', 'tolak'])],
            'catatan' => ['nullable', 'string'],
        ]);

        $pendaftaran = Pendaftaran::whereIn('id', $request->ids)->get();
        $berhasil = 0;

        foreach ($pendaftaran as $p) {
            try {
                match ($request->aksi) {
                    'setujui' => $this->service->setujuiOlehAdmin($p, $request->catatan),
                    'tolak' => $this->service->tolak($p, $request->catatan, auth()->user()),
                };
                $berhasil++;
            } catch (\RuntimeException) {
                // Skip yang gagal
            }
        }

        return back()->with('success', "Berhasil memproses {$berhasil} pendaftaran.");
    }
}
