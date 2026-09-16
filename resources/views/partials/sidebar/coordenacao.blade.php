<nav class="side-nav">
  <a href="{{ route('coordenacao.triagem.index') }}" class="side-link {{ request()->routeIs('coordenacao.triagem.*') ? 'active' : '' }}">
    <span class="ico">▤</span> Fila de triagem
    @if(($filaPendente ?? 0) > 0)
      <span class="side-count">{{ $filaPendente }}</span>
    @endif
  </a>
  <a href="{{ route('coordenacao.instituicoes.index') }}" class="side-link {{ request()->routeIs('coordenacao.instituicoes.*') ? 'active' : '' }}">
    <span class="ico">◈</span> Instituições
    @if(($instituicoesPendentes ?? 0) > 0)
      <span class="side-count">{{ $instituicoesPendentes }}</span>
    @endif
  </a>
  <a href="#" class="side-link"><span class="ico">◔</span> Professores</a>
</nav>
