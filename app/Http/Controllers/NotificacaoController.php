<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificacaoController extends Controller
{
    /** Marca uma notificação como lida e leva o usuário ao destino dela. */
    public function abrir(Request $request, string $id)
    {
        $notificacao = $request->user()->notifications()->findOrFail($id);
        $notificacao->markAsRead();

        return redirect($notificacao->data['url'] ?? '/');
    }

    public function marcarTodasLidas(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}
