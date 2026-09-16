<?php

namespace App\Http\Controllers\Coordenacao;

use App\Http\Controllers\Controller;
use App\Http\Requests\Coordenacao\TriagemDemandaRequest;
use App\Models\Auditoria;
use App\Models\ChecklistEtico;
use App\Models\Demanda;
use App\Models\Departamento;
use App\Models\User;
use App\Notifications\DemandaTriada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TriagemController extends Controller
{
    public function index()
    {
        $fila = Demanda::whereIn('status', ['pendente_triagem', 'em_revisao_adicional'])
            ->with('instituicao')
            ->oldest()
            ->get();

        return view('coordenacao.triagem.index', ['fila' => $fila]);
    }

    public function show(Demanda $demanda)
    {
        abort_unless(in_array($demanda->status, ['pendente_triagem', 'em_revisao_adicional']), 404);

        return view('coordenacao.triagem.show', [
            'demanda' => $demanda->load('instituicao'),
            'departamentos' => Departamento::all(),
            'professores' => User::where('papel', 'professor')->get(),
        ]);
    }

    /**
     * RF-02.2/02.3: define nível final, faixa de horas, departamento e
     * professor responsável, e aplica o checklist ético (RF-06) antes de
     * aprovar. Se o checklist sinalizar substituição de serviço
     * profissional, a demanda vai para revisão adicional em vez de abrir
     * direto para candidatura (RF-06.2).
     */
    public function store(TriagemDemandaRequest $request, Demanda $demanda)
    {
        $dados = $request->validated();
        $statusAnterior = $demanda->status;

        DB::transaction(function () use ($dados, $demanda, $request) {
            ChecklistEtico::updateOrCreate(
                ['demanda_id' => $demanda->id],
                [
                    'substitui_servico_profissional' => $dados['substitui_servico_profissional'],
                    'justificativa' => $dados['justificativa'] ?? null,
                    'respondido_por' => $request->user()->id,
                ]
            );

            $demanda->aplicarTriagem($dados, sinalizado: (bool) $dados['substitui_servico_profissional']);
        });

        Auditoria::registrar($demanda->refresh(), $statusAnterior, $demanda->status, $request->user());

        // RF-07.1: instituição sempre é avisada; o professor designado só
        // quando a demanda de fato abriu para candidatura.
        $notificacao = new DemandaTriada($demanda);
        $demanda->instituicao->usuario?->notify($notificacao);
        if ($demanda->status === 'aberta_candidatura') {
            $demanda->professor?->notify($notificacao);
        }

        $mensagem = $demanda->status === 'em_revisao_adicional'
            ? 'Demanda sinalizada para revisão adicional pelo checklist ético.'
            : 'Demanda aprovada e aberta para candidatura.';

        return redirect()->route('coordenacao.triagem.index')->with('status', $mensagem);
    }
}
