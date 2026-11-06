<nav class="side-nav">
  <a href="{{ route('professor.demandas.index') }}" class="side-link {{ request()->routeIs('professor.demandas.*') ? 'active' : '' }}">
    Minhas demandas
  </a>
  <a href="{{ route('professor.candidaturas.index') }}" class="side-link {{ request()->routeIs('professor.candidaturas.*') ? 'active' : '' }}">
    Candidaturas
    @if(($candidaturasPendentes ?? 0) > 0)
      <span class="side-count">{{ $candidaturasPendentes }}</span>
    @endif
  </a>
  <a href="{{ route('professor.milestones.index') }}" class="side-link {{ request()->routeIs('professor.milestones.*') ? 'active' : '' }}">
    Milestones
  </a>
</nav>
