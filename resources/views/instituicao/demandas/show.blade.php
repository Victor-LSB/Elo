@extends('layouts.dashboard')

@section('titulo', $demanda->titulo.' — Elo')
@section('sidebar-role', 'Instituição parceira')
@section('sidebar-nav')
  @include('partials.sidebar.instituicao')
@endsection

@push('estilos')
<style>
  .content{ max-width: 880px; }
  .layout{ display: grid; grid-template-columns: 1.6fr 1fr; gap: 22px; }
  .desc-text{ font-size: 14.5px; }
  .meta-grid{ display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 16px; }
  .meta-item .k{ font-size: 12px; font-weight: 700; color: var(--ink-soft); text-transform: uppercase; letter-spacing: 0.03em; }
  .meta-item .v{ margin-top: 3px; font-size: 14.5px; color: var(--ink); }

  .milestone{ border: 1.5px solid var(--border); border-radius: 3px; padding: 16px 18px; margin-bottom: 12px; }
  .milestone.done{ border-color: var(--support); background: #F3F7F1; }
  .milestone.risk{ border-color: var(--danger); background: #FBF1EC; }
  .milestone-top{ display: flex; align-items: center; justify-content: space-between; gap: 12px; }
  .milestone-top h3{ font-size: 15px; }
  .milestone-meta{ margin-top: 6px; font-size: 13px; color: var(--ink-soft); }
  .approval-note{ margin-top: 10px; font-size: 12.5px; color: var(--ink-soft); }
  .dot{ width: 7px; height: 7px; border-radius: 50%; display: inline-block; margin-right: 6px; }
  .dot-ok{ background: var(--support); }
  .dot-wait{ background: var(--accent); }
  .milestone-actions{ margin-top: 14px; display: flex; gap: 10px; flex-wrap: wrap; }

  .group-list{ display: flex; flex-direction: column; gap: 10px; }
  .group-member{ display: flex; align-items: center; gap: 10px; font-size: 14px; }
  .avatar{
    width: 30px; height: 30px; border-radius: 50%; background: #E4EEEC; color: var(--primary-dark);
    display: flex; align-items: center; justify-content: center; font-size: 12.5px; font-weight: 700; flex-shrink: 0;
  }
  .empty-inline{ font-size: 13.5px; color: var(--ink-soft); }

  @media (max-width: 900px){ .layout{ grid-template-columns: 1fr; } }
  @media (max-width: 760px){ .meta-grid{ grid-template-columns: 1fr; } }
</style>
@endpush

@section('topbar')
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
    [$rotulo, $cor] = $rotulos[$demanda->status] ?? [$demanda->status, 'pendente'];
  @endphp
  <a href="{{ route('instituicao.demandas.index') }}" class="back">← Minhas demandas</a>
  <h1>{{ $demanda->titulo }}</h1>
  <span class="badge badge-{{ $cor }}" style="margin-top:8px;">{{ $rotulo }}</span>
@endsection

@section('content')
  <div class="layout">
    <div>
      <div class="card">
        <h2>Descrição</h2>
        <p class="desc-text">{{ $demanda->descricao }}</p>
        <div class="meta-grid">
          <div class="meta-item">
            <div class="k">Nível</div>
            <div class="v">{{ $demanda->nivel_complexidade ?? $demanda->nivel_estimado }}{{ $demanda->nivel_complexidade ? '' : ' (estimado)' }}</div>
          </div>
          <div class="meta-item"><div class="k">Departamento</div><div class="v">{{ $demanda->departamento?->nome ?? '—' }}</div></div>
          <div class="meta-item">
            <div class="k">Faixa de horas</div>
            <div class="v">{{ $demanda->horas_min ? $demanda->horas_min.' – '.$demanda->horas_max.'h' : '—' }}</div>
          </div>
          <div class="meta-item"><div class="k">Professor responsável</div><div class="v">{{ $demanda->professor?->nome ?? '—' }}</div></div>
        </div>
      </div>

      @if ($demanda->milestones->isNotEmpty())
        <div class="card">
          <h2>Milestones</h2>
          @foreach ($demanda->milestones as $milestone)
            <div class="milestone {{ $milestone->status === 'concluido' ? 'done' : ($milestone->status === 'em_risco' ? 'risk' : '') }}">
              <div class="milestone-top">
                <h3>{{ $milestone->ordem }}. {{ $milestone->titulo }}</h3>
                <span class="badge badge-{{ $milestone->status === 'concluido' ? 'concluido' : ($milestone->status === 'em_risco' ? 'risco' : ($milestone->status === 'aguardando_validacao' ? 'andamento' : 'pendente')) }}">
                  {{ match ($milestone->status) {
                      'concluido' => 'Concluído',
                      'em_risco' => 'Em risco',
                      'aguardando_validacao' => 'Aguardando validação',
                      'em_andamento' => 'Em andamento',
                      default => 'Não iniciado',
                  } }}
                </span>
              </div>
              <div class="milestone-meta">
                {{ $milestone->horas_creditadas }}h · prazo {{ $milestone->prazo->format('d/m/Y') }}
                @if ($milestone->data_conclusao)
                  · concluído em {{ $milestone->data_conclusao->format('d/m/Y') }}
                @endif
              </div>

              @if ($milestone->status === 'aguardando_validacao')
                @php $v = $milestone->validacao; @endphp
                <div class="approval-note">
                  <span class="dot {{ $v?->professor_aprovou ? 'dot-ok' : 'dot-wait' }}"></span>
                  Professor: {{ $v?->professor_aprovou ? 'aprovado' : 'pendente' }}
                </div>
                <div class="approval-note">
                  <span class="dot {{ $v?->instituicao_aprovou ? 'dot-ok' : 'dot-wait' }}"></span>
                  Instituição: {{ $v?->instituicao_aprovou ? 'aprovado' : 'pendente' }}
                </div>

                <div class="milestone-actions">
                  @unless ($v?->instituicao_aprovou)
                    <form method="POST" action="{{ route('instituicao.milestones.validar', $milestone) }}">
                      @csrf
                      <button type="submit" class="btn btn-primary btn-small">Validar entrega</button>
                    </form>
                  @endunless
                  @if ($milestone->entrega_path)
                    <a href="{{ Storage::url($milestone->entrega_path) }}" class="btn btn-outline btn-small">Ver relatório anexado</a>
                  @endif
                </div>
              @endif
            </div>
          @endforeach
        </div>
      @endif
    </div>

    <div>
      <div class="card">
        <h2>Grupo responsável</h2>
        @php $grupo = $demanda->grupoAtivo(); @endphp
        @if ($grupo)
          <div class="group-list">
            @foreach ($grupo->membros as $membro)
              <div class="group-member">
                <span class="avatar">{{ collect(explode(' ', $membro->nome))->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode('') }}</span>
                {{ $membro->nome }} — {{ $membro->curso }}
              </div>
            @endforeach
          </div>
        @else
          <p class="empty-inline">Nenhum grupo aprovado ainda.</p>
        @endif
      </div>

      @if ($demanda->checklistEtico)
        <div class="card">
          <h2>Checklist ético</h2>
          <p class="empty-inline">
            Substitui serviço profissional:
            <strong>{{ $demanda->checklistEtico->substitui_servico_profissional ? 'Sim' : 'Não' }}</strong>
          </p>
          @if ($demanda->checklistEtico->justificativa)
            <p class="empty-inline" style="margin-top:8px;">{{ $demanda->checklistEtico->justificativa }}</p>
          @endif
        </div>
      @endif
    </div>
  </div>
@endsection
