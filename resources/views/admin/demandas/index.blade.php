@extends('layouts.dashboard')

@section('titulo', 'Todas as demandas — Elo')
@section('sidebar-role', 'Elo · Administração')
@section('sidebar-nav')
  @include('partials.sidebar.admin')
@endsection

@push('estilos')
<style>
  .content{ max-width: 860px; }
  .filter-bar{ display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
  .filter-bar select{
    padding: 9px 12px; font-size: 14px; font-family: 'Atkinson Hyperlegible', sans-serif;
    border: 1.5px solid var(--border); border-radius: 3px; background: var(--bg); color: var(--ink);
  }
  .demand-card{
    background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-card);
    padding: 18px 20px; margin-bottom: 10px;
  }
  .demand-top{ display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
  .demand-top h3{ font-size: 15.5px; }
  .demand-meta{ margin-top: 6px; font-size: 13px; color: var(--ink-soft); display: flex; gap: 14px; flex-wrap: wrap; }
  .empty{ text-align: center; padding: 40px 20px; color: var(--ink-soft); font-size: 14.5px; }
</style>
@endpush

@section('topbar')
  <h1>Todas as demandas</h1>
  <p class="sub">Visão geral de todas as demandas cadastradas na plataforma — {{ $demandas->count() }} no total.</p>
@endsection

@section('content')
  @php
    $rotulos = [
      'pendente_triagem' => ['Pendente de triagem', 'pendente'],
      'em_revisao_adicional' => ['Em revisão adicional', 'pendente'],
      'aberta_candidatura' => ['Aberta p/ candidatura', 'aberta'],
      'em_execucao' => ['Em execução', 'execucao'],
      'concluida' => ['Concluída', 'concluida'],
      'abandonada' => ['Abandonada', 'abandonada'],
      'reaberta' => ['Reaberta', 'reaberta'],
    ];
  @endphp

  <form method="GET" action="{{ route('admin.demandas.index') }}" class="filter-bar">
    <select name="status" onchange="this.form.submit()">
      <option value="">Todos os status</option>
      @foreach ($rotulos as $valor => [$rotulo, $cor])
        <option value="{{ $valor }}" @selected($status === $valor)>{{ $rotulo }}</option>
      @endforeach
    </select>
  </form>

  @forelse ($demandas as $demanda)
    @php [$rotulo, $cor] = $rotulos[$demanda->status] ?? [$demanda->status, 'pendente']; @endphp
    <div class="demand-card">
      <div class="demand-top">
        <div>
          <span class="badge badge-{{ $cor }}">{{ $rotulo }}</span>
          <h3 style="display:inline; margin-left:8px;">{{ $demanda->titulo }}</h3>
        </div>
      </div>
      <div class="demand-meta">
        <span>Instituição: {{ $demanda->instituicao->nome }}</span>
        <span>Departamento: {{ $demanda->departamento?->nome ?? '—' }}</span>
        <span>Professor: {{ $demanda->professor?->nome ?? '—' }}</span>
        <span>Criada em {{ $demanda->created_at->format('d/m/Y') }}</span>
      </div>
    </div>
  @empty
    <div class="empty">Nenhuma demanda encontrada com esse filtro.</div>
  @endforelse
@endsection
