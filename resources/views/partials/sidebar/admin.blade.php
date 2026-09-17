<nav class="side-nav">
  <a href="{{ route('admin.excecoes.index') }}" class="side-link {{ request()->routeIs('admin.excecoes.*') ? 'active' : '' }}">
    <span class="ico">▤</span> Exceções
    @if(($excecoesPendentes ?? 0) > 0)
      <span class="side-count">{{ $excecoesPendentes }}</span>
    @endif
  </a>
  <a href="{{ route('admin.configuracoes.index') }}" class="side-link {{ request()->routeIs('admin.configuracoes.*') ? 'active' : '' }}">
    <span class="ico">⚙</span> Configurações
  </a>
  <a href="{{ route('admin.usuarios.index') }}" class="side-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
    <span class="ico">◈</span> Usuários
  </a>
  <a href="{{ route('admin.demandas.index') }}" class="side-link {{ request()->routeIs('admin.demandas.*') ? 'active' : '' }}">
    <span class="ico">◔</span> Todas as demandas
  </a>
</nav>
