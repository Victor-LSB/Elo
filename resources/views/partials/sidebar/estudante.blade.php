<nav class="side-nav">
  <a href="{{ route('estudante.demandas.index') }}" class="side-link {{ request()->routeIs('estudante.demandas.*') ? 'active' : '' }}">
    Demandas abertas
  </a>
  <a href="{{ route('estudante.grupo.show') }}" class="side-link {{ request()->routeIs('estudante.grupo.*') ? 'active' : '' }}">
    Meu grupo
  </a>
  <a href="{{ route('estudante.horas.index') }}" class="side-link {{ request()->routeIs('estudante.horas.*') ? 'active' : '' }}">
    Minhas horas
  </a>
</nav>
