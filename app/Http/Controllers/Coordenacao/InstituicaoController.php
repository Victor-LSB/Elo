<?php

namespace App\Http\Controllers\Coordenacao;

use App\Http\Controllers\Controller;
use App\Models\Instituicao;
use App\Notifications\CadastroInstituicaoAnalisado;

class InstituicaoController extends Controller
{
    public function index()
    {
        return view('coordenacao.instituicoes.index', [
            'pendentes' => Instituicao::where('status', 'pendente')->get(),
            'ativas' => Instituicao::where('status', 'ativa')->get(),
        ]);
    }

    public function validar(Instituicao $instituicao)
    {
        abort_unless($instituicao->isPendente(), 422);
        $instituicao->validar();

        // RF-07.1: a instituição descobre que já pode entrar na plataforma.
        $instituicao->usuario?->notify(new CadastroInstituicaoAnalisado($instituicao->refresh()));

        return back()->with('status', 'Cadastro validado. A instituição já pode cadastrar demandas.');
    }

    public function recusar(Instituicao $instituicao)
    {
        abort_unless($instituicao->isPendente(), 422);
        $instituicao->recusar();

        $instituicao->usuario?->notify(new CadastroInstituicaoAnalisado($instituicao->refresh()));

        return back()->with('status', 'Cadastro recusado.');
    }
}
