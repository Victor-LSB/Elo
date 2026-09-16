<nav class="side-nav">
  <a href="{{ route('instituicao.demandas.index') }}" class="side-link {{ request()->routeIs('instituicao.demandas.index') || request()->routeIs('instituicao.demandas.show') ? 'active' : '' }}">
    <span class="ico">▤</span> Minhas demandas
  </a>
  <a href="{{ route('instituicao.demandas.create') }}" class="side-link {{ request()->routeIs('instituicao.demandas.create') || request()->routeIs('instituicao.demandas.store') ? 'active' : '' }}">
    <span class="ico">+</span> Nova demanda
  </a>
  <a href="#" class="side-link"><span class="ico">◔</span> Perfil da instituição</a>
</nav>
