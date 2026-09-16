<?php

use App\Http\Middleware\HarusGantiPassword;
use App\Http\Middleware\RoleMiddleware;
use App\Models\Atlet;
use App\Models\NomorLomba;
use App\Models\Pendaftaran;
use App\Models\Pertandingan;
use App\Policies\AtletPolicy;
use App\Policies\NomorLombaPolicy;
use App\Policies\PendaftaranPolicy;
use App\Policies\PertandinganPolicy;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'harus_ganti_password' => HarusGantiPassword::class,
        ]);

        $middleware->web(append: [
            HarusGantiPassword::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->booted(function () {
        Gate::policy(Atlet::class, AtletPolicy::class);
        Gate::policy(Pendaftaran::class, PendaftaranPolicy::class);
        Gate::policy(Pertandingan::class, PertandinganPolicy::class);
        Gate::policy(NomorLomba::class, NomorLombaPolicy::class);
    })->create();
