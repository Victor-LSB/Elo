<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracao extends Model
{
    protected $table = 'configuracoes';

    protected $primaryKey = 'chave';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['chave', 'valor'];

    private const PADROES = [
        'prazo_candidatura_dias' => '5',
        'dias_ate_em_risco' => '3',
        'dias_ate_habilitar_reabertura' => '7',
        'dias_ate_marcar_parada' => '5',
    ];

    public static function get(string $chave): string
    {
        return static::find($chave)?->valor ?? self::PADROES[$chave] ?? '';
    }

    public static function set(string $chave, string $valor): void
    {
        static::updateOrCreate(['chave' => $chave], ['valor' => $valor]);
    }
}
