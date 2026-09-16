<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistEtico extends Model
{
    protected $fillable = [
        'demanda_id', 'substitui_servico_profissional', 'justificativa', 'respondido_por',
    ];

    protected function casts(): array
    {
        return ['substitui_servico_profissional' => 'boolean'];
    }

    public function demanda(): BelongsTo
    {
        return $this->belongsTo(Demanda::class);
    }

    public function respondente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'respondido_por');
    }
}
