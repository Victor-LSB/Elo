@extends('layouts.dashboard')

@section('titulo', 'Exceções — Elo')
@section('sidebar-role', 'Elo · Administração')
@section('sidebar-nav')
  @include('partials.sidebar.admin')
@endsection

@push('estilos')
<style>
  .stats{ grid-template-columns: repeat(5, 1fr); gap: 14px; }
  .stat-card{ padding: 16px 18px; }
  .stat-card .num{ font-size: 26px; }
  .stat-card.alert{ border-color: var(--danger); background: var(--danger-soft); }
  .stat-card.alert .num{ color: #6A2A18; }
  .stat-card .label{ font-size: 12.5px; }

  .exc-list{ display: flex; flex-direction: column; gap: 14px; }
  .exc-card{
    background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-card);
    padding: 20px 22px; display: flex; align-items: center; justify-content: space-between; gap: 20px;
  }
  .exc-card.risk{ border-left: 3px solid var(--danger); }
  .exc-main{ min-width: 0; }
  .exc-top{ display: flex; align-items: center; gap: 10px; margin-bottom: 8px; flex-wrap: wrap; }
  .exc-card h3{ font-size: 16.5px; }
  .exc-meta{ margin-top: 6px; font-size: 13.5px; color: var(--ink-soft); display: flex; gap: 14px; flex-wrap: wrap; }
  .exc-side{ text-align: right; flex-shrink: 0; }
  .exc-side .go{ font-size: 13.5px; font-weight: 700; color: var(--accent); }
  .exc-side .go:hover{ text-decoration: underline; }

  .quick-row{ display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
  .quick-card{
    background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-card); padding: 20px 22px;
  }
  .quick-card h3{ font-size: 15.5px; margin-bottom: 6px; }
  .quick-card p{ font-size: 13.5px; }
  .quick-card a.go{ display: inline-block; margin-top: 12px; font-size: 13.5px; font-weight: 700; color: var(--accent); }
  .quick-card a.go:hover{ text-decoration: underline; }
  .empty{ text-align: center; padding: 40px 20px; color: var(--ink-soft); font-size: 14.5px; }

  @media (max-width: 1100px){ .stats{ grid-template-columns: repeat(3, 1fr); } }
  @media (max-width: 760px){
    .stats{ grid-template-columns: 1fr 1fr; }
    .exc-card{ flex-direction: column; align-items: flex-start; }
    .exc-side{ text-align: left; }
    .quick-row{ grid-template-columns: 1fr; }
  }
</style>
@endpush

@section('topbar')
  <h1>Exceções do sistema</h1>
  <p class="sub">Milestones em risco e reaberturas que precisam de decisão manual.</p>
@endsection

@section('content')
  <div class="stats">
    <div class="stat-card">
      <div class="num">{{ \App\Models\Demanda::whereIn('status', ['aberta_candidatura', 'em_execucao', 'reaberta'])->count() }}</div>
      <div class="label">Demandas ativas</div>
    </div>
    <div class="stat-card">
      <div class="num">{{ \App\Models\User::where('papel', 'estudante')->count() }}</div>
      <div class="label">Estudantes cadastrados</div>
    </div>
    <div class="stat-card">
      <div class="num">{{ \App\Models\Instituicao::where('status', 'ativa')->count() }}</div>
      <div class="label">Instituições ativas</div>
    </div>
    <div class="stat-card {{ $milestonesEmRisco->count() > 0 ? 'alert' : '' }}">
      <div class="num">{{ $milestonesEmRisco->count() }}</div>
      <div class="label">Milestones em risco</div>
    </div>
    <div class="stat-card">
      <div class="num">{{ \App\Models\Demanda::where('status', 'abandonada')->count() }}</div>
      <div class="label">Demandas abandonadas</div>
    </div>
  </div>

  <div class="section-title">Precisa da sua decisão</div>
  <div class="exc-list">
    @forelse ($milestonesEmRisco as $milestone)
      @php $grupo = $milestone->demanda->grupoAtivo(); @endphp
      <div class="exc-card risk">
        <div class="exc-main">
          <div class="exc-top">
            <span class="badge badge-risco">
              Em risco há {{ (int) $milestone->updated_at->diffInDays(now()) }} dias
            </span>
            <h3>Milestone {{ $milestone->ordem }} — {{ $milestone->demanda->titulo }}</h3>
          </div>
          <div class="exc-meta">
            <span>Instituição: {{ $milestone->demanda->instituicao->nome }}</span>
            @if ($grupo)
              <span>Grupo: "{{ $grupo->nome }}"</span>
            @endif
            <span>
              {{ $milestone->demanda->milestones()->where('status', 'concluido')->sum('horas_creditadas') }}h
              já creditadas ao grupo
            </span>
          </div>
        </div>
        <div class="exc-side">
          <a href="{{ route('admin.excecoes.show', $milestone) }}" class="go">Resolver →</a>
        </div>
      </div>
    @empty
      <div class="empty">Nenhuma exceção pendente. Tudo em ordem.</div>
    @endforelse
  </div>

  <div class="section-title">Acesso rápido</div>
  <div class="quick-row">
    <div class="quick-card">
      <h3>Criar conta de professor ou coordenação</h3>
      <p>Esses papéis não se autocadastram — a conta é criada aqui pelo admin.</p>
      <a href="{{ route('admin.configuracoes.index') }}" class="go">Ir para configurações →</a>
    </div>
    <div class="quick-card">
      <h3>Prazos e jobs agendados</h3>
      <p>Prazo padrão de candidatura e período até marcar milestone "em risco".</p>
      <a href="{{ route('admin.configuracoes.index') }}" class="go">Ajustar parâmetros →</a>
    </div>
  </div>
@endsection
