<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Kontingen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Kredensial yang diberikan tidak cocok dengan data kami.'],
            ]);
        }

        if ($user->status !== 'aktif') {
            throw ValidationException::withMessages([
                'email' => ['Akun Anda berstatus nonaktif atau belum disetujui. Hubungi administrator.'],
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        if ($user->harus_ganti_password) {
            return redirect()->route('ganti-password');
        }

        $roleDashboard = $this->redirectAfterLogin($user);
        $intended = $request->session()->pull('url.intended');

        if ($intended) {
            if ($user->isAdmin() && str_contains($intended, '/admin')) {
                return redirect($intended);
            }
            if ($user->isPjCabor() && str_contains($intended, '/cabor')) {
                return redirect($intended);
            }
            if ($user->isKontingen() && str_contains($intended, '/kontingen')) {
                return redirect($intended);
            }
        }

        return redirect($roleDashboard);
    }

    protected function redirectAfterLogin(User $user): string
    {
        return match ($user->role) {
            'admin' => route('admin.dashboard'),
            'pj_cabor' => route('cabor.dashboard'),
            'kontingen' => route('kontingen.dashboard'),
            default => '/',
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showRegisterKontingen()
    {
        $event = Event::whereIn('status', ['pendaftaran_dibuka', 'berlangsung'])->latest()->first();

        return view('auth.register-kontingen', compact('event'));
    }

    public function registerKontingen(Request $request)
    {
        $request->validate([
            'nama_kontingen' => ['required', 'string', 'max:150'],
            'provinsi' => ['required', 'string', 'max:100'],
            'kota' => ['required', 'string', 'max:100'],
            'nama_ofisial' => ['required', 'string', 'max:150'],
            'no_hp_ofisial' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'unique:users,email', 'unique:kontingen,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'surat_mandat' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'setuju_privasi' => ['required', 'accepted'],
        ], [
            'email.unique' => 'Email ini sudah terdaftar sebagai akun pengguna atau kontingen lain.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
            'setuju_privasi.required' => 'Anda harus menyetujui kebijakan privasi sebelum mendaftar.',
        ]);

        // Ambil event aktif
        $event = Event::whereIn('status', ['pendaftaran_dibuka', 'berlangsung'])->latest()->first();
        if (! $event) {
            return back()->withInput()->with('error', 'Tidak ada event yang sedang membuka pendaftaran saat ini.');
        }

        // Cek duplikasi kontingen pada event yang sama (Unique: event_id, provinsi, kota)
        $existing = Kontingen::where('event_id', $event->id)
            ->where('provinsi', $request->provinsi)
            ->where('kota', $request->kota)
            ->exists();

        if ($existing) {
            return back()->withInput()->withErrors([
                'kota' => "Kontingen untuk daerah '{$request->kota}, {$request->provinsi}' sudah terdaftar pada event ini.",
            ]);
        }

        $suratMandatPath = null;
        if ($request->hasFile('surat_mandat')) {
            $suratMandatPath = $request->file('surat_mandat')->store('surat_mandat', 'private');
        }

        DB::transaction(function () use ($request, $event, $suratMandatPath) {
            $kontingen = Kontingen::create([
                'event_id' => $event->id,
                'nama' => $request->nama_kontingen,
                'slug' => Str::slug($request->nama_kontingen).'-'.Str::random(4),
                'provinsi' => $request->provinsi,
                'kota' => $request->kota,
                'nama_ofisial' => $request->nama_ofisial,
                'no_hp_ofisial' => $request->no_hp_ofisial,
                'email' => $request->email,
                'surat_mandat_path' => $suratMandatPath,
                'status' => 'menunggu',
            ]);

            User::create([
                'name' => $request->nama_ofisial,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'kontingen',
                'kontingen_id' => $kontingen->id,
                'status' => 'aktif',
            ]);
        });

        return redirect()->route('login')
            ->with('success', 'Pendaftaran kontingen berhasil! Akun Anda akan diverifikasi oleh Admin Dispora sebelum jadwal pertandingan dimulai.');
    }

    public function showGantiPassword()
    {
        return view('auth.ganti-password');
    }

    public function gantiPassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
            'harus_ganti_password' => false,
        ]);

        return redirect($this->redirectAfterLogin($user))
            ->with('success', 'Password berhasil diubah. Selamat datang!');
    }
}
