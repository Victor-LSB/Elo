@extends('layouts.dashboard')

@section('titulo', 'Minhas horas — Elo')
@section('sidebar-role', auth()->user()->curso)
@section('sidebar-nav')
  @include('partials.sidebar.estudante')
@endsection

@push('estilos')
<style>
  .content{ max-width: 780px; }
  .total-box{
    background: var(--surface); border: 1px solid var(--border); border-left: 3px solid var(--support);
    border-radius: var(--radius-card); padding: 22px 24px; margin-bottom: 26px;
    display: flex; align-items: center; justify-content: space-between; gap: 16px;
  }
  .total-box .num{ font-family: 'Source Serif 4', serif; font-size: 34px; font-weight: 700; color: var(--primary-dark); }
  .total-box .label{ font-size: 14px; color: var(--ink-soft); }

  .milestone{ border: 1.5px solid var(--border); border-radius: 3px; padding: 16px 18px; margin-bottom: 12px; }
  .milestone.done{ border-color: var(--support); background: #F3F7F1; }
  .milestone.risk{ border-color: var(--danger); background: #FBF1EC; }
  .milestone-top{ display: flex; align-items: center; justify-content: space-between; gap: 12px; }
  .milestone-top h3{ font-size: 15px; }
  .milestone-meta{ margin-top: 6px; font-size: 13px; color: var(--ink-soft); }
  .approval-row{ margin-top: 10px; display: flex; gap: 16px; font-size: 12.5px; color: var(--ink-soft); flex-wrap: wrap; }
  .dot{ width: 7px; height: 7px; border-radius: 50%; display: inline-block; margin-right: 5px; }
  .dot-ok{ background: var(--support); }
  .dot-wait{ background: var(--accent); }
  .upload-row{ margin-top: 14px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
  .upload-row input[type=file]{ font-size: 13px; font-family: inherit; }

  .cert-list{ display: flex; flex-direction: column; gap: 12px; }
  .cert-card{
    background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-card);
    padding: 18px 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px;
  }
  .cert-card h3{ font-size: 15.5px; }
  .cert-meta{ margin-top: 4px; font-size: 13px; color: var(--ink-soft); display: flex; gap: 12px; flex-wrap: wrap; }
  .cert-hours{ font-weight: 700; color: var(--support); }
  .empty{ text-align: center; padding: 40px 20px; color: var(--ink-soft); font-size: 14.5px; }

  @media (max-width: 760px){ .cert-card{ flex-direction: column; align-items: flex-start; } }
</style>
@endpush

@section('topbar')
  <h1>Minhas horas</h1>
  <p class="sub">Acompanhe o progresso da sua demanda ativa e os certificados já emitidos.</p>
@endsection

@section('content')
  @php $totalHoras = $certificados->sum('horas_totais'); @endphp

  <div class="total-box">
    <span class="label">Horas complementares creditadas</span>
    <span class="num">{{ $totalHoras }}h</span>
  </div>

  <div class="section-title">Demanda em andamento</div>
  @if ($demanda)
    <div class="card">
      <h2>{{ $demanda->titulo }}</h2>
      <p class="sub-h">{{ $demanda->instituicao->nome }} · Nível {{ $demanda->nivel_complexidade }}</p>

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

          @if ($milestone->status === 'aguardando_validacao' && $milestone->validacao)
            <div class="approval-row">
              <span>
                <span class="dot {{ $milestone->validacao->professor_aprovou ? 'dot-ok' : 'dot-wait' }}"></span>
                Professor: {{ $milestone->validacao->professor_aprovou ? 'aprovado' : 'pendente' }}
              </span>
              <span>
                <span class="dot {{ $milestone->validacao->instituicao_aprovou ? 'dot-ok' : 'dot-wait' }}"></span>
                Instituição: {{ $milestone->validacao->instituicao_aprovou ? 'aprovado' : 'pendente' }}
              </span>
            </div>
          @endif

          @if (in_array($milestone->status, ['em_andamento', 'em_risco']))
            <form method="POST" action="{{ route('estudante.milestones.entrega', $milestone) }}" enctype="multipart/form-data" class="upload-row">
              @csrf
              <input type="file" name="entrega" required>
              <button type="submit" class="btn btn-primary btn-small">Enviar entrega</button>
            </form>
          @endif
        </div>
      @endforeach
    </div>
  @else
    <div class="empty">Você não tem nenhuma demanda em andamento no momento.</div>
  @endif

  <div class="section-title">Certificados emitidos</div>
  <div class="cert-list">
    @forelse ($certificados as $certificado)
      <div class="cert-card">
        <div>
          <h3>{{ $certificado->demanda->titulo }}</h3>
          <div class="cert-meta">
            <span>{{ $certificado->demanda->instituicao->nome }}</span>
            <span class="cert-hours">{{ $certificado->horas_totais }}h</span>
            <span>Emitido em {{ $certificado->data_emissao->format('d/m/Y') }}</span>
          </div>
        </div>
        @if ($certificado->pdf_path)
          <a href="{{ Storage::url($certificado->pdf_path) }}" class="btn btn-outline btn-small">↓ Baixar PDF</a>
        @endif
      </div>
    @empty
      <div class="empty">Nenhum certificado emitido ainda.</div>
    @endforelse
  </div>
@endsection
