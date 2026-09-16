@extends('layouts.dashboard')

@section('titulo', 'Professores — Elo')
@section('sidebar-role', 'Coordenação de extensão')
@section('sidebar-nav')
  @include('partials.sidebar.coordenacao')
@endsection

@push('estilos')
<style>
  .content{ max-width: 820px; }
  .prof-card{
    background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-card);
    padding: 16px 20px; margin-bottom: 12px;
    display: flex; align-items: center; justify-content: space-between; gap: 16px;
  }
  .prof-card h3{ font-size: 15.5px; }
  .prof-meta{ font-size: 13px; color: var(--ink-soft); margin-top: 3px; }
  .prof-counts{ display: flex; gap: 18px; text-align: center; }
  .prof-counts .item .n{ font-size: 18px; font-weight: 700; color: var(--ink); }
  .prof-counts .item .l{ font-size: 11px; color: var(--ink-soft); text-transform: uppercase; letter-spacing: 0.03em; }
  .empty{ text-align: center; padding: 40px 20px; color: var(--ink-soft); font-size: 14.5px; }

  @media (max-width: 760px){
    .prof-card{ flex-direction: column; align-items: flex-start; }
  }
</style>
@endpush

@section('topbar')
  <h1>Professores</h1>
  <p class="sub">Professores cadastrados na plataforma e a carga de demandas de cada um.</p>
@endsection

@section('content')
  @forelse ($professores as $professor)
    <div class="prof-card">
      <div>
        <h3>{{ $professor->nome }}</h3>
        <div class="prof-meta">
          {{ $professor->email }}
          @if ($professor->departamento)
            · {{ $professor->departamento->nome }}
          @endif
        </div>
      </div>
      <div class="prof-counts">
        <div class="item">
          <div class="n">{{ $professor->demandas_ativas_count }}</div>
          <div class="l">Ativas</div>
        </div>
        <div class="item">
          <div class="n">{{ $professor->demandas_total_count }}</div>
          <div class="l">Total</div>
        </div>
      </div>
    </div>
  @empty
    <div class="empty">Nenhum professor cadastrado ainda.</div>
  @endforelse
@endsection
