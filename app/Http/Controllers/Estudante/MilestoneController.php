<?php

namespace App\Http\Controllers\Estudante;

use App\Http\Controllers\Controller;
use App\Models\Milestone;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    public function index(Request $request)
    {
        $grupo = $request->user()->grupos()->first();
        $demanda = $grupo?->demandaAtiva();

        return view('estudante.horas.index', [
            'demanda' => $demanda?->load('milestones.validacao'),
            'certificados' => $request->user()->certificados()->with('demanda')->get(),
        ]);
    }

    /** RF-04.2: grupo atualiza status/anexa entrega de um milestone. */
    public function registrarEntrega(Milestone $milestone, Request $request)
    {
        $request->validate(['entrega' => ['required', 'file', 'max:10240']]);

        $grupo = $request->user()->grupos()->first();
        abort_unless(
            $grupo && $milestone->demanda->grupoAtivo()?->id === $grupo->id,
            403
        );

        $path = $request->file('entrega')->store('entregas/milestones');
        $milestone->registrarEntrega($path);

        return back()->with('status', 'Entrega enviada. Aguardando validação do professor e da instituição.');
    }
}
