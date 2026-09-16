<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;

class PengumumanController extends Controller
{
    public function index()
    {
        $pengumuman = Pengumuman::whereIn('target', ['publik', 'semua'])
            ->where(fn ($q) => $q->whereNull('tayang_mulai')->orWhere('tayang_mulai', '<=', now()))
            ->latest()
            ->paginate(15);

        return view('publik.pengumuman', compact('pengumuman'));
    }
}
