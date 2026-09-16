<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Notification;

class Grupo extends Model
{
    protected $fillable = ['nome', 'criado_por'];

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    public function membros(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'grupo_membros', 'grupo_id', 'estudante_id')
            ->withTimestamps();
    }

    public function candidaturas(): HasMany
    {
        return $this->hasMany(Candidatura::class);
    }

    /**
     * Regra de negócio: um grupo não pode ter mais de uma candidatura
     * pendente ou demanda em execução ao mesmo tempo. Enquanto isso for
     * verdade, o grupo fica bloqueado para novas candidaturas.
     *
     * Chamar SEMPRE antes de criar uma nova Candidatura (RF-03.2).
     */
    public function temEngajamentoAtivo(): bool
    {
        return $this->candidaturas()
            ->whereIn('status', ['pendente'])
            ->exists()
            ||
            Demanda::whereHas('candidaturaAprovada', function ($q) {
                $q->where('grupo_id', $this->id);
            })
            ->whereIn('status', ['em_execucao'])
            ->exists();
    }

    /** A demanda em que o grupo está atualmente engajado (se houver). */
    public function demandaAtiva(): ?Demanda
    {
        $candidaturaAtiva = $this->candidaturas()
            ->whereIn('status', ['pendente', 'aprovada'])
            ->whereHas('demanda', fn ($q) => $q->whereIn('status', ['aberta_candidatura', 'em_execucao']))
            ->latest()
            ->first();

        return $candidaturaAtiva?->demanda;
    }

    /** Envia uma notificação a todos os membros do grupo (RF-07.1). */
    public function notificarMembros($notificacao): void
    {
        Notification::send($this->membros, $notificacao);
    }
}
