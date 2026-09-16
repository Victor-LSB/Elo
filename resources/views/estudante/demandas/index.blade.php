@extends('layouts.dashboard')

@section('titulo', 'Demandas abertas — Elo')
@section('sidebar-role', auth()->user()->curso)
@section('sidebar-nav')
  @include('partials.sidebar.estudante')
@endsection

@push('estilos')
<style>
  .group-banner{
    display: flex; align-items: center; justify-content: space-between; gap: 16px;
    background: var(--surface); border: 1px solid var(--border); border-left: 3px solid var(--support);
    border-radius: var(--radius-card); padding: 16px 20px; margin-bottom: 26px; font-size: 14px;
  }
  .group-banner b{ color: var(--primary-dark); }
  .group-banner a{ font-weight: 700; color: var(--accent); flex-shrink: 0; }
  .group-banner a:hover{ text-decoration: underline; }
  .group-banner.locked{ background: var(--accent-soft); border-color: var(--accent); }
  .group-banner.locked b{ color: #6A3E0F; }

  .filters{ display: flex; gap: 10px; margin-bottom: 10px; flex-wrap: wrap; }
  .filter-chip{
    padding: 7px 15px; border: 1.5px solid var(--border); border-radius: 20px;
    font-size: 13.5px; font-weight: 700; color: var(--ink-soft);
  }
  .filter-chip.active{ border-color: var(--primary); background: #E4EEEC; color: var(--primary); }
  .filter-form{ margin-bottom: 22px; display: flex; gap: 10px; flex-wrap: wrap; }
  .filter-form select{
    padding: 8px 12px; font-size: 13.5px; font-family: 'Atkinson Hyperlegible', sans-serif;
    border: 1.5px solid var(--border); border-radius: 3px; background: var(--surface); color: var(--ink-soft);
  }

  .demand-list{ display: flex; flex-direction: column; gap: 14px; }
  .demand-card{
    background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-card);
    padding: 20px 22px; display: flex; align-items: center; justify-content: space-between; gap: 20px;
  }
  .demand-main{ min-width: 0; }
  .demand-top{ display: flex; align-items: center; gap: 10px; margin-bottom: 8px; flex-wrap: wrap; }
  .demand-card h3{ font-size: 17px; }
  .demand-meta{ margin-top: 6px; font-size: 13.5px; color: var(--ink-soft); display: flex; gap: 14px; flex-wrap: wrap; }
  .stopped-tag{ margin-top: 8px; font-size: 12.5px; font-weight: 700; color: var(--danger); }
  .demand-side{ text-align: right; flex-shrink: 0; }
  .demand-side .go{ font-size: 13.5px; font-weight: 700; color: var(--accent); }
  .demand-side .go:hover{ text-decoration: underline; }

  .demand-card.is-locked{ opacity: 0.72; }
  .demand-card.is-locked h3{ color: var(--ink-soft); }
  .demand-card.is-locked .go{ pointer-events: none; color: var(--ink-soft); text-decoration: line-through; }
  .lock-note{ margin-top: 6px; font-size: 12px; font-weight: 700; color: var(--danger); }

  .empty{ text-align: center; padding: 50px 20px; color: var(--ink-soft); font-size: 14.5px; }

  @media (max-width: 760px){
    .demand-card{ flex-direction: column; align-items: flex-start; }
    .demand-side{ text-align: left; }
    .group-banner{ flex-direction: column; align-items: flex-start; }
  }
</style>
@endpush

@section('topbar')
  <h1>Demandas abertas</h1>
  <p class="sub">Candidate-se em grupo às demandas de instituições parceiras e acumule horas complementares.</p>
@endsection

@section('content')
  @if ($grupoBloqueado && $demandaAtiva)
    <div class="group-banner locked">
      <span>🔒 Seu grupo já está em uma demanda ativa (<b>{{ $demandaAtiva->titulo }}</b>). Um grupo só pode ter <b>uma candidatura ou demanda em execução por vez</b> — finalize-a para se candidatar a outra.</span>
      <a href="{{ route('estudante.grupo.show') }}">Ver grupo →</a>
    </div>
  @elseif (auth()->user()->grupos->isEmpty())
    <div class="group-banner">
      <span>Você ainda não faz parte de um grupo. É preciso estar em um grupo para se candidatar.</span>
      <a href="{{ route('estudante.grupo.show') }}">Criar grupo →</a>
    </div>
  @endif

  <form method="GET" class="filter-form">
    <select name="area" onchange="this.form.submit()">
      <option value="">Todas as áreas</option>
      @foreach (['Tecnologia', 'Educação', 'Design', 'Saúde', 'Assistência social', 'Administração', 'Comunicação'] as $area)
        <option value="{{ $area }}" @selected(request('area') === $area)>{{ $area }}</option>
      @endforeach
    </select>
    <select name="nivel" onchange="this.form.submit()">
      <option value="">Todos os níveis</option>
      @foreach ([1, 2, 3] as $nivel)
        <option value="{{ $nivel }}" @selected(request('nivel') == $nivel)>Nível {{ $nivel }}</option>
      @endforeach
    </select>
  </form>

  <div class="demand-list">
    @forelse ($demandas as $demanda)
      <div class="demand-card {{ $grupoBloqueado ? 'is-locked' : '' }}">
        <div class="demand-main">
          <div class="demand-top">
            <span class="badge badge-aberta">Aberta p/ candidatura</span>
            <h3>{{ $demanda->titulo }}</h3>
          </div>
          <div class="demand-meta">
            <span>Nível {{ $demanda->nivel_complexidade }} · {{ $demanda->area_sugerida }}</span>
            <span>{{ $demanda->instituicao->nome }}</span>
            <span>{{ $demanda->horas_min }}–{{ $demanda->horas_max }}h</span>
            <span>{{ $demanda->candidaturas()->count() }} candidatura(s)</span>
          </div>
          @if ($demanda->prazoCandidaturaVencido())
            <div class="stopped-tag">
              Parada há {{ (int) $demanda->data_abertura_candidatura->diffInDays(now()) }} dias sem candidatura
            </div>
          @endif
        </div>
        <div class="demand-side">
          <a href="{{ route('estudante.demandas.show', $demanda) }}" class="go">Ver demanda →</a>
          @if ($grupoBloqueado)
            <div class="lock-note">🔒 Grupo indisponível</div>
          @endif
        </div>
      </div>
    @empty
      <div class="empty">Nenhuma demanda aberta para candidatura no momento.</div>
    @endforelse
  </div>
@endsection
