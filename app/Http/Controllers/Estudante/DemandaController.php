<?php

namespace App\Http\Controllers\Estudante;

use App\Http\Controllers\Controller;
use App\Models\Candidatura;
use App\Models\Demanda;
use App\Notifications\NovaCandidatura;
use Illuminate\Http\Request;

class DemandaController extends Controller
{
    /** RF-03.1: grupos visualizam demandas abertas, filtráveis por área/nível. */
    public function index(Request $request)
    {
        $demandas = Demanda::where('status', 'aberta_candidatura')
            ->when($request->area, fn ($q, $area) => $q->where('area_sugerida', $area))
            ->when($request->nivel, fn ($q, $nivel) => $q->where('nivel_complexidade', $nivel))
            ->with('instituicao')
            ->latest()
            ->get();

        $grupo = $request->user()->grupos()->first();

        return view('estudante.demandas.index', [
            'demandas' => $demandas,
            'grupoBloqueado' => $grupo?->temEngajamentoAtivo() ?? false,
            'demandaAtiva' => $grupo?->demandaAtiva(),
        ]);
    }

    public function show(Demanda $demanda, Request $request)
    {
        abort_unless($demanda->status === 'aberta_candidatura', 404);

        $grupo = $request->user()->grupos()->first();

        return view('estudante.demandas.show', [
            'demanda' => $demanda->load('instituicao'),
            'grupo' => $grupo,
            'bloqueado' => $grupo?->temEngajamentoAtivo() ?? true,
        ]);
    }

    /**
     * RF-03.2: grupo envia candidatura com mensagem/proposta.
     * Aplica a regra de negócio: um grupo só pode ter uma candidatura ou
     * demanda ativa por vez.
     */
    public function candidatar(Demanda $demanda, Request $request)
    {
        $request->validate(['mensagem' => ['required', 'string', 'min:20']]);

        $grupo = $request->user()->grupos()->first();
        abort_unless($grupo, 422, 'Você precisa estar em um grupo para se candidatar.');

        if ($grupo->temEngajamentoAtivo()) {
            return back()->withErrors([
                'grupo' => 'Seu grupo já tem uma candidatura pendente ou demanda em execução. Finalize-a antes de se candidatar a outra.',
            ]);
        }

        abort_unless($demanda->status === 'aberta_candidatura', 422, 'Esta demanda não está mais aberta para candidatura.');

        $candidatura = Candidatura::create([
            'demanda_id' => $demanda->id,
            'grupo_id' => $grupo->id,
            'mensagem' => $request->mensagem,
            'status' => 'pendente',
        ]);

        // RF-07.1: avisa o professor responsável que há candidatura a analisar.
        $demanda->professor?->notify(new NovaCandidatura($candidatura));

        return redirect()->route('estudante.demandas.index')
            ->with('status', 'Candidatura enviada! Você será notificado quando o professor responder.');
    }
}
