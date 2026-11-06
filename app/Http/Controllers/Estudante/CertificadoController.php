<?php

namespace App\Http\Controllers\Estudante;

use App\Http\Controllers\Controller;
use App\Models\Certificado;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CertificadoController extends Controller
{
    /** Gera o PDF na hora, a partir dos dados atuais — nada é salvo em disco. */
    public function pdf(Certificado $certificado, Request $request)
    {
        abort_unless($certificado->estudante_id === $request->user()->id, 403);

        $certificado->load([
            'estudante',
            'demanda.instituicao',
            'demanda.departamento',
            'demanda.professor',
            'demanda.candidaturaAprovada',
            'demanda.milestones',
        ]);

        $demanda = $certificado->demanda;

        $html = view('certificados.pdf', [
            'certificado' => $certificado,
            'estudante' => $certificado->estudante,
            'demanda' => $demanda,
            'inicio' => $demanda->candidaturaAprovada?->data_resposta,
            'fim' => $demanda->milestones->max('data_conclusao') ?? $certificado->data_emissao,
            'urlVerificacao' => route('certificados.verificar', $certificado->codigo),
        ])->render();

        $options = new Options;
        $options->set('defaultFont', 'Times');
        $options->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $arquivo = 'certificado-'.Str::slug($demanda->titulo).'-'.$certificado->codigo.'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$arquivo.'"',
        ]);
    }
}
