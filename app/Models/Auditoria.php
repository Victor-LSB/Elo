<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Auditoria extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'auditavel_type', 'auditavel_id', 'usuario_id',
        'status_anterior', 'status_novo', 'observacao',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Auditoria $a) {
            $a->created_at ??= now();
        });
    }

    public function auditavel(): MorphTo
    {
        return $this->morphTo();
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function registrar(Model $entidade, ?string $statusAnterior, string $statusNovo, ?User $usuario = null, ?string $observacao = null): self
    {
        return static::create([
            'auditavel_type' => get_class($entidade),
            'auditavel_id' => $entidade->id,
            'usuario_id' => $usuario?->id,
            'status_anterior' => $statusAnterior,
            'status_novo' => $statusNovo,
            'observacao' => $observacao,
        ]);
    }
}
