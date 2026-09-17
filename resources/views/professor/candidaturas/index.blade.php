@extends('layouts.dashboard')

@section('titulo', 'Candidaturas — Elo')
@section('sidebar-role', auth()->user()->departamento?->nome ? 'Departamento de '.auth()->user()->departamento->nome : 'Professor orientador')
@section('sidebar-nav')
  @include('partials.sidebar.professor')
@endsection

@push('estilos')
<style>
  .content{ max-width: 780px; }
  .candidatura-card{
    background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-card);
    padding: 20px 22px; margin-bottom: 14px;
  }
  .candidatura-top{ display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
  .candidatura-top h3{ font-size: 16px; }
  .candidatura-date{ font-size: 12.5px; color: var(--ink-soft); }
  .demand-tag{ font-size: 13px; color: var(--ink-soft); margin-top: 3px; }
  .group-members{ margin-top: 8px; font-size: 13.5px; color: var(--ink-soft); }
  .proposal{
    margin-top: 12px; padding: 12px 14px; background: var(--surface-alt); border-radius: 3px;
    font-size: 13.5px; color: var(--ink);
  }
  .candidatura-actions{ margin-top: 14px; display: flex; gap: 10px; flex-wrap: wrap; }
  .resolved-badge{ font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 3px; }
  .empty{ text-align: center; padding: 40px 20px; color: var(--ink-soft); font-size: 14.5px; }
</style>
@endpush

@section('topbar')
  <h1>Candidaturas</h1>
  <p class="sub">Todas as candidaturas pendentes das suas demandas, num só lugar.</p>
@endsection

@section('content')
  <div class="section-title">Aguardando sua análise</div>
  @forelse ($pendentes as $candidatura)
    <div class="candidatura-card">
      <div class="candidatura-top">
        <h3>Grupo "{{ $candidatura->grupo->nome }}"</h3>
        <span class="candidatura-date">Enviada {{ $candidatura->created_at->diffForHumans() }}</span>
      </div>
      <div class="demand-tag">{{ $candidatura->demanda->titulo }} · {{ $candidatura->demanda->instituicao->nome }}</div>
      <div class="group-members">{{ $candidatura->grupo->membros->pluck('nome')->implode(', ') }}</div>
      <div class="proposal">{{ $candidatura->mensagem }}</div>

      <div class="candidatura-actions">
        @if ($candidatura->demanda->isNivel3())
          <a href="{{ route('professor.candidaturas.show', $candidatura->demanda) }}" class="btn btn-approve btn-small">
            Definir milestones e aprovar →
          </a>
        @else
          <form method="POST" action="{{ route('professor.candidaturas.aprovar', $candidatura) }}">
            @csrf
            <button type="submit" class="btn btn-approve btn-small">Aprovar</button>
          </form>
        @endif
        <form method="POST" action="{{ route('professor.candidaturas.rejeitar', $candidatura) }}">
          @csrf
          <button type="submit" class="btn btn-reject btn-small">Rejeitar</button>
        </form>
        <a href="{{ route('professor.candidaturas.show', $candidatura->demanda) }}" class="btn btn-outline btn-small">Ver demanda</a>
      </div>
    </div>
  @empty
    <div class="empty">Nenhuma candidatura aguardando análise no momento.</div>
  @endforelse

  @if ($recentes->isNotEmpty())
    <div class="section-title">Respondidas recentemente</div>
    @foreach ($recentes as $candidatura)
      <div class="candidatura-card" style="opacity:0.65;">
        <div class="candidatura-top">
          <h3>Grupo "{{ $candidatura->grupo->nome }}"</h3>
          @if ($candidatura->status === 'aprovada')
            <span class="resolved-badge" style="background:#E1EADB;color:#3D5433;">Aprovado</span>
          @elseif ($candidatura->status === 'rejeitada_automatica')
            <span class="resolved-badge" style="background:var(--danger-soft);color:#6A2A18;">Rejeitado automaticamente</span>
          @else
            <span class="resolved-badge" style="background:var(--danger-soft);color:#6A2A18;">Rejeitado</span>
          @endif
        </div>
        <div class="demand-tag">{{ $candidatura->demanda->titulo }} · {{ $candidatura->demanda->instituicao->nome }}</div>
      </div>
    @endforeach
  @endif
@endsection
