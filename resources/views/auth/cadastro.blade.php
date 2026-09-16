@extends('layouts.auth')

@section('titulo', 'Cadastre-se — Elo')

@push('estilos')
<style>
  .top{ max-width: 760px; }
  .shell{ max-width: 760px; margin: 0 auto; padding: 40px 24px 72px; }
  .head{ text-align: center; max-width: 46ch; margin: 0 auto 40px; }
  .head h1{ font-size: 30px; }
  .head p{ margin-top: 10px; font-size: 16px; }
  .role-grid{ display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
  .role-card{
    background: var(--surface); border: 1.5px solid var(--border); border-radius: var(--radius-card);
    padding: 30px 26px; text-align: left; cursor: pointer; transition: border-color 0.15s ease;
  }
  .role-card:hover{ border-color: var(--primary); }
  .role-mark{
    width: 42px; height: 42px; border-radius: 3px; display: flex; align-items: center; justify-content: center;
    font-family: 'Source Serif 4', serif; font-weight: 700; font-size: 19px; color: #fff; margin-bottom: 18px;
  }
  .role-card h3{ font-size: 19px; margin-bottom: 8px; }
  .role-card p{ font-size: 14.5px; margin-bottom: 18px; }
  .role-card .go{ font-size: 14px; font-weight: 700; color: var(--accent); }
  .note{ margin-top: 32px; }
  .foot-note{ margin-top: 30px; }
  @media (max-width: 560px){ .role-grid{ grid-template-columns: 1fr; } }
</style>
@endpush

@section('body')
<div class="top centered">
  <a href="{{ route('landing') }}" class="logo">Elo</a>
</div>

<div class="shell">
  <div class="head">
    <h1>Como você vai usar a Elo?</h1>
    <p>Essa escolha define suas permissões na plataforma e não pode ser alterada depois do cadastro.</p>
  </div>

  <div class="role-grid">
    <a href="{{ route('cadastro.estudante.form') }}" class="role-card">
      <div class="role-mark" style="background:var(--primary-dark)">E</div>
      <h3>Sou estudante</h3>
      <p>Quero formar ou entrar em um grupo, me candidatar a demandas e acumular horas complementares.</p>
      <span class="go">Cadastrar como estudante →</span>
    </a>
    <a href="{{ route('cadastro.instituicao.form') }}" class="role-card">
      <div class="role-mark" style="background:var(--primary)">I</div>
      <h3>Sou instituição parceira</h3>
      <p>Represento uma ONG, escola pública ou associação de bairro e tenho uma demanda real para cadastrar.</p>
      <span class="go">Cadastrar instituição →</span>
    </a>
  </div>

  <div class="note">
    Professores, coordenação de extensão e administradores não se autocadastram. Contas desses papéis são criadas pela coordenação de extensão da instituição de ensino.
  </div>

  <p class="foot-note">Já tem uma conta? <a href="{{ route('login') }}" class="link-accent">Entrar</a></p>
</div>
@endsection
