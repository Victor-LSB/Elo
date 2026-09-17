<?php

namespace App\Notifications;

use App\Models\ConviteGrupo;

/** Avisa o estudante que ele recebeu um convite para entrar num grupo. */
class ConviteGrupoRecebido extends NotificacaoElo
{
    public function __construct(public ConviteGrupo $convite)
    {
    }

    public function titulo(object $notifiable): string
    {
        return 'Convite para grupo';
    }

    public function mensagem(object $notifiable): string
    {
        $grupo = $this->convite->grupo->nome;
        $convidadoPor = $this->convite->convidadoPor->nome;

        return "{$convidadoPor} convidou você para o grupo \"{$grupo}\". Aceite ou recuse na página \"Meu grupo\".";
    }

    public function url(object $notifiable): string
    {
        return route('estudante.grupo.show', absolute: false);
    }
}
