@extends('layouts.auth')

@section('titulo', 'Cadastro de instituição — Elo')

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
    <span class="tag">Cadastro · Instituição parceira</span>
    <h1>Cadastre sua instituição</h1>
    <p class="sub">ONGs, escolas públicas e associações de bairro podem cadastrar demandas reais para estudantes.</p>

    @if ($errors->any())
      <div class="alert">
        @foreach ($errors->all() as $erro)
          {{ $erro }}<br>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('cadastro.instituicao') }}">
      @csrf
      <div class="field">
        <label for="nome_instituicao">Nome da instituição</label>
        <input type="text" id="nome_instituicao" name="nome_instituicao" value="{{ old('nome_instituicao') }}" placeholder="Ex.: ONG Girassol" required>
      </div>

      <div class="field">
        <label for="tipo">Tipo de instituição</label>
        <select id="tipo" name="tipo" required>
          <option value="" disabled {{ old('tipo') ? '' : 'selected' }}>Selecione o tipo</option>
          @foreach (['ong' => 'ONG', 'escola_publica' => 'Escola pública', 'associacao_bairro' => 'Associação de bairro', 'orgao_publico' => 'Órgão público municipal', 'outro' => 'Outro'] as $valor => $rotulo)
            <option value="{{ $valor }}" @selected(old('tipo') === $valor)>{{ $rotulo }}</option>
          @endforeach
        </select>
      </div>

      <div class="grid-2">
        <div class="field">
          <label for="responsavel">Responsável</label>
          <input type="text" id="responsavel" name="responsavel" value="{{ old('responsavel') }}" placeholder="Nome do responsável" required>
        </div>
        <div class="field">
          <label for="telefone">Telefone</label>
          <input type="tel" id="telefone" name="telefone" value="{{ old('telefone') }}" placeholder="(47) 99999-9999" required>
        </div>
      </div>

      <div class="field">
        <label for="email">E-mail de contato</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="contato@instituicao.org" required>
      </div>

      <div class="field">
        <label for="sobre">Sobre a instituição</label>
        <textarea id="sobre" name="sobre" placeholder="Conte brevemente o que a instituição faz e a comunidade que atende.">{{ old('sobre') }}</textarea>
        <div class="field-hint">Isso ajuda a coordenação a validar o cadastro mais rápido.</div>
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

      <div class="note">
        Sua conta ficará <strong>pendente de validação</strong>. A coordenação de extensão confere os dados antes de ativar o acesso — isso normalmente leva até 2 dias úteis.
      </div>

      <button type="submit" class="btn btn-primary">Enviar cadastro</button>
    </form>

    <p class="foot-note">Já tem conta? <a href="{{ route('login') }}" class="link-accent">Entrar</a></p>
  </div>
</div>
@endsection
