@extends('layouts.auth')

@section('titulo', 'Cadastro de estudante — Elo')

@push('estilos')
<style>
  .shell{ max-width: 560px; margin: 0 auto; padding: 24px 24px 72px; }
</style>
@endpush

@section('body')
<div class="top">
  <a href="{{ route('landing') }}" class="logo">Elo</a>
  <a href="{{ route('cadastro') }}" class="back">← Trocar papel</a>
</div>

<div class="shell">
  <div class="auth-card">
    <span class="tag">Cadastro · Estudante</span>
    <h1>Crie sua conta de estudante</h1>
    <p class="sub">Com ela você forma ou entra em um grupo e se candidata a demandas de instituições parceiras.</p>

    @if ($errors->any())
      <div class="alert">
        @foreach ($errors->all() as $erro)
          {{ $erro }}<br>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('cadastro.estudante') }}">
      @csrf
      <div class="field">
        <label for="nome">Nome completo</label>
        <input type="text" id="nome" name="nome" value="{{ old('nome') }}" placeholder="Seu nome" required>
      </div>

      <div class="grid-2">
        <div class="field">
          <label for="email">E-mail institucional</label>
          <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="voce@edu.univali.br" required>
        </div>
        <div class="field">
          <label for="matricula">Matrícula</label>
          <input type="text" id="matricula" name="matricula" value="{{ old('matricula') }}" placeholder="Nº da matrícula" required>
        </div>
      </div>

      <div class="field">
        <label for="curso">Curso</label>
        <select id="curso" name="curso" required>
          <option value="" disabled {{ old('curso') ? '' : 'selected' }}>Selecione seu curso</option>
          @foreach (['Sistemas para Internet', 'Ciência da Computação', 'Engenharia de Software', 'Administração', 'Design', 'Pedagogia', 'Outro'] as $curso)
            <option value="{{ $curso }}" @selected(old('curso') === $curso)>{{ $curso }}</option>
          @endforeach
        </select>
      </div>

      <div class="grid-2">
        <div class="field">
          <label for="password">Senha</label>
          <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres" required>
        </div>
        <div class="field">
          <label for="password_confirmation">Confirmar senha</label>
          <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repita a senha" required>
        </div>
      </div>

      <label class="checkbox-line" style="margin-top:22px;">
        <input type="checkbox" required>
        <span>Li e concordo com os <a href="#" class="link-accent">Termos de uso</a> e a <a href="#" class="link-accent">Política de privacidade</a> da Elo.</span>
      </label>

      <button type="submit" class="btn btn-primary">Criar minha conta</button>
    </form>

    <p class="foot-note">Já tem conta? <a href="{{ route('login') }}" class="link-accent">Entrar</a></p>
  </div>
</div>
@endsection
