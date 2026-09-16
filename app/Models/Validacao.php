<?php

namespace App\Models;

use App\Notifications\ValidacaoConcluida;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Validacao extends Model
{
    protected $table = 'validacoes';

    protected $fillable = [
        'milestone_id', 'professor_id', 'professor_aprovou', 'professor_data',
        'instituicao_aprovou', 'instituicao_data', 'comentario',
    ];

    protected function casts(): array
    {
        return [
            'professor_aprovou' => 'boolean',
            'instituicao_aprovou' => 'boolean',
            'professor_data' => 'datetime',
            'instituicao_data' => 'datetime',
        ];
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    public function professor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    public function aprovarComoProfessor(User $professor): void
    {
        $this->update([
            'professor_id' => $professor->id,
            'professor_aprovou' => true,
            'professor_data' => now(),
        ]);
        $this->finalizarSePossivel(aprovadorFoiProfessor: true);
    }

    public function aprovarComoInstituicao(): void
    {
        $this->update(['instituicao_aprovou' => true, 'instituicao_data' => now()]);
        $this->finalizarSePossivel(aprovadorFoiProfessor: false);
    }

    private function finalizarSePossivel(bool $aprovadorFoiProfessor): void
    {
        $this->refresh();
        $milestone = $this->milestone;

        if ($milestone->podeSerConcluido()) {
            $milestone->concluirECreditarHoras();

            // RF-07.1: dupla validação completa — grupo, professor e
            // instituição sabem que as horas foram creditadas.
            $notificacao = new ValidacaoConcluida($milestone->refresh());
            $milestone->demanda->grupoAtivo()?->notificarMembros($notificacao);
            $milestone->demanda->professor?->notify($notificacao);
            $milestone->demanda->instituicao->usuario?->notify($notificacao);

            return;
        }

        // Ainda falta a outra parte: avisa quem precisa aprovar (RF-05.3).
        $pendente = $aprovadorFoiProfessor
            ? $milestone->demanda->instituicao->usuario
            : $milestone->demanda->professor;

        $pendente?->notify(new ValidacaoConcluida($milestone, aguardandoContraparte: true));
    }
}
