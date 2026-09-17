@extends('layouts.auth')

@section('titulo', 'Entrar — Elo')

@push('estilos')
<style>
  .shell{ min-height: calc(100vh - 80px); display: flex; align-items: center; justify-content: center; padding: 40px 24px 60px; }
  .auth-card{ width: 100%; max-width: 420px; }
</style>
@endpush

@section('body')
<div class="top centered">
  <a href="{{ route('landing') }}" class="logo">Elo</a>
</div>

<div class="shell">
  <div class="auth-card">
    <h1>Entrar</h1>
    <p class="sub">Acesse com o e-mail cadastrado como estudante, instituição, professor, coordenação ou admin.</p>

    @if ($errors->any())
      <div class="alert">
        @foreach ($errors->all() as $erro)
          {{ $erro }}<br>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}">
      @csrf
      <div class="field">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="voce@exemplo.com" required autofocus>
      </div>
      <div class="field">
        <label for="password">Senha</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>
      </div>
      <div class="row-between">
        <label class="checkbox-line">
          <input type="checkbox" name="lembrar" value="1">
          Lembrar de mim
        </label>
        <a href="{{ route('password.request') }}" class="link-accent">Esqueci minha senha</a>
      </div>
      <button type="submit" class="btn btn-primary">Entrar</button>
    </form>

    <div class="divider">novo por aqui</div>

    <p class="foot-note">Ainda não tem conta? <a href="{{ route('cadastro') }}" class="link-accent">Cadastre-se</a></p>
  </div>
</div>
@endsection
