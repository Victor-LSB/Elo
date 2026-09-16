<?php

namespace App\Http\Controllers\Coordenacao;

use App\Http\Controllers\Controller;
use App\Models\User;

class ProfessorController extends Controller
{
    public function index()
    {
        $professores = User::where('papel', 'professor')
            ->with('departamento')
            ->withCount([
                'demandasComoProfessor as demandas_ativas_count' => function ($query) {
                    $query->whereIn('status', ['aberta_candidatura', 'em_execucao']);
                },
                'demandasComoProfessor as demandas_total_count',
            ])
            ->orderBy('nome')
            ->get();

        return view('coordenacao.professores.index', ['professores' => $professores]);
    }
}
