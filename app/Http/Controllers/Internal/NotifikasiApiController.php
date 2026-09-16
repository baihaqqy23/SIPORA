<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;

class NotifikasiApiController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $items = Notifikasi::where('user_id', $user->id)
            ->latest()
            ->take(20)
            ->get(['id', 'judul', 'pesan', 'url_tujuan', 'dibaca_pada', 'created_at']);

        $unread = Notifikasi::where('user_id', $user->id)
            ->whereNull('dibaca_pada')
            ->count();

        return response()->json([
            'items' => $items,
            'unread' => $unread,
        ]);
    }
}
