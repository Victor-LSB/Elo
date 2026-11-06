<?php

namespace App\Http\Controllers;

use App\Models\Certificado;

class CertificadoVerificacaoController extends Controller
{
    public function show(string $codigo)
    {
        $certificado = Certificado::with('estudante', 'demanda.instituicao')
            ->where('codigo', strtoupper($codigo))
            ->first();

        return response()->view('certificados.verificar', [
            'certificado' => $certificado,
            'codigo' => strtoupper($codigo),
        ], $certificado ? 200 : 404);
    }
}
