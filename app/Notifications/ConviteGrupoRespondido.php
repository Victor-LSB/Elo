<?php

namespace App\Notifications;

use App\Models\ConviteGrupo;

/** Avisa quem convidou que o convite foi aceito ou recusado. */
class ConviteGrupoRespondido extends NotificacaoElo
{
    public function __construct(public ConviteGrupo $convite)
    {
    }

    public function titulo(object $notifiable): string
    {
        return $this->convite->status === 'aceito' ? 'Convite aceito' : 'Convite recusado';
    }

    public function mensagem(object $notifiable): string
    {
        $estudante = $this->convite->estudante->nome;
        $grupo = $this->convite->grupo->nome;

        return $this->convite->status === 'aceito'
            ? "{$estudante} aceitou o convite e agora faz parte do grupo \"{$grupo}\"."
            : "{$estudante} recusou o convite para o grupo \"{$grupo}\".";
    }

    public function url(object $notifiable): string
    {
        return route('estudante.grupo.show', absolute: false);
    }
}
