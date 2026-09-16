<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class Demanda extends Model
{
    protected $fillable = [
        'instituicao_id', 'departamento_id', 'professor_id',
        'titulo', 'descricao', 'area_sugerida',
        'nivel_estimado', 'nivel_complexidade',
        'horas_min', 'horas_max', 'status',
        'prazo_candidatura_dias', 'data_abertura_candidatura',
    ];

    protected function casts(): array
    {
        return ['data_abertura_candidatura' => 'datetime'];
    }

    // ---------- Relações ----------

    public function instituicao(): BelongsTo
    {
        return $this->belongsTo(Instituicao::class);
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    public function professor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    public function checklistEtico(): HasOne
    {
        return $this->hasOne(ChecklistEtico::class);
    }

    public function candidaturas(): HasMany
    {
        return $this->hasMany(Candidatura::class);
    }

    public function candidaturaAprovada(): HasOne
    {
        return $this->hasOne(Candidatura::class)->where('status', 'aprovada');
    }

    public function grupoAtivo(): ?Grupo
    {
        return $this->candidaturaAprovada?->grupo;
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class)->orderBy('ordem');
    }

    public function certificados(): HasMany
    {
        return $this->hasMany(Certificado::class);
    }

    // ---------- Triagem (RF-02.2) ----------

    /**
     * Aplica a decisão de triagem da coordenação. Se o checklist ético
     * sinalizar substituição de serviço profissional, a demanda vai para
     * revisão adicional em vez de abrir direto para candidatura (RF-06.2).
     */
    public function aplicarTriagem(array $dados, bool $checklistSinalizado): void
    {
        $this->fill([
            'nivel_complexidade' => $dados['nivel_complexidade'],
            'departamento_id' => $dados['departamento_id'],
            'professor_id' => $dados['professor_id'],
            'horas_min' => $dados['horas_min'],
            'horas_max' => $dados['horas_max'],
            'prazo_candidatura_dias' => $dados['prazo_candidatura_dias'] ?? 5,
        ]);

        if ($checklistSinalizado) {
            $this->status = 'em_revisao_adicional';
        } else {
            $this->status = 'aberta_candidatura';
            $this->data_abertura_candidatura = now();
        }

        $this->save();

        // Nível 3 exige milestones definidos na aprovação (RF-04.1) — ficam
        // a cargo do professor/instituição via MilestoneController depois
        // que uma candidatura for aprovada.
    }

    public function prazoCandidaturaVencido(): bool
    {
        if (! $this->data_abertura_candidatura || ! $this->prazo_candidatura_dias) {
            return false;
        }

        return Carbon::now()->diffInDays($this->data_abertura_candidatura) >= $this->prazo_candidatura_dias;
    }

    public function isNivel3(): bool
    {
        return $this->nivel_complexidade === 3;
    }

    /**
     * RF-05.4/05.5: quando todos os milestones estiverem concluídos, marca
     * a demanda como concluída e emite um certificado consolidado para
     * cada estudante do grupo ativo, somando as horas de todos os
     * milestones.
     */
    public function verificarConclusaoEEmitirCertificados(): void
    {
        if ($this->milestones()->where('status', '!=', 'concluido')->exists()) {
            return;
        }

        $grupo = $this->grupoAtivo();
        if (! $grupo) {
            return;
        }

        $totalHoras = $this->milestones()->sum('horas_creditadas');
        $statusAnterior = $this->status;
        $this->update(['status' => 'concluida']);
        Auditoria::registrar($this, $statusAnterior, 'concluida');

        foreach ($grupo->membros as $estudante) {
            Certificado::firstOrCreate(
                ['estudante_id' => $estudante->id, 'demanda_id' => $this->id],
                ['horas_totais' => $totalHoras, 'data_emissao' => now()]
            );
        }
    }
}
