<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Passa por requisições em domínios centrais (localhost, admin) sem inicializar tenancy.
        InitializeTenancyBySubdomain::$onFail = function ($exception, $request, $next) {
            return $next($request);
        };

        App::setLocale('pt_BR');
        Number::useCurrency('BRL');
    }
}
