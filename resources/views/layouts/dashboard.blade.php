<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('titulo', 'Elo')</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:opsz,wght@8..60,400;8..60,500;8..60,600;8..60,700&family=Atkinson+Hyperlegible:wght@400;700&display=swap" rel="stylesheet">
@include('partials.dashboard-styles')
@stack('estilos')
</head>
<body>

<div class="app">
  <aside class="sidebar">
    <a href="{{ route('landing') }}" class="logo">Elo</a>
    @yield('sidebar-nav')
    <div class="side-account">
      <div class="who">{{ auth()->user()->nome }}</div>
      <div class="role">@yield('sidebar-role')</div>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="sair">Sair</button>
      </form>
    </div>
  </aside>

  <main class="main">
    <div class="topbar">
      <div class="topbar-conteudo">
        @yield('topbar')
      </div>

      <div class="sino-wrap">
        <button type="button" class="sino" id="sinoBtn" aria-label="Notificações" aria-expanded="false">
          <span aria-hidden="true">🔔</span>
          @if (($totalNaoLidas ?? 0) > 0)
            <span class="sino-badge">{{ $totalNaoLidas > 9 ? '9+' : $totalNaoLidas }}</span>
          @endif
        </button>

        <div class="sino-painel" id="sinoPainel" hidden>
          <div class="sino-cabecalho">
            <strong>Notificações</strong>
            @if (($totalNaoLidas ?? 0) > 0)
              <form method="POST" action="{{ route('notificacoes.lidas') }}">
                @csrf
                <button type="submit" class="sino-marcar">Marcar todas como lidas</button>
              </form>
            @endif
          </div>

          @forelse ($notificacoes ?? [] as $notificacao)
            <a href="{{ route('notificacoes.abrir', $notificacao->id) }}" class="sino-item">
              <div class="sino-titulo">{{ $notificacao->data['titulo'] }}</div>
              <div class="sino-msg">{{ $notificacao->data['mensagem'] }}</div>
              <div class="sino-quando">{{ $notificacao->created_at->diffForHumans() }}</div>
            </a>
          @empty
            <div class="sino-vazio">Nenhuma notificação nova.</div>
          @endforelse
        </div>
      </div>
    </div>

    <div class="content">
      @if (session('status'))
        <div class="alert-status">{{ session('status') }}</div>
      @endif
      @if ($errors->any())
        <div class="alert-erros">
          @foreach ($errors->all() as $erro)
            {{ $erro }}<br>
          @endforeach
        </div>
      @endif

      @yield('content')
    </div>
  </main>
</div>

@stack('scripts')
<script>
  (function(){
    const btn = document.getElementById('sinoBtn');
    const painel = document.getElementById('sinoPainel');
    if (!btn || !painel) return;

    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const aberto = !painel.hidden;
      painel.hidden = aberto;
      btn.setAttribute('aria-expanded', String(!aberto));
    });

    document.addEventListener('click', (e) => {
      if (!painel.hidden && !painel.contains(e.target)) {
        painel.hidden = true;
        btn.setAttribute('aria-expanded', 'false');
      }
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && !painel.hidden) {
        painel.hidden = true;
        btn.setAttribute('aria-expanded', 'false');
        btn.focus();
      }
    });
  })();
</script>
</body>
</html>
