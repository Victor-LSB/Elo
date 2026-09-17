<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Auditoria;
use App\Models\Candidatura;
use App\Models\Demanda;
use App\Models\Milestone;
use App\Notifications\CandidaturaRespondida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CandidaturaController extends Controller
{
    public function index(Request $request)
    {
        $demandas = $request->user()->demandasComoProfessor()
            ->with('candidaturas.grupo.membros', 'instituicao')
            ->get();

        return view('professor.demandas.index', ['demandas' => $demandas]);
    }

    /** Página central de candidaturas: todas as pendentes do professor, de todas as demandas. */
    public function candidaturas(Request $request)
    {
        $demandas = $request->user()->demandasComoProfessor()
            ->with(['candidaturas' => function ($q) {
                $q->with('grupo.membros')->latest();
            }, 'instituicao'])
            ->get();

        $pendentes = $demandas->flatMap(fn ($d) => $d->candidaturas->where('status', 'pendente')->map(function ($c) use ($d) {
            $c->setRelation('demanda', $d);

            return $c;
        }));

        $recentes = $demandas->flatMap(fn ($d) => $d->candidaturas->whereIn('status', ['aprovada', 'rejeitada', 'rejeitada_automatica'])->map(function ($c) use ($d) {
            $c->setRelation('demanda', $d);

            return $c;
        }))->sortByDesc('data_resposta')->take(10);

        return view('professor.candidaturas.index', [
            'pendentes' => $pendentes,
            'recentes' => $recentes,
        ]);
    }

    public function show(Demanda $demanda, Request $request)
    {
        $this->autorizarPropria($demanda, $request);

        return view('professor.candidaturas.show', [
            'demanda' => $demanda->load('candidaturas.grupo.membros'),
        ]);
    }

    /**
     * RF-03.3: professor aprova ou rejeita candidaturas. Ao aprovar uma,
     * as demais para a mesma demanda são rejeitadas automaticamente e a
     * demanda passa a "em_execucao". Nível 3 também exige a criação dos
     * milestones neste momento (RF-04.1).
     */
    public function aprovar(Candidatura $candidatura, Request $request)
    {
        $demanda = $candidatura->demanda;
        $this->autorizarPropria($demanda, $request);
        abort_unless($candidatura->status === 'pendente', 422);

        if ($demanda->isNivel3()) {
            $request->validate([
                'milestones' => ['required', 'array', 'min:1'],
                'milestones.*.titulo' => ['required', 'string'],
                'milestones.*.horas_creditadas' => ['required', 'integer', 'min:1'],
                'milestones.*.prazo' => ['required', 'date'],
            ]);
        }

        DB::transaction(function () use ($candidatura, $demanda, $request) {
            $candidatura->update(['status' => 'aprovada', 'data_resposta' => now()]);

            $rejeitadas = $demanda->candidaturas()
                ->where('id', '!=', $candidatura->id)
                ->where('status', 'pendente')
                ->get();

            $demanda->candidaturas()
                ->whereIn('id', $rejeitadas->pluck('id'))
                ->update(['status' => 'rejeitada_automatica', 'data_resposta' => now()]);

            $statusAnterior = $demanda->status;
            $demanda->update(['status' => 'em_execucao']);
            Auditoria::registrar($demanda, $statusAnterior, 'em_execucao', $request->user());

            if ($demanda->isNivel3()) {
                foreach ($request->milestones as $i => $m) {
                    Milestone::create([
                        'demanda_id' => $demanda->id,
                        'ordem' => $i + 1,
                        'titulo' => $m['titulo'],
                        'descricao' => $m['descricao'] ?? null,
                        'horas_creditadas' => $m['horas_creditadas'],
                        'prazo' => $m['prazo'],
                        'status' => $i === 0 ? 'em_andamento' : 'nao_iniciado',
                    ]);
                }
            } else {
                // Nível 1/2: um único "milestone" implícito cobrindo a demanda toda.
                Milestone::create([
                    'demanda_id' => $demanda->id,
                    'ordem' => 1,
                    'titulo' => $demanda->titulo,
                    'horas_creditadas' => $demanda->horas_max,
                    'prazo' => now()->addDays(30),
                    'status' => 'em_andamento',
                ]);
            }

            // RF-07.1: avisa o grupo aprovado e os que foram preteridos —
            // estes últimos ficam liberados para novas candidaturas.
            $candidatura->grupo->notificarMembros(new CandidaturaRespondida($candidatura->refresh()));
            foreach ($rejeitadas as $rejeitada) {
                $rejeitada->grupo->notificarMembros(new CandidaturaRespondida($rejeitada->refresh()));
            }
        });

        return back()->with('status', 'Candidatura aprovada. As demais foram rejeitadas automaticamente.');
    }

    public function rejeitar(Candidatura $candidatura, Request $request)
    {
        $this->autorizarPropria($candidatura->demanda, $request);
        abort_unless($candidatura->status === 'pendente', 422);

        $candidatura->update(['status' => 'rejeitada', 'data_resposta' => now()]);

        // RF-07.1: o grupo fica liberado para novas candidaturas.
        $candidatura->grupo->notificarMembros(new CandidaturaRespondida($candidatura->refresh()));

        return back()->with('status', 'Candidatura rejeitada.');
    }

    private function autorizarPropria(Demanda $demanda, Request $request): void
    {
        abort_unless($demanda->professor_id === $request->user()->id, 403);
    }
}
