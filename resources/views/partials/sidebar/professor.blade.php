<nav class="side-nav">
  <a href="{{ route('professor.demandas.index') }}" class="side-link {{ request()->routeIs('professor.demandas.*') ? 'active' : '' }}">
    <span class="ico">▤</span> Minhas demandas
  </a>
  <a href="{{ route('professor.demandas.index') }}#candidaturas" class="side-link {{ request()->routeIs('professor.candidaturas.*') ? 'active' : '' }}">
    <span class="ico">✎</span> Candidaturas
    @if(($candidaturasPendentes ?? 0) > 0)
      <span class="side-count">{{ $candidaturasPendentes }}</span>
    @endif
  </a>
  <a href="{{ route('professor.milestones.index') }}" class="side-link {{ request()->routeIs('professor.milestones.*') ? 'active' : '' }}">
    <span class="ico">◔</span> Milestones
  </a>
</nav>
