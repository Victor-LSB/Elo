@extends('layouts.dashboard')

@section('titulo', 'Candidaturas — Elo')
@section('sidebar-role', auth()->user()->departamento?->nome ? 'Departamento de '.auth()->user()->departamento->nome : 'Professor orientador')
@section('sidebar-nav')
  @include('partials.sidebar.professor')
@endsection

@push('estilos')
<style>
  .content{ max-width: 760px; }
  .demand-brief{
    background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-card);
    padding: 18px 20px; margin-bottom: 24px; font-size: 14px; color: var(--ink-soft);
  }
  .demand-brief b{ color: var(--ink); }

  .candidatura-card{
    background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-card);
    padding: 22px 24px; margin-bottom: 16px;
  }
  .candidatura-card.resolved{ opacity: 0.6; }
  .candidatura-top{ display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 4px; }
  .candidatura-top h3{ font-size: 16px; }
  .candidatura-date{ font-size: 12.5px; color: var(--ink-soft); }
  .group-members{ margin-top: 6px; font-size: 13.5px; color: var(--ink-soft); }
  .proposal{
    margin-top: 14px; padding: 14px 16px; background: var(--surface-alt); border-radius: 3px;
    font-size: 14px; color: var(--ink);
  }
  .proposal-label{ font-size: 12px; font-weight: 700; color: var(--ink-soft); text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 6px; }
  .candidatura-actions{ margin-top: 16px; display: flex; gap: 10px; align-items: flex-start; }
  .resolved-badge{ font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 3px; }

  .milestones-box{ margin-top: 16px; padding: 16px; border: 1.5px dashed var(--border); border-radius: 3px; }
  .milestones-box h4{ font-size: 13.5px; margin: 0 0 4px; font-family: 'Atkinson Hyperlegible', sans-serif; }
  .milestone-linha{ display: grid; grid-template-columns: 2fr 0.7fr 1fr; gap: 8px; margin-top: 10px; }
  .milestone-linha input{
    padding: 9px 11px; font-size: 13.5px; font-family: 'Atkinson Hyperlegible', sans-serif;
    border: 1.5px solid var(--border); border-radius: 3px; background: var(--bg); color: var(--ink); width: 100%;
  }
  .empty{ text-align: center; padding: 40px 20px; color: var(--ink-soft); font-size: 14.5px; }

  @media (max-width: 760px){
    .candidatura-actions{ flex-direction: column; }
    .milestone-linha{ grid-template-columns: 1fr; }
  }
</style>
@endpush

@section('topbar')
  <a href="{{ route('professor.demandas.index') }}" class="back">← Minhas demandas</a>
  <h1>Candidaturas</h1>
  <p class="sub">{{ $demanda->titulo }} · {{ $demanda->instituicao->nome }}</p>
@endsection

@section('content')
  <div class="demand-brief">
    <b>Nível {{ $demanda->nivel_complexidade }} · {{ $demanda->area_sugerida }}</b> — {{ $demanda->descricao }}
    Faixa: {{ $demanda->horas_min }}–{{ $demanda->horas_max }}h.
    Apenas um grupo será aprovado; os demais são rejeitados automaticamente.
  </div>

  @forelse ($demanda->candidaturas as $candidatura)
    <div class="candidatura-card {{ $candidatura->status === 'pendente' ? '' : 'resolved' }}">
      <div class="candidatura-top">
        <h3>Grupo "{{ $candidatura->grupo->nome }}"</h3>
        <span class="candidatura-date">Enviada {{ $candidatura->created_at->diffForHumans() }}</span>
      </div>
      <div class="group-members">
        {{ $candidatura->grupo->membros->pluck('nome')->implode(', ') }}
      </div>
      <div class="proposal">
        <div class="proposal-label">Proposta do grupo</div>
        {{ $candidatura->mensagem }}
      </div>

      <div class="candidatura-actions">
        @if ($candidatura->status === 'pendente')
          <form method="POST" action="{{ route('professor.candidaturas.aprovar', $candidatura) }}" style="flex:1;">
            @csrf
            @if ($demanda->isNivel3())
              <div class="milestones-box">
                <h4>Milestones da demanda (obrigatório para Nível 3)</h4>
                <p style="font-size:12.5px;">Defina título, horas e prazo de cada etapa. Elas são criadas ao aprovar o grupo.</p>
                @for ($i = 0; $i < 3; $i++)
                  <div class="milestone-linha">
                    <input type="text" name="milestones[{{ $i }}][titulo]" placeholder="Título do milestone {{ $i + 1 }}" {{ $i === 0 ? 'required' : '' }}>
                    <input type="number" name="milestones[{{ $i }}][horas_creditadas]" placeholder="Horas" min="1" {{ $i === 0 ? 'required' : '' }}>
                    <input type="date" name="milestones[{{ $i }}][prazo]" {{ $i === 0 ? 'required' : '' }}>
                  </div>
                @endfor
              </div>
            @endif
            <button type="submit" class="btn btn-approve" style="margin-top:14px;">Aprovar grupo</button>
          </form>

          <form method="POST" action="{{ route('professor.candidaturas.rejeitar', $candidatura) }}">
            @csrf
            <button type="submit" class="btn btn-reject">Rejeitar</button>
          </form>
        @elseif ($candidatura->status === 'aprovada')
          <span class="resolved-badge" style="background:#E1EADB;color:#3D5433;">Aprovado — demanda em execução</span>
        @elseif ($candidatura->status === 'rejeitada_automatica')
          <span class="resolved-badge" style="background:var(--danger-soft);color:#6A2A18;">Rejeitado automaticamente</span>
        @else
          <span class="resolved-badge" style="background:var(--danger-soft);color:#6A2A18;">Rejeitado</span>
        @endif
      </div>
    </div>
  @empty
    <div class="empty">Nenhuma candidatura recebida ainda.</div>
  @endforelse
@endsection
