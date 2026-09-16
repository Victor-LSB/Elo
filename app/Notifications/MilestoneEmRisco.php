<?php

namespace App\Notifications;

use App\Models\Milestone;

/**
 * RF-07.1 + RF-04.3/04.4: avisa que um milestone passou do prazo sem
 * atualização. Vai para o grupo (ainda dá tempo de responder), para o
 * professor orientador e — quando o risco se prolonga — para o admin,
 * que é quem decide sobre a reabertura.
 */
class MilestoneEmRisco extends NotificacaoElo
{
    public function __construct(
        public Milestone $milestone,
        public bool $riscoProlongado = false,
    ) {
    }

    public function titulo(object $notifiable): string
    {
        return $this->riscoProlongado
            ? 'Milestone em risco há vários dias'
            : 'Milestone em risco';
    }

    public function mensagem(object $notifiable): string
    {
        $milestone = "\"{$this->milestone->titulo}\"";
        $demanda = $this->milestone->demanda->titulo;
        $dias = (int) $this->milestone->prazo->diffInDays(now());

        if ($notifiable->isAdmin() || ($this->riscoProlongado && $notifiable->isProfessor())) {
            return "O milestone {$milestone} da demanda \"{$demanda}\" está em risco há {$dias} dias sem resposta do grupo. "
                .'É possível reabrir o trabalho remanescente para outro grupo, preservando as horas já creditadas.';
        }

        if ($notifiable->isProfessor()) {
            return "O milestone {$milestone} da demanda \"{$demanda}\" passou do prazo sem atualização do grupo.";
        }

        return "O milestone {$milestone} da demanda \"{$demanda}\" passou do prazo. "
            .'Atualize o progresso ou envie a entrega para evitar a reabertura para outro grupo.';
    }

    public function url(object $notifiable): string
    {
        if ($notifiable->isAdmin()) {
            return route('admin.excecoes.show', $this->milestone, absolute: false);
        }

        if ($notifiable->isProfessor()) {
            return route('professor.milestones.index', absolute: false);
        }

        return route('estudante.horas.index', absolute: false);
    }
}
