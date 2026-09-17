<?php

namespace App\Http\Controllers\Estudante;

use App\Http\Controllers\Controller;
use App\Models\ConviteGrupo;
use App\Models\Grupo;
use App\Models\User;
use App\Notifications\ConviteGrupoRecebido;
use App\Notifications\ConviteGrupoRespondido;
use Illuminate\Http\Request;

class GrupoController extends Controller
{
    public function show(Request $request)
    {
        $grupo = $request->user()->grupos()->with('membros')->first();

        $convitesRecebidos = ConviteGrupo::where('estudante_id', $request->user()->id)
            ->where('status', 'pendente')
            ->with(['grupo.membros', 'convidadoPor'])
            ->latest()
            ->get();

        $convitesEnviados = $grupo
            ? ConviteGrupo::where('grupo_id', $grupo->id)
                ->where('status', 'pendente')
                ->with('estudante')
                ->latest()
                ->get()
            : collect();

        return view('estudante.grupo.show', [
            'grupo' => $grupo,
            'convitesRecebidos' => $convitesRecebidos,
            'convitesEnviados' => $convitesEnviados,
        ]);
    }

    /** Cria um grupo novo com o estudante logado como criador e primeiro membro. */
    public function store(Request $request)
    {
        $request->validate(['nome' => ['required', 'string', 'max:100']]);

        abort_if($request->user()->grupos()->exists(), 422, 'Você já faz parte de um grupo.');

        $grupo = Grupo::create([
            'nome' => $request->nome,
            'criado_por' => $request->user()->id,
        ]);
        $grupo->membros()->attach($request->user()->id);

        return redirect()->route('estudante.grupo.show');
    }

    /** Convida outro estudante por matrícula ou e-mail — o convite precisa ser aceito. */
    public function convidar(Request $request)
    {
        $request->validate(['identificador' => ['required', 'string']]);

        $grupo = $request->user()->grupos()->first();
        abort_unless($grupo, 404);

        $convidado = User::where('papel', 'estudante')
            ->where(fn ($q) => $q->where('email', $request->identificador)->orWhere('matricula', $request->identificador))
            ->first();

        if (! $convidado) {
            return back()->withErrors([
                'identificador' => 'Nenhum estudante encontrado com esse e-mail ou matrícula.',
            ]);
        }

        if ($grupo->membros->contains($convidado->id)) {
            return back()->withErrors([
                'identificador' => "{$convidado->nome} já faz parte do grupo.",
            ]);
        }

        if ($convidado->grupos()->exists()) {
            return back()->withErrors([
                'identificador' => "{$convidado->nome} já faz parte de outro grupo.",
            ]);
        }

        $convitePendente = ConviteGrupo::where('grupo_id', $grupo->id)
            ->where('estudante_id', $convidado->id)
            ->where('status', 'pendente')
            ->exists();

        if ($convitePendente) {
            return back()->withErrors([
                'identificador' => "{$convidado->nome} já foi convidado(a) e ainda não respondeu.",
            ]);
        }

        $convite = ConviteGrupo::create([
            'grupo_id' => $grupo->id,
            'estudante_id' => $convidado->id,
            'convidado_por' => $request->user()->id,
            'status' => 'pendente',
        ]);

        $convidado->notify(new ConviteGrupoRecebido($convite));

        return back()->with('status', "Convite enviado para {$convidado->nome}. Assim que aceitar, ele(a) entra no grupo.");
    }

    /** O estudante convidado aceita o convite e entra no grupo. */
    public function aceitarConvite(ConviteGrupo $convite, Request $request)
    {
        abort_unless($convite->estudante_id === $request->user()->id, 403);
        abort_unless($convite->isPendente(), 422);

        if ($request->user()->grupos()->exists()) {
            return back()->withErrors(['convite' => 'Você já faz parte de um grupo. Saia dele antes de aceitar outro convite.']);
        }

        $convite->aceitar();
        $convite->convidadoPor->notify(new ConviteGrupoRespondido($convite->refresh()));

        return redirect()->route('estudante.grupo.show')->with('status', "Você entrou no grupo \"{$convite->grupo->nome}\".");
    }

    /** O estudante convidado recusa o convite. */
    public function recusarConvite(ConviteGrupo $convite, Request $request)
    {
        abort_unless($convite->estudante_id === $request->user()->id, 403);
        abort_unless($convite->isPendente(), 422);

        $convite->recusar();
        $convite->convidadoPor->notify(new ConviteGrupoRespondido($convite->refresh()));

        return back()->with('status', 'Convite recusado.');
    }

    /** Sai do grupo — não afeta horas já creditadas (certificados já emitidos). */
    public function saidaDoGrupo(Request $request)
    {
        $grupo = $request->user()->grupos()->first();
        abort_unless($grupo, 404);

        $grupo->membros()->detach($request->user()->id);

        return redirect()->route('estudante.demandas.index');
    }
}
