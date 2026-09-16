<?php

namespace App\Http\Controllers\Instituicao;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instituicao\StoreDemandaRequest;
use App\Models\Auditoria;
use App\Models\Demanda;
use Illuminate\Http\Request;

class DemandaController extends Controller
{
    public function index(Request $request)
    {
        $demandas = $request->user()->instituicao->demandas()->latest()->get();

        return view('instituicao.demandas.index', ['demandas' => $demandas]);
    }

    public function create()
    {
        return view('instituicao.demandas.create');
    }

    /** RF-02.1: instituição cadastra demanda com título, descrição, área e nível estimado. */
    public function store(StoreDemandaRequest $request)
    {
        $demanda = Demanda::create([
            ...$request->validated(),
            'instituicao_id' => $request->user()->instituicao_id,
            'status' => 'pendente_triagem',
        ]);

        Auditoria::registrar($demanda, null, 'pendente_triagem', $request->user());

        return redirect()->route('instituicao.demandas.index')
            ->with('status', 'Demanda enviada para triagem da coordenação.');
    }

    public function show(Demanda $demanda, Request $request)
    {
        $this->autorizarPropria($demanda, $request);

        return view('instituicao.demandas.show', [
            'demanda' => $demanda->load(['milestones.validacao', 'candidaturaAprovada.grupo.membros', 'checklistEtico']),
        ]);
    }

    private function autorizarPropria(Demanda $demanda, Request $request): void
    {
        abort_unless($demanda->instituicao_id === $request->user()->instituicao_id, 403);
    }
}
