@extends('layouts.dashboard')

@section('titulo', 'Meu grupo — Elo')
@section('sidebar-role', auth()->user()->curso)
@section('sidebar-nav')
  @include('partials.sidebar.estudante')
@endsection

@push('estilos')
<style>
  .content{ max-width: 640px; }
  .group-name{ display: flex; align-items: center; justify-content: space-between; gap: 10px; }
  .group-list{ display: flex; flex-direction: column; gap: 12px; margin-top: 18px; }
  .group-member{ display: flex; align-items: center; gap: 12px; }
  .avatar{
    width: 36px; height: 36px; border-radius: 50%; background: #E4EEEC; color: var(--primary-dark);
    display: flex; align-items: center; justify-content: center; font-size: 13.5px; font-weight: 700; flex-shrink: 0;
  }
  .member-info .name{ font-size: 14.5px; font-weight: 700; color: var(--ink); }
  .member-info .course{ font-size: 12.5px; color: var(--ink-soft); }
  .member-tag{ margin-left: auto; font-size: 12px; font-weight: 700; color: var(--support); }
  .invite-row{ margin-top: 20px; display: flex; gap: 10px; }
  .invite-row input{
    flex: 1; padding: 10px 13px; font-size: 14.5px; font-family: 'Atkinson Hyperlegible', sans-serif;
    border: 1.5px solid var(--border); border-radius: 3px; background: var(--bg); color: var(--ink);
  }
  .invite-row input:focus{ outline: none; border-color: var(--primary); background: var(--surface); }
  .status-banner{
    margin-top: 18px; padding: 14px 16px; background: var(--surface-alt); border-left: 3px solid var(--support);
    border-radius: 2px; font-size: 13.5px; color: var(--ink-soft);
  }
  .btn-sair{ background: transparent; border-color: var(--border); color: var(--ink-soft); }
  .btn-sair:hover{ border-color: var(--danger); color: var(--danger); }
  @media (max-width: 760px){ .invite-row{ flex-direction: column; } }
</style>
@endpush

@section('topbar')
  <h1>Meu grupo</h1>
  <p class="sub">Um grupo pode se candidatar junto a demandas e reunir estudantes de qualquer curso.</p>
@endsection

@section('content')
  @if ($convitesRecebidos->isNotEmpty())
    <div class="card" style="border-left: 3px solid var(--accent);">
      <h2>Convites recebidos</h2>
      <p class="sub-h">Você precisa aceitar para entrar em um grupo.</p>
      <div class="group-list">
        @foreach ($convitesRecebidos as $convite)
          <div class="group-member" style="align-items:flex-start;">
            <span class="avatar">{{ collect(explode(' ', $convite->grupo->nome))->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode('') }}</span>
            <div class="member-info" style="flex:1;">
              <div class="name">Grupo "{{ $convite->grupo->nome }}"</div>
              <div class="course">
                Convidado por {{ $convite->convidadoPor->nome }} · membros atuais: {{ $convite->grupo->membros->pluck('nome')->implode(', ') }}
              </div>
              <div style="margin-top:8px; display:flex; gap:8px;">
                <form method="POST" action="{{ route('estudante.convites.aceitar', $convite) }}">
                  @csrf
                  <button type="submit" class="btn btn-approve btn-small">Aceitar</button>
                </form>
                <form method="POST" action="{{ route('estudante.convites.recusar', $convite) }}">
                  @csrf
                  <button type="submit" class="btn btn-reject btn-small">Recusar</button>
                </form>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endif

  @if (! $grupo)
    <div class="card">
      <h2>Criar um grupo</h2>
      <p class="sub-h">Você ainda não faz parte de um grupo. Crie um para poder se candidatar a demandas.</p>
      <form method="POST" action="{{ route('estudante.grupo.store') }}">
        @csrf
        <div class="field">
          <label for="nome">Nome do grupo</label>
          <input type="text" id="nome" name="nome" placeholder="Ex.: Vetor4" required>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top:20px;">Criar grupo</button>
      </form>
    </div>
  @else
    @php $demandaAtiva = $grupo->demandaAtiva(); @endphp

    <div class="card">
      <div class="group-name">
        <h2>{{ $grupo->nome }}</h2>
        @if ($demandaAtiva)
          <span class="badge badge-execucao">Ativo em 1 demanda</span>
        @endif
      </div>
      <p class="sub-h">Criado por {{ $grupo->criador->nome }} em {{ $grupo->created_at->format('d/m/Y') }}</p>

      <div class="group-list">
        @foreach ($grupo->membros as $membro)
          <div class="group-member">
            <span class="avatar">{{ collect(explode(' ', $membro->nome))->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode('') }}</span>
            <div class="member-info">
              <div class="name">{{ $membro->nome }}</div>
              <div class="course">{{ $membro->curso }}</div>
            </div>
            @if ($membro->id === auth()->id())
              <span class="member-tag">Você</span>
            @endif
          </div>
        @endforeach
      </div>

      @error('identificador')
        <div class="alert">{{ $message }}</div>
      @enderror

      <form method="POST" action="{{ route('estudante.grupo.convidar') }}" class="invite-row">
        @csrf
        <input type="text" name="identificador" placeholder="E-mail ou matrícula do colega para convidar" required>
        <button type="submit" class="btn btn-primary">Convidar</button>
      </form>

      @if ($convitesEnviados->isNotEmpty())
        <div class="status-banner">
          <strong>Convites aguardando resposta:</strong>
          {{ $convitesEnviados->map(fn ($c) => $c->estudante->nome)->implode(', ') }}
        </div>
      @endif

      @if ($demandaAtiva)
        <div class="status-banner">
          O grupo já está engajado na demanda <strong>{{ $demandaAtiva->titulo }}</strong>. Novos convites não afetam o trabalho em andamento, mas passam a valer para as próximas candidaturas do grupo.
        </div>
      @endif
    </div>

    <div class="card">
      <h2>Sair do grupo</h2>
      <p class="sub-h" style="margin-top:6px;">Isso não afeta as horas já creditadas a você em demandas concluídas.</p>
      <form method="POST" action="{{ route('estudante.grupo.sair') }}">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sair">Sair do grupo</button>
      </form>
    </div>
  @endif
@endsection
