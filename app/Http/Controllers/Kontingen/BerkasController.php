<?php

namespace App\Http\Controllers\Kontingen;

use App\Http\Controllers\Controller;
use App\Models\BerkasAtlet;
use Illuminate\Support\Facades\Storage;

class BerkasController extends Controller
{
    public function download(BerkasAtlet $berkas)
    {
        // Pastikan hanya kontingen pemilik atau admin
        $user = auth()->user();

        $isOwner = $berkas->atlet->kontingen_id === $user->kontingen_id;
        $isAdmin = $user->role === 'admin';

        abort_unless($isOwner || $isAdmin, 403);

        if (! Storage::disk('private')->exists($berkas->path)) {
            abort(404, 'Berkas tidak ditemukan.');
        }

        return Storage::disk('private')->download($berkas->path, $berkas->nama_berkas);
    }
}
