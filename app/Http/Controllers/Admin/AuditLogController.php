<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $userId = $request->get('user_id');
        $aksi = $request->get('aksi');

        $query = AuditLog::with('user')
            ->when($search, function ($q, $search) {
                $q->where('aksi', 'like', "%{$search}%")
                    ->orWhere('model_type', 'like', "%{$search}%")
                    ->orWhere('ip', 'like', "%{$search}%");
            })
            ->when($userId, fn ($q, $id) => $q->where('user_id', $id))
            ->when($aksi, fn ($q, $a) => $q->where('aksi', $a))
            ->orderByDesc('created_at');

        $logs = $query->paginate(25)->withQueryString();
        $users = User::orderBy('name')->get();

        $totalLogs = AuditLog::count();
        $totalAktivitasHariIni = AuditLog::whereDate('created_at', today())->count();

        return view('admin.audit-log.index', compact(
            'logs',
            'users',
            'search',
            'userId',
            'aksi',
            'totalLogs',
            'totalAktivitasHariIni'
        ));
    }
}
