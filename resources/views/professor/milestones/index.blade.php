@extends('layouts.dashboard')

@section('titulo', 'Milestones — Elo')
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

  .milestone{ border: 1.5px solid var(--border); border-radius: 3px; padding: 18px 20px; margin-bottom: 14px; background: var(--surface); }
  .milestone.done{ border-color: var(--support); background: #F3F7F1; }
  .milestone.pending-action{ border-color: var(--accent); background: var(--accent-soft); }
  .milestone.risk{ border-color: var(--danger); background: #FBF1EC; }
  .milestone-top{ display: flex; align-items: center; justify-content: space-between; gap: 12px; }
  .milestone-top h3{ font-size: 15.5px; }
  .milestone-sub{ margin-top: 4px; font-size: 12.5px; color: var(--ink-soft); }
  .milestone-meta{ margin-top: 6px; font-size: 13px; color: var(--ink-soft); }
  .deliverable{ margin-top: 12px; padding: 12px 14px; background: var(--surface); border-radius: 3px; font-size: 13.5px; }
  .deliverable a{ color: var(--accent); font-weight: 700; }
  .deliverable a:hover{ text-decoration: underline; }
  .approval-row{ margin-top: 12px; display: flex; gap: 16px; font-size: 13px; color: var(--ink-soft); flex-wrap: wrap; }
  .dot{ width: 7px; height: 7px; border-radius: 50%; display: inline-block; margin-right: 5px; }
  .dot-ok{ background: var(--support); }
  .dot-wait{ background: var(--accent); }
  .milestone-actions{ margin-top: 14px; display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-start; }
  .ajustes-form textarea{
    padding: 9px 11px; font-size: 13.5px; font-family: 'Atkinson Hyperlegible', sans-serif;
    border: 1.5px solid var(--border); border-radius: 3px; background: var(--bg); color: var(--ink);
    min-height: 60px; width: 260px; max-width: 100%;
  }
  .empty{ text-align: center; padding: 40px 20px; color: var(--ink-soft); font-size: 14.5px; }
</style>
@endpush

@section('topbar')
  <a href="{{ route('professor.demandas.index') }}" class="back">← Minhas demandas</a>
  <h1>Milestones</h1>
  <p class="sub">Todas as etapas das demandas sob sua orientação.</p>
@endsection

@section('content')
  <div class="demand-brief">
    Em demandas de <b>Nível 3</b>, cada milestone precisa da sua aprovação <b>e</b> da aprovação da instituição para creditar horas ao grupo.
  </div>

  @forelse ($milestones as $milestone)
    @php
      $v = $milestone->validacao;
      $precisaAcao = $milestone->status === 'aguardando_validacao' && ! ($v?->professor_aprovou);
      $classe = $milestone->status === 'concluido' ? 'done' : ($milestone->status === 'em_risco' ? 'risk' : ($precisaAcao ? 'pending-action' : ''));
    @endphp
    <div class="milestone {{ $classe }}">
      <div class="milestone-top">
        <div>
          <h3>{{ $milestone->ordem }}. {{ $milestone->titulo }}</h3>
          <div class="milestone-sub">{{ $milestone->demanda->titulo }} · {{ $milestone->demanda->instituicao->nome }}</div>
        </div>
        <span class="badge badge-{{ $milestone->status === 'concluido' ? 'concluido' : ($milestone->status === 'em_risco' ? 'risco' : ($milestone->status === 'aguardando_validacao' ? 'andamento' : 'pendente')) }}">
          {{ match ($milestone->status) {
              'concluido' => 'Concluído',
              'em_risco' => 'Em risco',
              'aguardando_validacao' => $precisaAcao ? 'Aguardando sua validação' : 'Aguardando instituição',
              'em_andamento' => 'Em andamento',
              default => 'Não iniciado',
          } }}
        </span>
      </div>

      <div class="milestone-meta">
        {{ $milestone->horas_creditadas }}h · prazo {{ $milestone->prazo->format('d/m/Y') }}
        @if ($milestone->data_entrega)
          · entrega anexada em {{ $milestone->data_entrega->format('d/m/Y') }}
        @endif
      </div>

      @if ($milestone->entrega_path)
        <div class="deliverable">
          📎 <a href="{{ Storage::url($milestone->entrega_path) }}">Ver entrega anexada pelo grupo</a>
        </div>
      @endif

      @if ($milestone->status === 'aguardando_validacao')
        <div class="approval-row">
          <span>
            <span class="dot {{ $v?->professor_aprovou ? 'dot-ok' : 'dot-wait' }}"></span>
            Sua aprovação: {{ $v?->professor_aprovou ? 'concluída' : 'pendente' }}
          </span>
          <span>
            <span class="dot {{ $v?->instituicao_aprovou ? 'dot-ok' : 'dot-wait' }}"></span>
            Aprovação da instituição: {{ $v?->instituicao_aprovou ? 'concluída' : 'pendente' }}
          </span>
        </div>

        @if ($precisaAcao)
          <div class="milestone-actions">
            <form method="POST" action="{{ route('professor.milestones.validar', $milestone) }}">
              @csrf
              <button type="submit" class="btn btn-approve btn-small">Aprovar milestone</button>
            </form>
            <form method="POST" action="{{ route('professor.milestones.ajustes', $milestone) }}" class="ajustes-form">
              @csrf
              <textarea name="comentario" placeholder="O que precisa ser ajustado?" required></textarea>
              <button type="submit" class="btn btn-outline btn-small" style="margin-top:8px;">Pedir ajustes</button>
            </form>
          </div>
        @else
          <div style="margin-top:12px;font-size:13.5px;color:var(--support);font-weight:700;">
            ✓ Você aprovou. Aguardando a instituição para creditar as horas.
          </div>
        @endif
      @endif
    </div>
  @empty
    <div class="empty">Nenhum milestone nas suas demandas ainda.</div>
  @endforelse
@endsection
