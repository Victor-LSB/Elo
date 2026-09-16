<?php

namespace App\Notifications;

use App\Models\Candidatura;

/**
 * RF-07.1: avisa o professor responsável de que um grupo se candidatou à
 * demanda dele e aguarda aprovação ou rejeição (RF-03.3).
 */
class NovaCandidatura extends NotificacaoElo
{
    public function __construct(public Candidatura $candidatura)
    {
    }

    public function titulo(object $notifiable): string
    {
        return 'Nova candidatura recebida';
    }

    public function mensagem(object $notifiable): string
    {
        $grupo = $this->candidatura->grupo->nome;
        $demanda = $this->candidatura->demanda->titulo;

        return "O grupo \"{$grupo}\" se candidatou à demanda \"{$demanda}\" e aguarda sua análise.";
    }

    public function url(object $notifiable): string
    {
        return route('professor.candidaturas.show', $this->candidatura->demanda, absolute: false);
    }
}
