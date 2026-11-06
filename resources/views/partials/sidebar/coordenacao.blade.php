<nav class="side-nav">
  <a href="{{ route('coordenacao.triagem.index') }}" class="side-link {{ request()->routeIs('coordenacao.triagem.*') ? 'active' : '' }}">
    Fila de triagem
    @if(($filaPendente ?? 0) > 0)
      <span class="side-count">{{ $filaPendente }}</span>
    @endif
  </a>
  <a href="{{ route('coordenacao.instituicoes.index') }}" class="side-link {{ request()->routeIs('coordenacao.instituicoes.*') ? 'active' : '' }}">
    Instituições
    @if(($instituicoesPendentes ?? 0) > 0)
      <span class="side-count">{{ $instituicoesPendentes }}</span>
    @endif
  </a>
  <a href="{{ route('coordenacao.professores.index') }}" class="side-link {{ request()->routeIs('coordenacao.professores.*') ? 'active' : '' }}">
    Professores
  </a>
</nav>
