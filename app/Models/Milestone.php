<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Milestone extends Model
{
    protected $fillable = [
        'demanda_id', 'ordem', 'titulo', 'descricao', 'horas_creditadas',
        'prazo', 'status', 'entrega_path', 'data_entrega', 'data_conclusao',
    ];

    protected function casts(): array
    {
        return [
            'prazo' => 'date',
            'data_entrega' => 'datetime',
            'data_conclusao' => 'datetime',
        ];
    }

    public function demanda(): BelongsTo
    {
        return $this->belongsTo(Demanda::class);
    }

    public function validacao(): HasOne
    {
        return $this->hasOne(Validacao::class);
    }

    /** RF-04.3: marca como "em risco" se passou do prazo sem atualização. Chamado pelo job agendado. */
    public function marcarEmRiscoSeVencido(): bool
    {
        if ($this->status === 'em_andamento' && $this->prazo->isPast()) {
            $this->update(['status' => 'em_risco']);
            return true;
        }
        return false;
    }

    /** Grupo anexa a entrega (nível 2/3). */
    public function registrarEntrega(string $path): void
    {
        $this->update([
            'entrega_path' => $path,
            'data_entrega' => now(),
            'status' => 'aguardando_validacao',
        ]);
    }

    /**
     * RF-05.3: nível 3 precisa de aprovação do professor E da instituição
     * antes de creditar horas. Níveis 1/2 usam apenas um validador.
     */
    public function podeSerConcluido(): bool
    {
        $v = $this->validacao;
        if (! $v) {
            return false;
        }

        if ($this->demanda->isNivel3()) {
            return $v->professor_aprovou && $v->instituicao_aprovou;
        }

        return $v->professor_aprovou || $v->instituicao_aprovou;
    }

    /** Credita as horas ao grupo ativo e marca o milestone como concluído. */
    public function concluirECreditarHoras(): void
    {
        $this->update(['status' => 'concluido', 'data_conclusao' => now()]);
        $this->demanda->verificarConclusaoEEmitirCertificados();
    }
}
