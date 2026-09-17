@extends('layouts.auth')

@section('titulo', 'Definir nova senha — Elo')

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
    <h1>Definir nova senha</h1>
    <p class="sub">Escolha uma senha com pelo menos 8 caracteres para acessar sua conta.</p>

    @if ($errors->any())
      <div class="alert">
        @foreach ($errors->all() as $erro)
          {{ $erro }}<br>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">
      <div class="field">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required autofocus>
      </div>
      <div class="field">
        <label for="password">Nova senha</label>
        <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres" required>
      </div>
      <div class="field">
        <label for="password_confirmation">Confirmar nova senha</label>
        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repita a senha" required>
      </div>
      <button type="submit" class="btn btn-primary">Salvar senha</button>
    </form>
  </div>
</div>
@endsection
