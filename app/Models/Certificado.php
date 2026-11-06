<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Certificado extends Model
{
    protected $fillable = ['estudante_id', 'demanda_id', 'codigo', 'horas_totais', 'pdf_path', 'data_emissao'];

    /** Todo certificado nasce com um código único, usado na verificação pública de autenticidade. */
    protected static function booted(): void
    {
        static::creating(function (Certificado $certificado) {
            if (! $certificado->codigo) {
                do {
                    $codigo = 'CEX-'.Str::upper(Str::random(4)).'-'.Str::upper(Str::random(4));
                } while (static::where('codigo', $codigo)->exists());

                $certificado->codigo = $codigo;
            }
        });
    }

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
