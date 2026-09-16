@extends('layouts.dashboard')

@section('titulo', 'Perfil da instituição — Elo')
@section('sidebar-role', 'Instituição parceira')
@section('sidebar-nav')
  @include('partials.sidebar.instituicao')
@endsection

@push('estilos')
<style>
  .content{ max-width: 640px; }
  .status-line{ margin-bottom: 18px; }
</style>
@endpush

@section('topbar')
  <h1>Perfil da instituição</h1>
  <p class="sub">Mantenha os dados de contato atualizados para a coordenação e os professores.</p>
@endsection

@section('content')
  @if ($errors->any())
    <div class="alert">
      @foreach ($errors->all() as $erro)
        {{ $erro }}<br>
      @endforeach
    </div>
  @endif

  @if (session('status'))
    <div class="status-line">
      <span class="badge badge-ativa">{{ session('status') }}</span>
    </div>
  @endif

  <form method="POST" action="{{ route('instituicao.perfil.update') }}">
    @csrf
    @method('PUT')

    <div class="field">
      <label for="nome">Nome da instituição</label>
      <input type="text" id="nome" name="nome" value="{{ old('nome', $instituicao->nome) }}" required>
    </div>

    <div class="field">
      <label for="tipo">Tipo de instituição</label>
      <select id="tipo" name="tipo" required>
        @foreach (['ong' => 'ONG', 'escola_publica' => 'Escola pública', 'associacao_bairro' => 'Associação de bairro', 'orgao_publico' => 'Órgão público municipal', 'outro' => 'Outro'] as $valor => $rotulo)
          <option value="{{ $valor }}" @selected(old('tipo', $instituicao->tipo) === $valor)>{{ $rotulo }}</option>
        @endforeach
      </select>
    </div>

    <div class="grid-2">
      <div class="field">
        <label for="responsavel">Responsável</label>
        <input type="text" id="responsavel" name="responsavel" value="{{ old('responsavel', $instituicao->responsavel) }}" required>
      </div>
      <div class="field">
        <label for="contato_telefone">Telefone</label>
        <input type="tel" id="contato_telefone" name="contato_telefone" value="{{ old('contato_telefone', $instituicao->contato_telefone) }}">
      </div>
    </div>

    <div class="field">
      <label for="contato_email">E-mail de contato</label>
      <input type="email" id="contato_email" name="contato_email" value="{{ old('contato_email', $instituicao->contato_email) }}" required>
    </div>

    <div class="field">
      <label for="sobre">Sobre a instituição</label>
      <textarea id="sobre" name="sobre">{{ old('sobre', $instituicao->sobre) }}</textarea>
      <div class="field-hint">Visível para a coordenação durante a validação do cadastro.</div>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Salvar alterações</button>
    </div>
  </form>
@endsection
