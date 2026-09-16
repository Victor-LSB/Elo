<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('lembrar'))) {
            return back()->withErrors(['email' => 'E-mail ou senha incorretos.']);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Instituição com cadastro ainda pendente não acessa o painel.
        if ($user->isInstituicao() && ! $user->instituicao->isAtiva()) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Seu cadastro ainda está pendente de validação pela coordenação.',
            ]);
        }

        return redirect()->intended(match ($user->papel) {
            'estudante' => route('estudante.demandas.index'),
            'instituicao' => route('instituicao.demandas.index'),
            'professor' => route('professor.demandas.index'),
            'coordenacao' => route('coordenacao.demandas.index'),
            'admin' => route('admin.excecoes.index'),
        });
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
