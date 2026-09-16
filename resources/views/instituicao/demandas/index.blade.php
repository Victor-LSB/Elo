@extends('layouts.dashboard')

@section('titulo', 'Minhas demandas — Elo')
@section('sidebar-role', 'Instituição parceira')
@section('sidebar-nav')
  @include('partials.sidebar.instituicao')
@endsection

@push('estilos')
<style>
  .topbar-conteudo{ display: flex; align-items: center; justify-content: space-between; gap: 20px; }
  .filters{ display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
  .filter-chip{
    padding: 7px 15px; border: 1.5px solid var(--border); border-radius: 20px;
    font-size: 13.5px; font-weight: 700; color: var(--ink-soft);
  }
  .filter-chip.active{ border-color: var(--primary); background: #E4EEEC; color: var(--primary); }

  .demand-list{ display: flex; flex-direction: column; gap: 14px; }
  .demand-card{
    background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-card);
    padding: 20px 22px; display: flex; align-items: center; justify-content: space-between; gap: 20px;
  }
  .demand-main{ min-width: 0; }
  .demand-top{ display: flex; align-items: center; gap: 10px; margin-bottom: 8px; flex-wrap: wrap; }
  .demand-card h3{ font-size: 17px; }
  .demand-meta{ margin-top: 6px; font-size: 13.5px; color: var(--ink-soft); display: flex; gap: 14px; flex-wrap: wrap; }
  .demand-side{ text-align: right; flex-shrink: 0; }
  .demand-side .go{ font-size: 13.5px; font-weight: 700; color: var(--accent); }
  .demand-side .go:hover{ text-decoration: underline; }
  .empty{ text-align: center; padding: 50px 20px; color: var(--ink-soft); font-size: 14.5px; }

  @media (max-width: 760px){
    .topbar-conteudo{ flex-direction: column; align-items: flex-start; }
    .demand-card{ flex-direction: column; align-items: flex-start; }
    .demand-side{ text-align: left; }
  }
</style>
@endpush

@section('topbar')
  <div>
    <h1>Minhas demandas</h1>
    <p class="sub">Acompanhe a triagem, o andamento e a validação de cada demanda cadastrada.</p>
  </div>
  <a href="{{ route('instituicao.demandas.create') }}" class="btn btn-primary">+ Nova demanda</a>
@endsection

@section('content')
  @php
    $rotulos = [
      'pendente_triagem' => ['Pendente triagem', 'pendente'],
      'em_revisao_adicional' => ['Em revisão adicional', 'risco'],
      'aberta_candidatura' => ['Aberta p/ candidatura', 'aberta'],
      'em_execucao' => ['Em execução', 'execucao'],
      'concluida' => ['Concluída', 'concluida'],
      'abandonada' => ['Abandonada', 'abandonada'],
      'reaberta' => ['Reaberta', 'reaberta'],
      'recusada' => ['Recusada', 'abandonada'],
    ];
  @endphp

  <div class="stats">
    <div class="stat-card">
      <div class="num">{{ $demandas->whereIn('status', ['aberta_candidatura', 'em_execucao', 'reaberta'])->count() }}</div>
      <div class="label">Demandas ativas</div>
    </div>
    <div class="stat-card">
      <div class="num">{{ $demandas->where('status', 'pendente_triagem')->count() }}</div>
      <div class="label">Aguardando triagem</div>
    </div>
    <div class="stat-card">
      <div class="num">{{ $demandas->where('status', 'em_execucao')->count() }}</div>
      <div class="label">Em execução</div>
    </div>
    <div class="stat-card">
      <div class="num">{{ $demandas->where('status', 'concluida')->count() }}</div>
      <div class="label">Concluídas</div>
    </div>
  </div>

  <div class="demand-list">
    @forelse ($demandas as $demanda)
      @php [$rotulo, $cor] = $rotulos[$demanda->status] ?? [$demanda->status, 'pendente']; @endphp
      <div class="demand-card">
        <div class="demand-main">
          <div class="demand-top">
            <span class="badge badge-{{ $cor }}">{{ $rotulo }}</span>
            <h3>{{ $demanda->titulo }}</h3>
          </div>
          <div class="demand-meta">
            <span>
              Nível {{ $demanda->nivel_complexidade ?? $demanda->nivel_estimado }}{{ $demanda->nivel_complexidade ? '' : ' (estimado)' }}
              · {{ $demanda->area_sugerida }}
            </span>
            @if ($demanda->departamento)
              <span>Departamento: {{ $demanda->departamento->nome }}</span>
            @endif
            @if ($demanda->status === 'aberta_candidatura')
              <span>{{ $demanda->candidaturas()->count() }} candidatura(s)</span>
            @endif
            @if ($demanda->status === 'em_execucao')
              @php
                $total = $demanda->milestones()->count();
                $feitos = $demanda->milestones()->where('status', 'concluido')->count();
              @endphp
              <span>Milestone {{ min($feitos + 1, $total) }} de {{ $total }}</span>
            @endif
          </div>
        </div>
        <div class="demand-side">
          <a href="{{ route('instituicao.demandas.show', $demanda) }}" class="go">Ver detalhes →</a>
        </div>
      </div>
    @empty
      <div class="empty">Você ainda não cadastrou nenhuma demanda.</div>
    @endforelse
  </div>
@endsection
