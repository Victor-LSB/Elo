@extends('layouts.dashboard')

@section('titulo', 'Instituições — Elo')
@section('sidebar-role', 'Coordenação de extensão')
@section('sidebar-nav')
  @include('partials.sidebar.coordenacao')
@endsection

@push('estilos')
<style>
  .content{ max-width: 820px; }
  .inst-card{
    background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-card);
    padding: 22px 24px; margin-bottom: 14px;
  }
  .inst-card.pending{ border-left: 3px solid var(--accent); }
  .inst-top{ display: flex; align-items: center; justify-content: space-between; gap: 12px; }
  .inst-top h3{ font-size: 17px; }
  .inst-desc{ margin-top: 10px; font-size: 14px; }
  .meta-grid{ display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 14px; }
  .meta-item .k{ font-size: 12px; font-weight: 700; color: var(--ink-soft); text-transform: uppercase; letter-spacing: 0.03em; }
  .meta-item .v{ margin-top: 3px; font-size: 14px; color: var(--ink); }
  .inst-actions{ margin-top: 16px; display: flex; gap: 10px; }
  .inst-card.small{ padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
  .inst-card.small h3{ font-size: 15.5px; }
  .inst-meta-inline{ font-size: 13px; color: var(--ink-soft); margin-top: 3px; }
  .empty{ text-align: center; padding: 40px 20px; color: var(--ink-soft); font-size: 14.5px; }

  @media (max-width: 760px){
    .meta-grid{ grid-template-columns: 1fr; }
    .inst-card.small{ flex-direction: column; align-items: flex-start; }
  }
</style>
@endpush

@section('topbar')
  <h1>Instituições</h1>
  <p class="sub">Valide o cadastro de novas instituições parceiras antes de liberarem o acesso.</p>
@endsection

@section('content')
  @php
    $tipos = [
      'ong' => 'ONG',
      'escola_publica' => 'Escola pública',
      'associacao_bairro' => 'Associação de bairro',
      'orgao_publico' => 'Órgão público municipal',
      'outro' => 'Outro',
    ];
  @endphp

  <div class="section-title">Aguardando validação</div>
  @forelse ($pendentes as $instituicao)
    <div class="inst-card pending">
      <div class="inst-top">
        <h3>{{ $instituicao->nome }}</h3>
        <span class="badge badge-aberta">Pendente</span>
      </div>
      @if ($instituicao->sobre)
        <p class="inst-desc">{{ $instituicao->sobre }}</p>
      @endif
      <div class="meta-grid">
        <div class="meta-item"><div class="k">Tipo</div><div class="v">{{ $tipos[$instituicao->tipo] ?? $instituicao->tipo }}</div></div>
        <div class="meta-item"><div class="k">Responsável</div><div class="v">{{ $instituicao->responsavel }}</div></div>
        <div class="meta-item">
          <div class="k">Contato</div>
          <div class="v">{{ $instituicao->contato_email }}{{ $instituicao->contato_telefone ? ' · '.$instituicao->contato_telefone : '' }}</div>
        </div>
        <div class="meta-item"><div class="k">Cadastrada em</div><div class="v">{{ $instituicao->created_at->format('d/m/Y') }}</div></div>
      </div>
      <div class="inst-actions">
        <form method="POST" action="{{ route('coordenacao.instituicoes.validar', $instituicao) }}">
          @csrf
          <button type="submit" class="btn btn-approve btn-small">Validar cadastro</button>
        </form>
        <form method="POST" action="{{ route('coordenacao.instituicoes.recusar', $instituicao) }}">
          @csrf
          <button type="submit" class="btn btn-reject btn-small">Recusar</button>
        </form>
      </div>
    </div>
  @empty
    <div class="empty">Nenhuma instituição aguardando validação.</div>
  @endforelse

  <div class="section-title">Instituições ativas</div>
  @forelse ($ativas as $instituicao)
    <div class="inst-card small">
      <div>
        <h3>{{ $instituicao->nome }}</h3>
        <div class="inst-meta-inline">
          Responsável: {{ $instituicao->responsavel }} · {{ $instituicao->demandas()->count() }} demanda(s) cadastrada(s)
        </div>
      </div>
      <span class="badge badge-ativa">Ativa</span>
    </div>
  @empty
    <div class="empty">Nenhuma instituição ativa ainda.</div>
  @endforelse
@endsection
