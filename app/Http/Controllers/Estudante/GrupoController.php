<?php

namespace App\Http\Controllers\Estudante;

use App\Http\Controllers\Controller;
use App\Models\Grupo;
use App\Models\User;
use Illuminate\Http\Request;

class GrupoController extends Controller
{
    public function show(Request $request)
    {
        $grupo = $request->user()->grupos()->with('membros')->first();

        return view('estudante.grupo.show', ['grupo' => $grupo]);
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

    /** Convida outro estudante por matrícula ou e-mail. */
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

        $grupo->membros()->syncWithoutDetaching([$convidado->id]);

        return back()->with('status', "{$convidado->nome} foi adicionado(a) ao grupo.");
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
