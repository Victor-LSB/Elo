<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Uso nas rotas: ->middleware('role:coordenacao') ou 'role:professor,coordenacao'
     * (RNF-02: controle de acesso baseado em papel para todas as rotas).
     */
    public function handle(Request $request, Closure $next, string ...$papeis): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->papel, $papeis, true)) {
            abort(403, 'Você não tem permissão para acessar este recurso.');
        }

        return $next($request);
    }
}
