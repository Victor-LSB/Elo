@extends('layouts.auth')

@section('titulo', 'Verificação de certificado — ConectaExtensão')

@push('estilos')
<style>
  .shell{ min-height: calc(100vh - 80px); display: flex; align-items: center; justify-content: center; padding: 40px 24px 60px; }
  .auth-card{ width: 100%; max-width: 520px; }
  .auth-card.invalido{ border-top-color: var(--danger); }
  .dados{ margin: 22px 0 0; border-top: 1px solid var(--border); }
  .dados div{ display: flex; justify-content: space-between; gap: 16px; padding: 11px 0; border-bottom: 1px solid var(--border); font-size: 14.5px; }
  .dados dt{ color: var(--ink-soft); }
  .dados dd{ margin: 0; font-weight: 600; text-align: right; }
  .codigo{ font-family: ui-monospace, Consolas, monospace; letter-spacing: 0.04em; }
</style>
@endpush

@section('body')
<div class="top centered">
  <a href="{{ route('landing') }}" class="logo">ConectaExtensão</a>
</div>

<div class="shell">
  @if ($certificado)
    <div class="auth-card">
      <span class="tag">Certificado válido</span>
      <h1>Documento autêntico</h1>
      <p class="sub">Este certificado foi emitido pela plataforma ConectaExtensão e consta nos nossos registros.</p>
      <dl class="dados">
        <div><dt>Estudante</dt><dd>{{ $certificado->estudante->nome }}</dd></div>
        <div><dt>Demanda</dt><dd>{{ $certificado->demanda->titulo }}</dd></div>
        <div><dt>Instituição parceira</dt><dd>{{ $certificado->demanda->instituicao->nome }}</dd></div>
        <div><dt>Carga horária</dt><dd>{{ $certificado->horas_totais }} horas</dd></div>
        <div><dt>Emitido em</dt><dd>{{ $certificado->data_emissao->format('d/m/Y') }}</dd></div>
        <div><dt>Código</dt><dd class="codigo">{{ $certificado->codigo }}</dd></div>
      </dl>
    </div>
  @else
    <div class="auth-card invalido">
      <span class="tag" style="background:var(--danger-soft);color:#7A2626;">Não encontrado</span>
      <h1>Código não localizado</h1>
      <p class="sub">Não existe certificado com o código <span class="codigo">{{ $codigo }}</span>. Confira se o código foi digitado corretamente, exatamente como aparece no documento.</p>
    </div>
  @endif
</div>
@endsection
