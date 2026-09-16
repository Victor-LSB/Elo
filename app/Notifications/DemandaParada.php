<?php

namespace App\Notifications;

use App\Models\Demanda;

/**
 * RF-03.4 + RF-07.1: a demanda passou do prazo de candidatura sem receber
 * nenhuma proposta. Ela continua visível para os estudantes (com indicador
 * de "parada há X dias"), mas a coordenação e a instituição são avisadas
 * para poderem reavaliar escopo, nível ou divulgação.
 */
class DemandaParada extends NotificacaoElo
{
    public function __construct(public Demanda $demanda)
    {
    }

    public function titulo(object $notifiable): string
    {
        return 'Demanda sem candidaturas';
    }

    public function mensagem(object $notifiable): string
    {
        $dias = (int) $this->demanda->data_abertura_candidatura->diffInDays(now());
        $titulo = "\"{$this->demanda->titulo}\"";

        if ($notifiable->isCoordenacao()) {
            return "A demanda {$titulo} está aberta há {$dias} dias sem receber nenhuma candidatura. "
                .'Pode valer revisar o nível, a faixa de horas ou o departamento atribuído.';
        }

        return "A demanda {$titulo} ainda não recebeu candidaturas ({$dias} dias em aberto). "
            .'Ela continua visível para os estudantes — a coordenação foi avisada.';
    }

    public function url(object $notifiable): string
    {
        return $notifiable->isCoordenacao()
            ? route('coordenacao.triagem.index', absolute: false)
            : route('instituicao.demandas.show', $this->demanda, absolute: false);
    }
}
