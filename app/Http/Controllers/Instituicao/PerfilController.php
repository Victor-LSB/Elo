<?php

namespace App\Http\Controllers\Instituicao;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PerfilController extends Controller
{
    public function show(Request $request)
    {
        return view('instituicao.perfil.show', [
            'instituicao' => $request->user()->instituicao,
        ]);
    }

    public function update(Request $request)
    {
        $instituicao = $request->user()->instituicao;

        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'tipo' => ['required', Rule::in(['ong', 'escola_publica', 'associacao_bairro', 'orgao_publico', 'outro'])],
            'responsavel' => ['required', 'string', 'max:255'],
            'contato_email' => ['required', 'email', 'max:255'],
            'contato_telefone' => ['nullable', 'string', 'max:30'],
            'sobre' => ['nullable', 'string'],
        ]);

        $instituicao->update($dados);

        return back()->with('status', 'Dados da instituição atualizados.');
    }
}
