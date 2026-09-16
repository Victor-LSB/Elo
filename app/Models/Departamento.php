<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departamento extends Model
{
    protected $fillable = ['nome', 'area'];

    public function demandas(): HasMany
    {
        return $this->hasMany(Demanda::class);
    }

    public function professores(): HasMany
    {
        return $this->hasMany(User::class)->where('papel', 'professor');
    }
}
