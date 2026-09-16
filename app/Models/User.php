<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nome', 'email', 'password', 'papel', 'curso', 'matricula',
        'departamento_id', 'instituicao_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // ---------- Relações ----------

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    public function instituicao(): BelongsTo
    {
        return $this->belongsTo(Instituicao::class);
    }

    /** Grupos dos quais este estudante faz parte. */
    public function grupos(): BelongsToMany
    {
        return $this->belongsToMany(Grupo::class, 'grupo_membros', 'estudante_id', 'grupo_id')
            ->withTimestamps();
    }

    /** Demandas atribuídas a este professor. */
    public function demandasComoProfessor(): HasMany
    {
        return $this->hasMany(Demanda::class, 'professor_id');
    }

    public function certificados(): HasMany
    {
        return $this->hasMany(Certificado::class, 'estudante_id');
    }

    // ---------- Helpers de papel ----------

    public function isEstudante(): bool { return $this->papel === 'estudante'; }
    public function isInstituicao(): bool { return $this->papel === 'instituicao'; }
    public function isProfessor(): bool { return $this->papel === 'professor'; }
    public function isCoordenacao(): bool { return $this->papel === 'coordenacao'; }
    public function isAdmin(): bool { return $this->papel === 'admin'; }
}
