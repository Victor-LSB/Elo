<?php

namespace App\Notifications;

use App\Models\Milestone;

/**
 * RF-07.1 + RF-05.3/05.4: avisa quando um milestone é concluído — ou seja,
 * quando a dupla validação (professor e instituição) se completa e as horas
 * passam a contar para o grupo. Também é usada para avisar a contraparte de
 * que falta a aprovação dela.
 */
class ValidacaoConcluida extends NotificacaoElo
{
    public function __construct(
        public Milestone $milestone,
        public bool $aguardandoContraparte = false,
    ) {
    }

    public function titulo(object $notifiable): string
    {
        return $this->aguardandoContraparte
            ? 'Entrega aguardando sua validação'
            : 'Milestone validado';
    }

    public function mensagem(object $notifiable): string
    {
        $milestone = "\"{$this->milestone->titulo}\"";
        $demanda = $this->milestone->demanda->titulo;

        if ($this->aguardandoContraparte) {
            return "O milestone {$milestone} da demanda \"{$demanda}\" recebeu uma aprovação e aguarda a sua "
                .'para que as horas sejam creditadas ao grupo.';
        }

        $horas = $this->milestone->horas_creditadas;

        if ($notifiable->isEstudante()) {
            return "O milestone {$milestone} foi validado pelo professor e pela instituição. {$horas}h creditadas ao seu histórico.";
        }

        return "O milestone {$milestone} da demanda \"{$demanda}\" foi validado pelas duas partes. {$horas}h creditadas ao grupo.";
    }

    public function url(object $notifiable): string
    {
        return match (true) {
            $notifiable->isEstudante() => route('estudante.horas.index', absolute: false),
            $notifiable->isProfessor() => route('professor.milestones.index', absolute: false),
            default => route('instituicao.demandas.show', $this->milestone->demanda, absolute: false),
        };
    }
}
