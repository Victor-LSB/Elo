<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Milestone;
use App\Models\Validacao;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    public function index(Request $request)
    {
        $milestones = Milestone::whereHas('demanda', fn ($q) => $q->where('professor_id', $request->user()->id))
            ->with('demanda.instituicao', 'validacao')
            ->orderBy('prazo')
            ->get();

        return view('professor.milestones.index', ['milestones' => $milestones]);
    }

    /** Professor aprova a entrega de um milestone (lado da dupla validação — RF-05.3). */
    public function validar(Milestone $milestone, Request $request)
    {
        abort_unless($milestone->demanda->professor_id === $request->user()->id, 403);
        abort_unless($milestone->status === 'aguardando_validacao', 422, 'Este milestone não está aguardando validação.');

        $validacao = $milestone->validacao ?? Validacao::create(['milestone_id' => $milestone->id]);
        $validacao->aprovarComoProfessor($request->user());

        return back()->with('status', 'Milestone aprovado. Aguardando a instituição para creditar as horas.');
    }

    /** Professor pede ajustes ao grupo em vez de aprovar direto. */
    public function pedirAjustes(Milestone $milestone, Request $request)
    {
        abort_unless($milestone->demanda->professor_id === $request->user()->id, 403);
        $request->validate(['comentario' => ['required', 'string']]);

        $milestone->update(['status' => 'em_andamento']);
        $validacao = $milestone->validacao ?? Validacao::create(['milestone_id' => $milestone->id]);
        $validacao->update(['comentario' => $request->comentario]);

        return back()->with('status', 'Pedido de ajustes enviado ao grupo.');
    }
}
