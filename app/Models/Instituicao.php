<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Instituicao extends Model
{
    protected $table = 'instituicoes';

    protected $fillable = [
        'nome', 'tipo', 'responsavel', 'contato_email', 'contato_telefone', 'sobre', 'status',
    ];

    public function usuario(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function demandas(): HasMany
    {
        return $this->hasMany(Demanda::class);
    }

    public function isPendente(): bool { return $this->status === 'pendente'; }
    public function isAtiva(): bool { return $this->status === 'ativa'; }

    public function validar(): void
    {
        $this->update(['status' => 'ativa']);
    }

    public function recusar(): void
    {
        $this->update(['status' => 'recusada']);
    }
}
