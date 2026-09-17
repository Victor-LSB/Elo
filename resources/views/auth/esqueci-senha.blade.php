@extends('layouts.auth')

@section('titulo', 'Esqueci minha senha — Elo')

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
    <h1>Esqueci minha senha</h1>
    <p class="sub">Informe o e-mail da sua conta. Se ele estiver cadastrado, enviamos um link para você definir uma nova senha.</p>

    @if ($errors->any())
      <div class="alert">
        @foreach ($errors->all() as $erro)
          {{ $erro }}<br>
        @endforeach
      </div>
    @endif

    @if (session('status'))
      <div class="note">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
      @csrf
      <div class="field">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="voce@exemplo.com" required autofocus>
      </div>
      <button type="submit" class="btn btn-primary">Enviar link</button>
    </form>

    <p class="foot-note"><a href="{{ route('login') }}" class="link-accent">← Voltar para o login</a></p>
  </div>
</div>
@endsection
