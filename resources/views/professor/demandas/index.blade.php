@extends('layouts.dashboard')

@section('titulo', 'Minhas demandas — Elo')
@section('sidebar-role', auth()->user()->departamento?->nome ? 'Departamento de '.auth()->user()->departamento->nome : 'Professor orientador')
@section('sidebar-nav')
  @include('partials.sidebar.professor')
@endsection

@push('estilos')
<style>
  .demand-list{ display: flex; flex-direction: column; gap: 14px; }
  .demand-card{
    background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-card);
    padding: 20px 22px; display: flex; align-items: center; justify-content: space-between; gap: 20px;
  }
  .demand-card.urgent{ border-left: 3px solid var(--accent); }
  .demand-main{ min-width: 0; }
  .demand-top{ display: flex; align-items: center; gap: 10px; margin-bottom: 8px; flex-wrap: wrap; }
  .demand-card h3{ font-size: 17px; }
  .demand-meta{ margin-top: 6px; font-size: 13.5px; color: var(--ink-soft); display: flex; gap: 14px; flex-wrap: wrap; }
  .demand-meta strong{ color: var(--ink); }
  .demand-side{ text-align: right; flex-shrink: 0; }
  .demand-side .go{ font-size: 13.5px; font-weight: 700; color: var(--accent); }
  .demand-side .go:hover{ text-decoration: underline; }
  .empty{ text-align: center; padding: 40px 20px; color: var(--ink-soft); font-size: 14.5px; }

  @media (max-width: 760px){
    .demand-card{ flex-direction: column; align-items: flex-start; }
    .demand-side{ text-align: left; }
  }
</style>
@endpush

@section('topbar')
  <h1>Minhas demandas</h1>
  <p class="sub">Demandas do seu departamento atribuídas a você pela coordenação.</p>
@endsection

@section('content')
  @php
    $comCandidaturas = $demandas->filter(fn ($d) => $d->candidaturas->where('status', 'pendente')->isNotEmpty());
    $comMilestonePendente = $demandas->filter(function ($d) {
        return $d->milestones()->where('status', 'aguardando_validacao')
            ->whereHas('validacao', fn ($q) => $q->where('professor_aprovou', false))
            ->exists();
    });
    $precisamAcao = $comCandidaturas->merge($comMilestonePendente);
    $emAcompanhamento = $demandas->reject(fn ($d) => $precisamAcao->contains('id', $d->id));
    $totalCandidaturas = $demandas->sum(fn ($d) => $d->candidaturas->where('status', 'pendente')->count());
  @endphp

  <div class="stats">
    <div class="stat-card"><div class="num">{{ $demandas->count() }}</div><div class="label">Demandas atribuídas</div></div>
    <div class="stat-card {{ $totalCandidaturas > 0 ? 'alert' : '' }}">
      <div class="num">{{ $totalCandidaturas }}</div><div class="label">Candidaturas para revisar</div>
    </div>
    <div class="stat-card {{ $comMilestonePendente->count() > 0 ? 'alert' : '' }}">
      <div class="num">{{ $comMilestonePendente->count() }}</div><div class="label">Milestones aguardando validação</div>
    </div>
    <div class="stat-card">
      <div class="num">{{ $demandas->sum(fn ($d) => $d->milestones()->where('status', 'em_risco')->count()) }}</div>
      <div class="label">Milestones em risco</div>
    </div>
  </div>

  @if ($precisamAcao->isNotEmpty())
    <div class="section-title">Precisa da sua ação</div>
    <div class="demand-list">
      @foreach ($precisamAcao as $demanda)
        @php $pendentes = $demanda->candidaturas->where('status', 'pendente')->count(); @endphp
        <div class="demand-card urgent">
          <div class="demand-main">
            <div class="demand-top">
              <span class="badge badge-{{ $demanda->status === 'em_execucao' ? 'execucao' : 'aberta' }}">
                {{ $demanda->status === 'em_execucao' ? 'Em execução' : 'Aberta p/ candidatura' }}
              </span>
              <h3>{{ $demanda->titulo }}</h3>
            </div>
            <div class="demand-meta">
              <span>Nível {{ $demanda->nivel_complexidade }} · {{ $demanda->area_sugerida }}</span>
              <span>Instituição: {{ $demanda->instituicao->nome }}</span>
              @if ($pendentes > 0)
                <span><strong>{{ $pendentes }} candidatura(s)</strong> aguardando análise</span>
              @else
                <span><strong>Milestone</strong> aguardando sua validação</span>
              @endif
            </div>
          </div>
          <div class="demand-side">
            @if ($pendentes > 0)
              <a href="{{ route('professor.candidaturas.show', $demanda) }}" class="go">Revisar candidaturas →</a>
            @else
              <a href="{{ route('professor.milestones.index') }}" class="go">Validar milestone →</a>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  @endif

  <div class="section-title">Em acompanhamento</div>
  <div class="demand-list">
    @forelse ($emAcompanhamento as $demanda)
      @php
        $rotulos = [
          'aberta_candidatura' => ['Aberta p/ candidatura', 'aberta'],
          'em_execucao' => ['Em execução', 'execucao'],
          'concluida' => ['Concluída', 'concluida'],
          'abandonada' => ['Abandonada', 'abandonada'],
          'reaberta' => ['Reaberta', 'reaberta'],
        ];
        [$rotulo, $cor] = $rotulos[$demanda->status] ?? [$demanda->status, 'pendente'];
      @endphp
      <div class="demand-card">
        <div class="demand-main">
          <div class="demand-top">
            <span class="badge badge-{{ $cor }}">{{ $rotulo }}</span>
            <h3>{{ $demanda->titulo }}</h3>
          </div>
          <div class="demand-meta">
            <span>Nível {{ $demanda->nivel_complexidade }} · {{ $demanda->area_sugerida }}</span>
            <span>Instituição: {{ $demanda->instituicao->nome }}</span>
            @if ($grupo = $demanda->grupoAtivo())
              <span>Grupo: {{ $grupo->membros->count() }} estudantes</span>
            @endif
          </div>
        </div>
        <div class="demand-side">
          <a href="{{ route('professor.candidaturas.show', $demanda) }}" class="go">Ver detalhes →</a>
        </div>
      </div>
    @empty
      <div class="empty">Nenhuma demanda em acompanhamento.</div>
    @endforelse
  </div>
@endsection
