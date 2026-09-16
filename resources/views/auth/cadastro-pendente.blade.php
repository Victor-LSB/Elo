@extends('layouts.auth')

@section('titulo', 'Cadastro enviado — Elo')

@push('estilos')
<style>
  body{ display: flex; flex-direction: column; }
  .shell{ flex: 1; display: flex; align-items: center; justify-content: center; padding: 24px; }
  .card-ok{
    max-width: 440px; text-align: center; background: var(--surface);
    border: 1px solid var(--border); border-radius: var(--radius-card); padding: 44px 36px;
  }
  .mark{
    width: 56px; height: 56px; border-radius: 50%; background: #E4EEEC; color: var(--support);
    display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;
    font-size: 26px; font-weight: 700;
  }
  .card-ok h1{ font-size: 24px; }
  .card-ok p{ margin-top: 12px; font-size: 15.5px; }
  .steps{ margin-top: 24px; text-align: left; font-size: 14px; color: var(--ink-soft); }
  .steps li{ margin-bottom: 8px; }
  .btn-voltar{
    display: inline-flex; margin-top: 28px; padding: 12px 22px; font-size: 15px; font-weight: 700;
    border-radius: var(--radius-card); border: 1.5px solid var(--primary); color: var(--primary);
  }
  .btn-voltar:hover{ background: var(--primary); color: #fff; }
</style>
@endpush

@section('body')
<div class="top centered">
  <a href="{{ route('landing') }}" class="logo">Elo</a>
</div>

<div class="shell">
  <div class="card-ok">
    <div class="mark">✓</div>
    <h1>Cadastro enviado</h1>
    <p>Sua instituição foi registrada e está com o status <strong>pendente de validação</strong>.</p>
    <ul class="steps">
      <li>A coordenação de extensão vai conferir os dados enviados.</li>
      <li>Você recebe um e-mail quando a conta for ativada (até 2 dias úteis).</li>
      <li>Depois disso, já pode cadastrar sua primeira demanda.</li>
    </ul>
    <a href="{{ route('login') }}" class="btn-voltar">Voltar para o login</a>
  </div>
</div>
@endsection
