<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracao;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ConfiguracaoController extends Controller
{
    public function index()
    {
        return view('admin.configuracoes.index', [
            'valores' => [
                'prazo_candidatura_dias' => Configuracao::get('prazo_candidatura_dias'),
                'dias_ate_em_risco' => Configuracao::get('dias_ate_em_risco'),
                'dias_ate_habilitar_reabertura' => Configuracao::get('dias_ate_habilitar_reabertura'),
                'dias_ate_marcar_parada' => Configuracao::get('dias_ate_marcar_parada'),
            ],
            'professores' => User::where('papel', 'professor')->get(),
        ]);
    }

    public function atualizar(Request $request)
    {
        $dados = $request->validate([
            'prazo_candidatura_dias' => ['required', 'integer', 'min:1'],
            'dias_ate_em_risco' => ['required', 'integer', 'min:1'],
            'dias_ate_habilitar_reabertura' => ['required', 'integer', 'min:1'],
            'dias_ate_marcar_parada' => ['required', 'integer', 'min:1'],
        ]);

        foreach ($dados as $chave => $valor) {
            Configuracao::set($chave, (string) $valor);
        }

        return back()->with('status', 'Parâmetros salvos.');
    }

    /**
     * RF-01.2: professor e coordenação não se autocadastram — a conta é
     * criada aqui pelo admin, que envia um convite por e-mail para
     * definição de senha.
     */
    public function criarUsuario(Request $request)
    {
        $dados = $request->validate([
            'papel' => ['required', 'in:professor,coordenacao'],
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'departamento_id' => ['required_if:papel,professor', 'nullable', 'exists:departamentos,id'],
        ]);

        $user = User::create([
            ...$dados,
            'password' => Hash::make(Str::random(32)), // será redefinida via link de convite
        ]);

        Password::sendResetLink(['email' => $user->email]);

        return back()->with('status', "Conta criada. Um convite foi enviado para {$user->email}.");
    }
}
