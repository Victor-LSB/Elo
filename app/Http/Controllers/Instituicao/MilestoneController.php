<?php

namespace App\Http\Controllers\Instituicao;

use App\Http\Controllers\Controller;
use App\Models\Milestone;
use App\Models\Validacao;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    /** Instituição aprova a entrega de um milestone (lado da dupla validação — RF-05.3). */
    public function validar(Milestone $milestone, Request $request)
    {
        abort_unless(
            $milestone->demanda->instituicao_id === $request->user()->instituicao_id,
            403
        );
        abort_unless($milestone->status === 'aguardando_validacao', 422, 'Este milestone não está aguardando validação.');

        $validacao = $milestone->validacao ?? Validacao::create(['milestone_id' => $milestone->id]);
        $validacao->aprovarComoInstituicao();

        return back()->with('status', 'Entrega validada pela instituição.');
    }
}
