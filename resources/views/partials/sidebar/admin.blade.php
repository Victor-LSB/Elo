<nav class="side-nav">
  <a href="{{ route('admin.excecoes.index') }}" class="side-link {{ request()->routeIs('admin.excecoes.*') ? 'active' : '' }}">
    Exceções
    @if(($excecoesPendentes ?? 0) > 0)
      <span class="side-count">{{ $excecoesPendentes }}</span>
    @endif
  </a>
  <a href="{{ route('admin.configuracoes.index') }}" class="side-link {{ request()->routeIs('admin.configuracoes.*') ? 'active' : '' }}">
    Configurações
  </a>
  <a href="{{ route('admin.usuarios.index') }}" class="side-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
    Usuários
  </a>
  <a href="{{ route('admin.demandas.index') }}" class="side-link {{ request()->routeIs('admin.demandas.*') ? 'active' : '' }}">
    Todas as demandas
  </a>
</nav>
