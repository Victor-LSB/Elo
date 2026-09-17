<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Convite para um estudante entrar num grupo. O convidado precisa aceitar
 * ou recusar — entrar num grupo nunca é automático (RF-03).
 */
class ConviteGrupo extends Model
{
    protected $table = 'convites_grupo';

    protected $fillable = ['grupo_id', 'estudante_id', 'convidado_por', 'status', 'respondido_em'];

    protected function casts(): array
    {
        return [
            'respondido_em' => 'datetime',
        ];
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class);
    }

    public function estudante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'estudante_id');
    }

    public function convidadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'convidado_por');
    }

    public function isPendente(): bool
    {
        return $this->status === 'pendente';
    }

    public function aceitar(): void
    {
        $this->grupo->membros()->syncWithoutDetaching([$this->estudante_id]);
        $this->update(['status' => 'aceito', 'respondido_em' => now()]);

        // Outros convites pendentes que o estudante tinha para outros grupos
        // deixam de fazer sentido, já que ele só pode estar em um grupo.
        static::where('estudante_id', $this->estudante_id)
            ->where('id', '!=', $this->id)
            ->where('status', 'pendente')
            ->update(['status' => 'cancelado', 'respondido_em' => now()]);
    }

    public function recusar(): void
    {
        $this->update(['status' => 'recusado', 'respondido_em' => now()]);
    }
}
