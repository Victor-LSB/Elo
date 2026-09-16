<?php

namespace App\Notifications;

use App\Models\Candidatura;

/**
 * RF-07.1: notifica os membros do grupo quando o professor responde a
 * candidatura — aprovada, rejeitada ou rejeitada automaticamente porque
 * outro grupo foi aprovado para a mesma demanda.
 */
class CandidaturaRespondida extends NotificacaoElo
{
    public function __construct(public Candidatura $candidatura)
    {
    }

    public function titulo(object $notifiable): string
    {
        return match ($this->candidatura->status) {
            'aprovada' => 'Candidatura aprovada',
            'rejeitada_automatica' => 'Demanda preenchida por outro grupo',
            default => 'Candidatura não aprovada',
        };
    }

    public function mensagem(object $notifiable): string
    {
        $titulo = $this->candidatura->demanda->titulo;

        return match ($this->candidatura->status) {
            'aprovada' => "Seu grupo foi aprovado para a demanda \"{$titulo}\". A execução já pode começar.",
            'rejeitada_automatica' => "A demanda \"{$titulo}\" foi atribuída a outro grupo. Seu grupo está liberado para novas candidaturas.",
            default => "A candidatura do seu grupo para \"{$titulo}\" não foi aprovada. Seu grupo está liberado para novas candidaturas.",
        };
    }

    public function url(object $notifiable): string
    {
        return $this->candidatura->status === 'aprovada'
            ? route('estudante.horas.index', absolute: false)
            : route('estudante.demandas.index', absolute: false);
    }
}
