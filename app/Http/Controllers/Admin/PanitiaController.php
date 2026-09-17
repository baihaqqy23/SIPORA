<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CabangOlahraga;
use App\Models\Event;
use App\Models\Panitia;
use App\Models\PenugasanPanitia;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PanitiaController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $eventId = $request->get('event_id');

        $query = Panitia::with(['event', 'user', 'penugasan.cabangOlahraga'])
            ->withCount('penugasan')
            ->when($search, function ($q, $search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('jabatan', 'like', "%{$search}%")
                    ->orWhere('instansi', 'like', "%{$search}%");
            })
            ->when($eventId, fn ($q, $id) => $q->where('event_id', $id))
            ->orderBy('nama');

        $panitiaList = $query->paginate(15)->withQueryString();
        $events = Event::orderBy('nama')->get();

        $totalPanitia = Panitia::count();
        $totalPjCabor = PenugasanPanitia::where('peran', 'pj_cabor')->count();
        $totalWasitJuri = PenugasanPanitia::whereIn('peran', ['wasit', 'juri'])->count();

        return view('admin.panitia.index', compact(
            'panitiaList',
            'events',
            'search',
            'eventId',
            'totalPanitia',
            'totalPjCabor',
            'totalWasitJuri'
        ));
    }

    public function create(): View
    {
        $events = Event::orderBy('nama')->get();
        $activeEvent = Event::whereIn('status', ['berlangsung', 'pendaftaran_dibuka'])->first() ?? $events->first();
        $caborList = CabangOlahraga::orderBy('nama')->get();

        return view('admin.panitia.create', compact('events', 'activeEvent', 'caborList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'nama' => ['required', 'string', 'max:255'],
            'jabatan' => ['required', 'string', 'max:100'],
            'instansi' => ['nullable', 'string', 'max:150'],
            'no_hp' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'foto' => ['nullable', 'image', 'max:2048'],
            'create_user' => ['nullable', 'boolean'],
            'user_email' => ['nullable', 'required_if:create_user,1', 'email', 'max:150', 'unique:users,email'],
            'password' => ['nullable', 'required_if:create_user,1', 'string', 'min:8', 'confirmed'],
            'role' => ['nullable', 'in:admin,pj_cabor'],
            'harus_ganti_password' => ['nullable', 'boolean'],
            'penugasan_cabor_id' => ['required', 'exists:cabang_olahraga,id'],
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto_path'] = $request->file('foto')->store('panitia/foto', 'public');
        }

        $panitia = Panitia::create($validated);

        // Optionally create user account for panitia / PJ cabor
        if ($request->boolean('create_user') && $request->filled('user_email') && $request->filled('password')) {
            $userRole = $request->get('role', 'pj_cabor');
            $user = User::create([
                'name' => $panitia->nama,
                'email' => $request->user_email,
                'password' => Hash::make($request->password),
                'role' => $userRole,
                'panitia_id' => $panitia->id,
                'status' => 'aktif',
                'harus_ganti_password' => $request->boolean('harus_ganti_password'),
            ]);

            $panitia->update([
                'user_id' => $user->id,
                'email' => $panitia->email ?: $request->user_email,
            ]);
        }

        // Create assignment to the selected cabor
        $panitia->penugasan()->create([
            'cabang_olahraga_id' => $validated['penugasan_cabor_id'],
            'peran' => null,
        ]);

        return redirect()->route('admin.panitia.show', $panitia)
            ->with('success', "Data panitia '{$panitia->nama}' berhasil ditambahkan.");
    }

    public function buatAkun(Request $request, Panitia $panitium): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:pj_cabor,admin'],
            'harus_ganti_password' => ['nullable', 'boolean'],
        ]);

        $user = User::create([
            'name' => $panitium->nama,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'panitia_id' => $panitium->id,
            'status' => 'aktif',
            'harus_ganti_password' => $request->boolean('harus_ganti_password'),
        ]);

        $panitium->update([
            'user_id' => $user->id,
            'email' => $panitium->email ?: $validated['email'],
        ]);

        return back()->with('success', "Akun login untuk '{$panitium->nama}' ({$validated['email']}) berhasil dibuat dengan role {$validated['role']}.");
    }

    public function resetPasswordAkun(Request $request, Panitia $panitium): RedirectResponse
    {
        if (! $panitium->user) {
            return back()->with('error', 'Panitia ini belum memiliki akun login.');
        }

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:150', 'unique:users,email,'.$panitium->user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:pj_cabor,admin'],
            'status' => ['required', 'in:aktif,nonaktif'],
            'harus_ganti_password' => ['nullable', 'boolean'],
        ]);

        $dataToUpdate = [
            'email' => $validated['email'],
            'role' => $validated['role'],
            'status' => $validated['status'],
            'harus_ganti_password' => $request->boolean('harus_ganti_password'),
        ];

        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($validated['password']);
        }

        $panitium->user->update($dataToUpdate);

        return back()->with('success', "Informasi akun login untuk '{$panitium->nama}' berhasil diperbarui.");
    }

    public function hapusAkun(Panitia $panitium): RedirectResponse
    {
        if ($panitium->user) {
            $user = $panitium->user;
            $panitium->update(['user_id' => null]);
            $user->delete();
        }

        return back()->with('success', "Akun login untuk '{$panitium->nama}' berhasil dinonaktifkan/dihapus.");
    }

    public function show(Panitia $panitium): View
    {
        $panitium->load([
            'event',
            'user',
            'penugasan.cabangOlahraga',
        ]);

        // Single cabor affiliation: take the first (and should be only) penugasan
        $penugasan = $panitium->penugasan->first();

        return view('admin.panitia.show', [
            'panitia' => $panitium,
            'penugasan' => $penugasan,
        ]);
    }

    public function edit(Panitia $panitium): View
    {
        $events = Event::orderBy('nama')->get();
        $caborList = CabangOlahraga::orderBy('nama')->get();
        $penugasan = $panitium->penugasan()->first();

        return view('admin.panitia.edit', [
            'panitia' => $panitium,
            'events' => $events,
            'caborList' => $caborList,
            'penugasan' => $penugasan,
        ]);
    }

    public function update(Request $request, Panitia $panitium): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'nama' => ['required', 'string', 'max:255'],
            'jabatan' => ['required', 'string', 'max:100'],
            'instansi' => ['nullable', 'string', 'max:150'],
            'no_hp' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'foto' => ['nullable', 'image', 'max:2048'],
            'penugasan_cabor_id' => ['required', 'exists:cabang_olahraga,id'],
        ]);

        if ($request->hasFile('foto')) {
            if ($panitium->foto_path) {
                Storage::disk('public')->delete($panitium->foto_path);
            }
            $validated['foto_path'] = $request->file('foto')->store('panitia/foto', 'public');
        }

        $panitium->update($validated);

        // Sync single cabor affiliation: update or create the one penugasan record
        $penugasan = $panitium->penugasan()->first();
        if ($penugasan) {
            $penugasan->update(['cabang_olahraga_id' => $validated['penugasan_cabor_id']]);
        } else {
            $panitium->penugasan()->create(['cabang_olahraga_id' => $validated['penugasan_cabor_id']]);
        }

        return redirect()->route('admin.panitia.show', $panitium)
            ->with('success', "Data panitia '{$panitium->nama}' berhasil diperbarui.");
    }

    public function destroy(Panitia $panitium): RedirectResponse
    {
        $nama = $panitium->nama;
        $panitium->delete();

        return redirect()->route('admin.panitia.index')
            ->with('success', "Panitia '{$nama}' berhasil dihapus.");
    }
}
