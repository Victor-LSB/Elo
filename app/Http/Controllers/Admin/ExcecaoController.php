<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auditoria;
use App\Models\Milestone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExcecaoController extends Controller
{
    public function index()
    {
        return view('admin.excecoes.index', [
            'milestonesEmRisco' => Milestone::where('status', 'em_risco')->with('demanda.instituicao')->get(),
        ]);
    }

    public function show(Milestone $milestone)
    {
        return view('admin.excecoes.show', ['milestone' => $milestone->load('demanda.instituicao', 'demanda.grupoAtivo.membros')]);
    }

    /**
     * RF-04.4: se o milestone ficar "em risco" por período adicional sem
     * resposta, o admin pode:
     *  - reabrir o milestone remanescente para outro grupo, preservando as
     *    horas já creditadas ao grupo original;
     *  - dar mais prazo ao grupo atual;
     *  - marcar a demanda como abandonada.
     */
    public function resolver(Milestone $milestone, Request $request)
    {
        $request->validate([
            'decisao' => ['required', 'in:reabrir,mais_prazo,abandonar'],
            'observacao' => ['nullable', 'string'],
        ]);

        $demanda = $milestone->demanda;
        $statusAnterior = $demanda->status;

        DB::transaction(function () use ($request, $milestone, $demanda) {
            match ($request->decisao) {
                'reabrir' => $this->reabrirParaOutroGrupo($milestone, $demanda),
                'mais_prazo' => $milestone->update(['status' => 'em_andamento', 'prazo' => now()->addDays(7)]),
                'abandonar' => $demanda->update(['status' => 'abandonada']),
            };
        });

        Auditoria::registrar($demanda->refresh(), $statusAnterior, $demanda->status, $request->user(), $request->observacao);

        return redirect()->route('admin.excecoes.index')->with('status', 'Decisão registrada.');
    }

    private function reabrirParaOutroGrupo(Milestone $milestone, $demanda): void
    {
        // As horas do grupo original (milestones já concluídos) NÃO são
        // alteradas — só o milestone remanescente e os seguintes voltam a
        // ficar disponíveis para candidatura de um novo grupo.
        $milestone->update(['status' => 'abandonado']);

        $demanda->milestones()
            ->where('ordem', '>=', $milestone->ordem)
            ->where('id', '!=', $milestone->id)
            ->update(['status' => 'nao_iniciado']);

        // A candidatura aprovada anterior deixa de valer; a demanda volta a
        // aceitar novas candidaturas para o trabalho remanescente.
        $demanda->candidaturaAprovada?->update(['status' => 'rejeitada']);
        $demanda->update(['status' => 'reaberta', 'data_abertura_candidatura' => now()]);
    }
}
