<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificado extends Model
{
    protected $fillable = ['estudante_id', 'demanda_id', 'horas_totais', 'pdf_path', 'data_emissao'];

    protected function casts(): array
    {
        return ['data_emissao' => 'datetime'];
    }

    public function estudante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'estudante_id');
    }

    public function demanda(): BelongsTo
    {
        return $this->belongsTo(Demanda::class);
    }
}
