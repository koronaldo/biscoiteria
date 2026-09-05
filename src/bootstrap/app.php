<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Garante que o tenant seja inicializado em TODAS as rotas web,
        // inclusive nas requisições internas do Livewire (/livewire/update).
        // Para domínios centrais (localhost, 127.0.0.1) o middleware é ignorado
        // automaticamente (ver InitializeTenancyBySubdomain::$onFail no AppServiceProvider).
        $middleware->web(append: [
            \Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (
            \Stancl\Tenancy\Contracts\TenantCouldNotBeIdentifiedException $e,
            Request $request
        ) {
            return response()->view('errors.tenant-not-found', [
                'host' => $request->getHost(),
            ], 404);
        });
    })->create();
