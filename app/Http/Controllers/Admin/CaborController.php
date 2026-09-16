<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CabangOlahraga;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CaborController extends Controller
{
    public function index(Request $request)
    {
        $event = Event::whereIn('status', ['pendaftaran_dibuka', 'berlangsung', 'pendaftaran_ditutup'])->latest()->first();

        $query = CabangOlahraga::withCount('nomorLomba')
            ->with('pjUser')
            ->when($request->filled('q'), fn ($q) => $q->where('nama', 'like', '%'.$request->q.'%'));

        if ($event) {
            $query->where('event_id', $event->id);
        }

        $cabors = $query->orderBy('nama')->paginate(20)->withQueryString();

        return view('admin.cabor.index', compact('cabors', 'event'));
    }

    public function create()
    {
        $event = Event::whereIn('status', ['pendaftaran_dibuka', 'berlangsung', 'pendaftaran_ditutup', 'draft'])->latest()->first();
        $pjUsers = User::where('role', 'pj_cabor')->orderBy('name')->get();

        return view('admin.cabor.create', compact('event', 'pjUsers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'kode' => ['required', 'string', 'max:10'],
            'nama' => ['required', 'string', 'max:150'],
            'format_pertandingan' => ['required', Rule::in(['single_elimination', 'round_robin', 'heat', 'scoring'])],
            'jenis' => ['required', Rule::in(['individu', 'tim'])],
            'deskripsi' => ['nullable', 'string'],
            'pj_user_id' => ['nullable', 'exists:users,id'],
        ]);

        $cabor = CabangOlahraga::create($data);

        return redirect()->route('admin.cabor.show', $cabor)
            ->with('success', 'Cabang olahraga berhasil ditambahkan.');
    }

    public function show(CabangOlahraga $cabor)
    {
        $cabor->load(['nomorLomba', 'pjUser']);

        return view('admin.cabor.show', compact('cabor'));
    }

    public function edit(CabangOlahraga $cabor)
    {
        $pjUsers = User::where('role', 'pj_cabor')->orderBy('name')->get();

        return view('admin.cabor.edit', compact('cabor', 'pjUsers'));
    }

    public function update(Request $request, CabangOlahraga $cabor)
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:10'],
            'nama' => ['required', 'string', 'max:150'],
            'format_pertandingan' => ['required', Rule::in(['single_elimination', 'round_robin', 'heat', 'scoring'])],
            'jenis' => ['required', Rule::in(['individu', 'tim'])],
            'deskripsi' => ['nullable', 'string'],
            'pj_user_id' => ['nullable', 'exists:users,id'],
        ]);

        $cabor->update($data);

        return redirect()->route('admin.cabor.show', $cabor)
            ->with('success', 'Cabang olahraga berhasil diperbarui.');
    }

    public function destroy(CabangOlahraga $cabor)
    {
        $cabor->delete();

        return redirect()->route('admin.cabor.index')
            ->with('success', 'Cabang olahraga berhasil dihapus.');
    }
}
