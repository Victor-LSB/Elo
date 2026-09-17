@extends('layouts.dashboard')

@section('titulo', 'Usuários — Elo')
@section('sidebar-role', 'Elo · Administração')
@section('sidebar-nav')
  @include('partials.sidebar.admin')
@endsection

@push('estilos')
<style>
  .content{ max-width: 860px; }
  .filter-bar{ display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
  .filter-bar select, .filter-bar input{
    padding: 9px 12px; font-size: 14px; font-family: 'Atkinson Hyperlegible', sans-serif;
    border: 1.5px solid var(--border); border-radius: 3px; background: var(--bg); color: var(--ink);
  }
  .filter-bar input{ flex: 1; min-width: 200px; }
  .user-row{
    background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-card);
    padding: 14px 18px; margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between; gap: 16px;
  }
  .user-row .name{ font-weight: 700; color: var(--ink); font-size: 14.5px; }
  .user-row .email{ color: var(--ink-soft); font-size: 12.5px; margin-top: 2px; }
  .user-row .extra{ color: var(--ink-soft); font-size: 12.5px; margin-top: 2px; }
  .papel-tag{
    font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em;
    padding: 3px 9px; border-radius: 10px; background: var(--surface-alt); color: var(--ink-soft); flex-shrink: 0;
  }
  .papel-tag.estudante{ background: #E4EEEC; color: var(--primary-dark); }
  .papel-tag.professor{ background: #E1EADB; color: #3D5433; }
  .papel-tag.instituicao{ background: var(--accent-soft); color: #6A3E0F; }
  .papel-tag.coordenacao{ background: #E4EEEC; color: var(--primary-dark); }
  .papel-tag.admin{ background: var(--danger-soft); color: #6A2A18; }
  .empty{ text-align: center; padding: 40px 20px; color: var(--ink-soft); font-size: 14.5px; }

  @media (max-width: 760px){
    .user-row{ flex-direction: column; align-items: flex-start; }
  }
</style>
@endpush

@section('topbar')
  <h1>Usuários</h1>
  <p class="sub">Todas as contas cadastradas na plataforma — {{ $usuarios->count() }} no total.</p>
@endsection

@section('content')
  <form method="GET" action="{{ route('admin.usuarios.index') }}" class="filter-bar">
    <select name="papel" onchange="this.form.submit()">
      <option value="">Todos os papéis</option>
      @foreach (['estudante' => 'Estudante', 'professor' => 'Professor', 'instituicao' => 'Instituição', 'coordenacao' => 'Coordenação', 'admin' => 'Admin'] as $valor => $rotulo)
        <option value="{{ $valor }}" @selected($papel === $valor)>{{ $rotulo }}</option>
      @endforeach
    </select>
    <input type="text" name="busca" value="{{ $busca }}" placeholder="Buscar por nome ou e-mail…">
    <button type="submit" class="btn btn-outline btn-small">Filtrar</button>
  </form>

  @forelse ($usuarios as $usuario)
    <div class="user-row">
      <div>
        <div class="name">{{ $usuario->nome }}</div>
        <div class="email">{{ $usuario->email }}</div>
        <div class="extra">
          @if ($usuario->papel === 'professor')
            {{ $usuario->departamento?->nome ?? 'Sem departamento' }}
          @elseif ($usuario->papel === 'instituicao')
            {{ $usuario->instituicao?->nome ?? '—' }}
          @elseif ($usuario->papel === 'estudante')
            {{ $usuario->curso }} · matrícula {{ $usuario->matricula }}
          @endif
        </div>
      </div>
      <span class="papel-tag {{ $usuario->papel }}">{{ $usuario->papel }}</span>
    </div>
  @empty
    <div class="empty">Nenhum usuário encontrado com esse filtro.</div>
  @endforelse
@endsection
