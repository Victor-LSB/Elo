<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Certificado — {{ $certificado->codigo }}</title>
<style>
  @page { margin: 26pt; }
  body { margin: 0; font-family: 'Times', serif; color: #1B2330; }
  .moldura { border: 3pt solid #14263F; padding: 6pt; }
  .interna { border: 0.75pt solid #A8833A; height: 478pt; padding: 30pt 46pt 0; text-align: center; }
  .marca { font-size: 15pt; font-weight: bold; letter-spacing: 3pt; text-transform: uppercase; color: #14263F; }
  .marca-sub { font-family: 'Helvetica', sans-serif; font-size: 7.5pt; letter-spacing: 2pt; text-transform: uppercase; color: #505A69; margin-top: 3pt; }
  .filete { width: 70pt; height: 0; border-top: 1.5pt solid #A8833A; margin: 18pt auto 18pt; }
  h1 { font-size: 27pt; font-weight: normal; letter-spacing: 3pt; text-transform: uppercase; color: #14263F; margin: 0 0 22pt; }
  .texto { font-size: 12.5pt; line-height: 1.55; color: #1B2330; }
  .nome { font-size: 24pt; font-weight: bold; color: #14263F; margin: 8pt 0 3pt; }
  .dados-aluno { font-family: 'Helvetica', sans-serif; font-size: 9pt; color: #505A69; margin-bottom: 16pt; }
  .demanda { font-size: 16pt; font-weight: bold; margin: 8pt 0 10pt; color: #14263F; }
  table.fatos { width: 100%; border-collapse: collapse; margin-top: 28pt; border-top: 0.75pt solid #D3D8DF; border-bottom: 0.75pt solid #D3D8DF; }
  table.fatos td { width: 33%; padding: 8pt 4pt; text-align: center; }
  .k { font-family: 'Helvetica', sans-serif; font-size: 7pt; letter-spacing: 1.5pt; text-transform: uppercase; color: #505A69; }
  .v { font-size: 12pt; font-weight: bold; margin-top: 2pt; color: #14263F; }
  table.rodape { width: 100%; margin-top: 26pt; }
  table.rodape td { font-family: 'Helvetica', sans-serif; font-size: 7.5pt; color: #505A69; text-align: left; vertical-align: top; line-height: 1.5; }
  table.rodape td.dir { text-align: right; }
  .codigo { font-size: 10pt; font-weight: bold; letter-spacing: 1pt; color: #14263F; }
</style>
</head>
<body>
@php
  $nivel = [1 => 'Nível 1 · validação por presença', 2 => 'Nível 2 · validação por relatório', 3 => 'Nível 3 · validação por milestones'][$demanda->nivel_complexidade] ?? 'Extensão universitária';
  $data = fn ($d) => $d->locale('pt_BR')->translatedFormat('d \d\e F \d\e Y');
  $prof = $demanda->professor?->nome;
@endphp
<div class="moldura">
  <div class="interna">
    <div class="marca">ConectaExtensão</div>
    <div class="marca-sub">Plataforma de extensão universitária</div>
    <div class="filete"></div>

    <h1>Certificado de extensão</h1>

    <div class="texto">Certificamos que</div>
    <div class="nome">{{ $estudante->nome }}</div>
    <div class="dados-aluno">
      @if ($estudante->matricula) Matrícula {{ $estudante->matricula }} @endif
      @if ($estudante->matricula && $estudante->curso) &nbsp;·&nbsp; @endif
      @if ($estudante->curso) {{ $estudante->curso }} @endif
    </div>

    <div class="texto">concluiu com aproveitamento a demanda de extensão</div>
    <div class="demanda">{{ $demanda->titulo }}</div>
    <div class="texto">
      desenvolvida em parceria com <b>{{ $demanda->instituicao->nome }}</b>@if ($prof), sob orientação de <b>{{ $prof }}</b>@if ($demanda->departamento) (Departamento de {{ $demanda->departamento->nome }})@endif @endif,
      @if ($inicio) no período de {{ $data($inicio) }} a {{ $data($fim) }}, @endif
      com validação de todas as etapas previstas.
    </div>

    <table class="fatos">
      <tr>
        <td><div class="k">Carga horária</div><div class="v">{{ $certificado->horas_totais }} horas</div></td>
        <td><div class="k">Modalidade</div><div class="v">{{ $nivel }}</div></td>
        <td><div class="k">Emitido em</div><div class="v">{{ $data($certificado->data_emissao) }}</div></td>
      </tr>
    </table>

    <table class="rodape">
      <tr>
        <td>
          Código de verificação<br>
          <span class="codigo">{{ $certificado->codigo }}</span>
        </td>
        <td class="dir">
          A autenticidade deste documento pode ser conferida em<br>
          {{ $urlVerificacao }}
        </td>
      </tr>
    </table>
  </div>
</div>
</body>
</html>
