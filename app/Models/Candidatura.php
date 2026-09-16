<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Candidatura extends Model
{
    protected $fillable = ['demanda_id', 'grupo_id', 'mensagem', 'status', 'data_resposta'];

    protected function casts(): array
    {
        return ['data_resposta' => 'datetime'];
    }

    public function demanda(): BelongsTo
    {
        return $this->belongsTo(Demanda::class);
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class);
    }
}
