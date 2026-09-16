@extends('layouts.dashboard')

@section('titulo', $demanda->titulo.' — Elo')
@section('sidebar-role', auth()->user()->curso)
@section('sidebar-nav')
  @include('partials.sidebar.estudante')
@endsection

@push('estilos')
<style>
  .content{ max-width: 760px; }
  .desc-text{ font-size: 14.5px; }
  .meta-grid{ display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 16px; }
  .meta-item .k{ font-size: 12px; font-weight: 700; color: var(--ink-soft); text-transform: uppercase; letter-spacing: 0.03em; }
  .meta-item .v{ margin-top: 3px; font-size: 14.5px; color: var(--ink); }
  .blocked-note{
    padding: 16px 18px; background: var(--accent-soft); border-left: 3px solid var(--accent);
    border-radius: 2px; font-size: 14px; color: #6A3E0F;
  }
  @media (max-width: 760px){ .meta-grid{ grid-template-columns: 1fr; } }
</style>
@endpush

@section('topbar')
  <a href="{{ route('estudante.demandas.index') }}" class="back">← Demandas abertas</a>
  <h1>{{ $demanda->titulo }}</h1>
  <p class="sub">{{ $demanda->instituicao->nome }} · Nível {{ $demanda->nivel_complexidade }}</p>
@endsection

@section('content')
  <div class="card">
    <h2>Sobre a demanda</h2>
    <p class="desc-text">{{ $demanda->descricao }}</p>
    <div class="meta-grid">
      <div class="meta-item"><div class="k">Área</div><div class="v">{{ $demanda->area_sugerida }}</div></div>
      <div class="meta-item"><div class="k">Faixa de horas</div><div class="v">{{ $demanda->horas_min }}–{{ $demanda->horas_max }}h</div></div>
      <div class="meta-item"><div class="k">Departamento</div><div class="v">{{ $demanda->departamento?->nome ?? '—' }}</div></div>
      <div class="meta-item"><div class="k">Professor responsável</div><div class="v">{{ $demanda->professor?->nome ?? '—' }}</div></div>
    </div>
  </div>

  <div class="card">
    <h2>Candidatar meu grupo</h2>

    @if (! $grupo)
      <div class="blocked-note">
        Você precisa estar em um grupo para se candidatar.
        <a href="{{ route('estudante.grupo.show') }}" style="color:#6A3E0F;text-decoration:underline;font-weight:700;">Criar ou entrar em um grupo</a>.
      </div>
    @elseif ($bloqueado)
      <div class="blocked-note">
        🔒 Seu grupo <strong>"{{ $grupo->nome }}"</strong> já tem uma candidatura pendente ou demanda em execução.
        Um grupo só pode ter uma candidatura ou demanda ativa por vez — finalize-a antes de se candidatar a esta.
      </div>
      <a href="{{ route('estudante.horas.index') }}" class="btn btn-primary" style="margin-top:18px;">Ver minha demanda ativa</a>
    @else
      <p class="sub-h">A proposta é enviada ao professor responsável junto com a candidatura do grupo "{{ $grupo->nome }}".</p>
      <form method="POST" action="{{ route('estudante.demandas.candidatar', $demanda) }}">
        @csrf
        <div class="field">
          <label for="mensagem">Proposta / mensagem para o professor</label>
          <textarea id="mensagem" name="mensagem" placeholder="Conte a experiência prévia do grupo com o tema, e um cronograma estimado para a entrega." required>{{ old('mensagem') }}</textarea>
          <div class="field-hint">Mínimo de 20 caracteres.</div>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top:20px;">Enviar candidatura</button>
      </form>
    @endif
  </div>
@endsection
