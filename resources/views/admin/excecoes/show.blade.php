@extends('layouts.dashboard')

@section('titulo', 'Resolver exceção — Elo')
@section('sidebar-role', 'Elo · Administração')
@section('sidebar-nav')
  @include('partials.sidebar.admin')
@endsection

@push('estilos')
<style>
  .content{ max-width: 760px; }
  .timeline{ margin-top: 4px; }
  .tl-item{ display: flex; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--border); font-size: 13.5px; }
  .tl-item:last-child{ border-bottom: none; }
  .tl-date{ flex-shrink: 0; width: 64px; color: var(--ink-soft); font-weight: 700; }
  .tl-text{ color: var(--ink); }
  .tl-item.risk .tl-text{ color: #6A2A18; }

  .hours-box{
    margin-top: 18px; padding: 14px 16px; background: #E1EADB; border-radius: 3px;
    display: flex; align-items: center; justify-content: space-between; font-size: 14px; gap: 12px;
  }
  .hours-box .num{ font-family: 'Source Serif 4', serif; font-size: 20px; font-weight: 700; color: #3D5433; }

  .radio-pick{ display: flex; flex-direction: column; gap: 10px; margin-top: 6px; }
  .radio-opt{
    border: 1.5px solid var(--border); border-radius: 3px; padding: 14px 16px; cursor: pointer;
    display: flex; gap: 12px; align-items: flex-start;
  }
  .radio-opt.active{ border-color: var(--primary); background: #E4EEEC; }
  .radio-opt input{ margin-top: 3px; accent-color: var(--primary); }
  .radio-opt .opt-title{ font-size: 14.5px; font-weight: 700; color: var(--ink); }
  .radio-opt .opt-desc{ font-size: 13px; color: var(--ink-soft); margin-top: 3px; }
  .form-actions{ margin-top: 22px; display: flex; gap: 12px; }
</style>
@endpush

@section('topbar')
  <a href="{{ route('admin.excecoes.index') }}" class="back">← Exceções</a>
  <h1>Milestone em risco</h1>
  <p class="sub">{{ $milestone->demanda->titulo }} · {{ $milestone->demanda->instituicao->nome }}</p>
@endsection

@section('content')
  @php
    $demanda = $milestone->demanda;
    $grupo = $demanda->grupoAtivo();
    $horasCreditadas = $demanda->milestones()->where('status', 'concluido')->sum('horas_creditadas');
    $auditorias = \App\Models\Auditoria::where('auditavel_type', \App\Models\Milestone::class)
        ->where('auditavel_id', $milestone->id)
        ->orderBy('created_at')
        ->get();
  @endphp

  <div class="card">
    <h2>Linha do tempo</h2>
    <div class="timeline">
      @foreach ($demanda->milestones()->where('status', 'concluido')->get() as $concluido)
        <div class="tl-item">
          <div class="tl-date">{{ $concluido->data_conclusao?->format('d/m') ?? '—' }}</div>
          <div class="tl-text">
            Milestone {{ $concluido->ordem }} concluído e validado. {{ $concluido->horas_creditadas }}h creditadas.
          </div>
        </div>
      @endforeach
      <div class="tl-item">
        <div class="tl-date">{{ $milestone->prazo->format('d/m') }}</div>
        <div class="tl-text">Prazo do milestone {{ $milestone->ordem }} definido.</div>
      </div>
      @foreach ($auditorias as $auditoria)
        <div class="tl-item {{ $auditoria->status_novo === 'em_risco' ? 'risk' : '' }}">
          <div class="tl-date">{{ $auditoria->created_at->format('d/m') }}</div>
          <div class="tl-text">
            Status alterado de <strong>{{ $auditoria->status_anterior ?? '—' }}</strong> para <strong>{{ $auditoria->status_novo }}</strong>.
            {{ $auditoria->observacao }}
          </div>
        </div>
      @endforeach
      <div class="tl-item risk">
        <div class="tl-date">{{ $milestone->updated_at->format('d/m') }}</div>
        <div class="tl-text">
          {{ (int) $milestone->updated_at->diffInDays(now()) }} dias em risco sem resposta do grupo — orientador e admin notificados.
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <h2>Horas do grupo original</h2>
    <p class="sub-h">
      As horas já creditadas ao grupo{{ $grupo ? ' "'.$grupo->nome.'"' : '' }} são preservadas, independente da decisão abaixo.
    </p>
    <div class="hours-box">
      <span>Horas já creditadas (milestones concluídos)</span>
      <span class="num">{{ $horasCreditadas }}h</span>
    </div>
  </div>

  <form method="POST" action="{{ route('admin.excecoes.resolver', $milestone) }}">
    @csrf
    <div class="card">
      <h2>O que fazer com o milestone remanescente?</h2>
      <div class="radio-pick">
        <label class="radio-opt active">
          <input type="radio" name="decisao" value="reabrir" checked>
          <div>
            <div class="opt-title">Reabrir para outro grupo</div>
            <div class="opt-desc">
              O milestone {{ $milestone->ordem }} (e os seguintes) volta para "aberta p/ candidatura".
              As {{ $horasCreditadas }}h do grupo original ficam garantidas no histórico dele.
            </div>
          </div>
        </label>
        <label class="radio-opt">
          <input type="radio" name="decisao" value="mais_prazo">
          <div>
            <div class="opt-title">Dar mais prazo ao grupo atual</div>
            <div class="opt-desc">Concede mais 7 dias ao grupo antes de decidir pela reabertura.</div>
          </div>
        </label>
        <label class="radio-opt">
          <input type="radio" name="decisao" value="abandonar">
          <div>
            <div class="opt-title">Marcar demanda como abandonada</div>
            <div class="opt-desc">Encerra a demanda sem concluir; a instituição pode cadastrar novamente depois.</div>
          </div>
        </label>
      </div>

      <div class="field">
        <label for="observacao">Observação (registrada na auditoria)</label>
        <textarea id="observacao" name="observacao" placeholder="Explique brevemente o motivo da decisão."></textarea>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Confirmar decisão</button>
        <a href="{{ route('admin.excecoes.index') }}" class="btn btn-outline">Cancelar</a>
      </div>
    </div>
  </form>
@endsection

@push('scripts')
<script>
  document.querySelectorAll('.radio-opt input').forEach(input => {
    input.addEventListener('change', () => {
      document.querySelectorAll('.radio-opt').forEach(o => o.classList.remove('active'));
      input.closest('.radio-opt').classList.add('active');
    });
  });
</script>
@endpush
