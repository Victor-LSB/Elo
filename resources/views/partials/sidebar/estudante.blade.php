<nav class="side-nav">
  <a href="{{ route('estudante.demandas.index') }}" class="side-link {{ request()->routeIs('estudante.demandas.*') ? 'active' : '' }}">
    <span class="ico">▤</span> Demandas abertas
  </a>
  <a href="{{ route('estudante.grupo.show') }}" class="side-link {{ request()->routeIs('estudante.grupo.*') ? 'active' : '' }}">
    <span class="ico">◈</span> Meu grupo
  </a>
  <a href="{{ route('estudante.horas.index') }}" class="side-link {{ request()->routeIs('estudante.horas.*') ? 'active' : '' }}">
    <span class="ico">★</span> Minhas horas
  </a>
</nav>
