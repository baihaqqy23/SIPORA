<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HarusGantiPassword
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->harus_ganti_password) {
            if (! $request->is('ganti-password*') && ! $request->is('logout')) {
                return redirect()->route('ganti-password')
                    ->with('warning', 'Anda wajib mengganti password sebelum melanjutkan.');
            }
        }

        return $next($request);
    }
}
