<?php

namespace App\Http\Middleware;

use App\Models\Empresa;
use App\Models\Funcionario;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CarregarEmpresaPadrao
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (tenancy()->initialized && Auth::check() && ! Session::has('empresa_id')) {
            $user = Auth::user();
            $funcionario = Funcionario::where('user_id', $user->id)->first();

            if ($funcionario && $funcionario->empresa_id) {
                $empresa = Empresa::where('id', $funcionario->empresa_id)->first();
            } else {
                $empresa = Empresa::first();
            }

            if ($empresa) {
                Session::put('empresa_id', $empresa->id);
                Session::put('nome_empresa', $empresa->entidade?->nome ?? "Empresa {$empresa->id}");
            }
        }

        return $next($request);
    }
}
