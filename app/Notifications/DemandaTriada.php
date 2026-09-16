<?php

namespace App\Notifications;

use App\Models\Demanda;

/**
 * RF-07.1: avisa a instituição e o professor designado quando a coordenação
 * conclui a triagem — seja abrindo a demanda para candidatura, seja
 * sinalizando-a para revisão adicional pelo checklist ético (RF-06.2).
 */
class DemandaTriada extends NotificacaoElo
{
    public function __construct(public Demanda $demanda)
    {
    }

    public function titulo(object $notifiable): string
    {
        if ($this->demanda->status === 'em_revisao_adicional') {
            return 'Demanda sinalizada para revisão';
        }

        return $notifiable->isProfessor()
            ? 'Nova demanda atribuída a você'
            : 'Demanda aprovada na triagem';
    }

    public function mensagem(object $notifiable): string
    {
        $titulo = "\"{$this->demanda->titulo}\"";

        if ($this->demanda->status === 'em_revisao_adicional') {
            return "A demanda {$titulo} foi sinalizada no checklist ético e passará por revisão adicional antes de ser publicada.";
        }

        if ($notifiable->isProfessor()) {
            return "A coordenação atribuiu a demanda {$titulo} a você. Ela está aberta para candidatura de grupos — "
                .'você será avisado quando receber candidaturas.';
        }

        return "A demanda {$titulo} passou pela triagem e está aberta para candidatura de grupos de estudantes.";
    }

    public function url(object $notifiable): string
    {
        return $notifiable->isProfessor()
            ? route('professor.demandas.index', absolute: false)
            : route('instituicao.demandas.show', $this->demanda, absolute: false);
    }
}
