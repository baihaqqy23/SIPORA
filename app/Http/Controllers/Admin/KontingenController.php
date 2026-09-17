<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Kontingen;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KontingenController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $eventId = $request->get('event_id');

        $query = Kontingen::with(['event', 'user'])
            ->withCount(['atlet', 'timKontingen', 'pendaftaran'])
            ->when($search, function ($q, $search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('kota', 'like', "%{$search}%")
                    ->orWhere('provinsi', 'like', "%{$search}%")
                    ->orWhere('nama_ofisial', 'like', "%{$search}%");
            })
            ->when($status, fn ($q, $status) => $q->where('status', $status))
            ->when($eventId, fn ($q, $id) => $q->where('event_id', $id))
            ->orderBy('nama');

        $kontingens = $query->paginate(15)->withQueryString();
        $events = Event::orderBy('nama')->get();

        $totalKontingen = Kontingen::count();
        $totalDisetujui = Kontingen::where('status', 'disetujui')->count();
        $totalMenunggu = Kontingen::where('status', 'menunggu')->count();

        return view('admin.kontingen.index', compact(
            'kontingens',
            'events',
            'search',
            'status',
            'eventId',
            'totalKontingen',
            'totalDisetujui',
            'totalMenunggu'
        ));
    }

    public function create(): View
    {
        $events = Event::orderBy('nama')->get();
        $activeEvent = Event::whereIn('status', ['berlangsung', 'pendaftaran_dibuka'])->first() ?? $events->first();

        return view('admin.kontingen.create', compact('events', 'activeEvent'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'nama' => ['required', 'string', 'max:255'],
            'provinsi' => ['required', 'string', 'max:100'],
            'kota' => ['required', 'string', 'max:100'],
            'nama_ofisial' => ['required', 'string', 'max:150'],
            'no_hp_ofisial' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:150'],
            'status' => ['required', 'in:menunggu,disetujui,ditolak,nonaktif'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'surat_mandat' => ['nullable', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'create_user' => ['nullable', 'boolean'],
            'username' => ['nullable', 'string', 'max:50', 'unique:users,username'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $validated['slug'] = Str::slug($validated['nama'].'-'.$validated['kota']);

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $request->file('logo')->store('kontingen/logo', 'public');
        }

        if ($request->hasFile('surat_mandat')) {
            $validated['surat_mandat_path'] = $request->file('surat_mandat')->store('kontingen/mandat', 'public');
        }

        $kontingen = Kontingen::create($validated);

        // Optionally create user account for this kontingen
        if ($request->boolean('create_user') && $request->filled('username') && $request->filled('password')) {
            User::create([
                'name' => $kontingen->nama,
                'username' => $request->username,
                'email' => $kontingen->email,
                'password' => Hash::make($request->password),
                'role' => 'kontingen',
                'kontingen_id' => $kontingen->id,
                'is_active' => true,
            ]);
        }

        return redirect()->route('admin.kontingen.show', $kontingen)
            ->with('success', "Kontingen '{$kontingen->nama}' berhasil ditambahkan.");
    }

    public function show(Kontingen $kontingen): View
    {
        $kontingen->load([
            'event',
            'user',
            'atlet.berkas',
            'timKontingen.cabangOlahraga',
            'pendaftaran.nomorLomba.cabangOlahraga',
        ]);

        return view('admin.kontingen.show', compact('kontingen'));
    }

    public function edit(Kontingen $kontingen): View
    {
        $events = Event::orderBy('nama')->get();

        return view('admin.kontingen.edit', compact('kontingen', 'events'));
    }

    public function update(Request $request, Kontingen $kontingen): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'nama' => ['required', 'string', 'max:255'],
            'provinsi' => ['required', 'string', 'max:100'],
            'kota' => ['required', 'string', 'max:100'],
            'nama_ofisial' => ['required', 'string', 'max:150'],
            'no_hp_ofisial' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:150'],
            'status' => ['required', 'in:menunggu,disetujui,ditolak,nonaktif'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'surat_mandat' => ['nullable', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        if ($request->hasFile('logo')) {
            if ($kontingen->logo_path) {
                Storage::disk('public')->delete($kontingen->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('kontingen/logo', 'public');
        }

        if ($request->hasFile('surat_mandat')) {
            if ($kontingen->surat_mandat_path) {
                Storage::disk('public')->delete($kontingen->surat_mandat_path);
            }
            $validated['surat_mandat_path'] = $request->file('surat_mandat')->store('kontingen/mandat', 'public');
        }

        $kontingen->update($validated);

        return redirect()->route('admin.kontingen.show', $kontingen)
            ->with('success', "Kontingen '{$kontingen->nama}' berhasil diperbarui.");
    }

    public function destroy(Kontingen $kontingen): RedirectResponse
    {
        $nama = $kontingen->nama;
        $kontingen->delete();

        return redirect()->route('admin.kontingen.index')
            ->with('success', "Kontingen '{$nama}' berhasil dihapus.");
    }
}
