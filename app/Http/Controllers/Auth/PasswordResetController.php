<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /** GET /esqueci-senha — formulário para pedir o link de redefinição. */
    public function requestForm()
    {
        return view('auth.esqueci-senha');
    }

    /** POST /esqueci-senha — envia o link de redefinição por e-mail (ou convite do admin). */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        // Não revelamos se o e-mail existe ou não na base — mesma mensagem em ambos os casos.
        Password::sendResetLink($request->only('email'));

        return back()->with('status', 'Se esse e-mail estiver cadastrado, enviamos um link de redefinição de senha.');
    }

    /** GET /redefinir-senha/{token} — formulário para definir a nova senha. */
    public function resetForm(Request $request, string $token)
    {
        return view('auth.redefinir-senha', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    /** POST /redefinir-senha — efetiva a nova senha. */
    public function reset(Request $request)
    {
        $dados = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::reset($dados, function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));
        });

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withErrors(['email' => __($status)])->withInput($request->only('email'));
        }

        return redirect()->route('login')->with('status', 'Senha definida com sucesso. Você já pode entrar.');
    }
}
