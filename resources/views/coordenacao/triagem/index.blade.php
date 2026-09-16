@extends('layouts.dashboard')

@section('titulo', 'Fila de triagem — Elo')
@section('sidebar-role', 'Coordenação de extensão')
@section('sidebar-nav')
  @include('partials.sidebar.coordenacao')
@endsection

@push('estilos')
<style>
  .demand-list{ display: flex; flex-direction: column; gap: 14px; }
  .demand-card{
    background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-card);
    padding: 20px 22px; display: flex; align-items: center; justify-content: space-between; gap: 20px;
  }
  .demand-card.urgent{ border-left: 3px solid var(--accent); }
  .demand-card.revisao{ border-left: 3px solid var(--danger); }
  .demand-main{ min-width: 0; }
  .demand-top{ display: flex; align-items: center; gap: 10px; margin-bottom: 8px; flex-wrap: wrap; }
  .demand-card h3{ font-size: 17px; }
  .demand-meta{ margin-top: 6px; font-size: 13.5px; color: var(--ink-soft); display: flex; gap: 14px; flex-wrap: wrap; }
  .demand-side{ text-align: right; flex-shrink: 0; }
  .demand-side .go{ font-size: 13.5px; font-weight: 700; color: var(--accent); }
  .demand-side .go:hover{ text-decoration: underline; }
  .empty{ text-align: center; padding: 50px 20px; color: var(--ink-soft); font-size: 14.5px; }

  @media (max-width: 760px){
    .demand-card{ flex-direction: column; align-items: flex-start; }
    .demand-side{ text-align: left; }
  }
</style>
@endpush

@section('topbar')
  <h1>Fila de triagem</h1>
  <p class="sub">Valide demandas recebidas, defina nível final, departamento e aplique o checklist ético antes de publicar.</p>
@endsection

@section('content')
  <div class="stats">
    <div class="stat-card {{ $fila->where('status', 'pendente_triagem')->count() > 0 ? 'alert' : '' }}">
      <div class="num">{{ $fila->where('status', 'pendente_triagem')->count() }}</div>
      <div class="label">Demandas aguardando triagem</div>
    </div>
    <div class="stat-card">
      <div class="num">{{ $fila->where('status', 'em_revisao_adicional')->count() }}</div>
      <div class="label">Em revisão adicional</div>
    </div>
    <div class="stat-card">
      <div class="num">{{ \App\Models\Instituicao::where('status', 'pendente')->count() }}</div>
      <div class="label">Instituições pendentes</div>
    </div>
    <div class="stat-card">
      <div class="num">{{ \App\Models\Demanda::whereIn('status', ['aberta_candidatura', 'em_execucao', 'reaberta'])->count() }}</div>
      <div class="label">Demandas ativas no sistema</div>
    </div>
  </div>

  <div class="section-title">Demandas aguardando triagem</div>
  <div class="demand-list">
    @forelse ($fila as $demanda)
      @php $emRevisao = $demanda->status === 'em_revisao_adicional'; @endphp
      <div class="demand-card {{ $emRevisao ? 'revisao' : 'urgent' }}">
        <div class="demand-main">
          <div class="demand-top">
            <span class="badge badge-{{ $emRevisao ? 'risco' : 'pendente' }}">
              {{ $emRevisao ? 'Em revisão adicional' : 'Pendente triagem' }}
            </span>
            <h3>{{ $demanda->titulo }}</h3>
          </div>
          <div class="demand-meta">
            <span>{{ $demanda->instituicao->nome }}</span>
            <span>Nível estimado: {{ $demanda->nivel_estimado }}</span>
            <span>Enviada {{ $demanda->created_at->diffForHumans() }}</span>
          </div>
        </div>
        <div class="demand-side">
          <a href="{{ route('coordenacao.triagem.show', $demanda) }}" class="go">
            {{ $emRevisao ? 'Revisar →' : 'Fazer triagem →' }}
          </a>
        </div>
      </div>
    @empty
      <div class="empty">Nenhuma demanda aguardando triagem no momento.</div>
    @endforelse
  </div>
@endsection
