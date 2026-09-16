<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterEstudanteRequest;
use App\Http\Requests\Auth\RegisterInstituicaoRequest;
use App\Models\Instituicao;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /** RF-01.3 implícito: estudante já pode logar após o cadastro. */
    public function estudante(RegisterEstudanteRequest $request)
    {
        $dados = $request->validated();

        $user = User::create([
            'nome' => $dados['nome'],
            'email' => $dados['email'],
            'password' => Hash::make($dados['password']),
            'papel' => 'estudante',
            'curso' => $dados['curso'],
            'matricula' => $dados['matricula'],
        ]);

        Auth::login($user);

        return redirect()->route('estudante.demandas.index');
    }

    /**
     * RF-01.1: a instituição fica com status "pendente" e só consegue
     * cadastrar demandas depois que a coordenação validar o cadastro.
     */
    public function instituicao(RegisterInstituicaoRequest $request)
    {
        $dados = $request->validated();

        $user = DB::transaction(function () use ($dados) {
            $instituicao = Instituicao::create([
                'nome' => $dados['nome_instituicao'],
                'tipo' => $dados['tipo'],
                'responsavel' => $dados['responsavel'],
                'contato_email' => $dados['email'],
                'contato_telefone' => $dados['telefone'],
                'sobre' => $dados['sobre'] ?? null,
                'status' => 'pendente',
            ]);

            return User::create([
                'nome' => $dados['responsavel'],
                'email' => $dados['email'],
                'password' => Hash::make($dados['password']),
                'papel' => 'instituicao',
                'instituicao_id' => $instituicao->id,
            ]);
        });

        // Não faz login automático: cadastro pendente de validação.
        return redirect()->route('cadastro.pendente')->with('user_id', $user->id);
    }
}
