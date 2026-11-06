<nav class="side-nav">
  <a href="{{ route('instituicao.demandas.index') }}" class="side-link {{ request()->routeIs('instituicao.demandas.index') || request()->routeIs('instituicao.demandas.show') ? 'active' : '' }}">
    Minhas demandas
  </a>
  <a href="{{ route('instituicao.demandas.create') }}" class="side-link {{ request()->routeIs('instituicao.demandas.create') || request()->routeIs('instituicao.demandas.store') ? 'active' : '' }}">
    Nova demanda
  </a>
  <a href="{{ route('instituicao.perfil.show') }}" class="side-link {{ request()->routeIs('instituicao.perfil.*') ? 'active' : '' }}">
    Perfil da instituição
  </a>
</nav>
