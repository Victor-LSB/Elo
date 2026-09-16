<style>
  :root{
    --bg: #EEEBE1; --surface: #FFFFFF; --surface-alt: #E4E0D2;
    --ink: #1C2624; --ink-soft: #4C5652;
    --primary: #24474A; --primary-dark: #163030;
    --accent: #C97E2A; --accent-soft: #EAD3AC; --support: #6E8F63;
    --danger: #A6432B; --danger-soft: #F5E4DC;
    --border: #D6D0BE; --radius-card: 4px;
  }
  *{ box-sizing: border-box; }
  body{
    margin: 0; background: var(--bg); color: var(--ink);
    font-family: 'Atkinson Hyperlegible', Arial, sans-serif; font-size: 16px; line-height: 1.55;
    -webkit-font-smoothing: antialiased;
  }
  h1, h2, h3{ font-family: 'Source Serif 4', Georgia, serif; font-weight: 600; color: var(--ink); margin: 0; line-height: 1.15; }
  p{ margin: 0; color: var(--ink-soft); }
  a{ color: inherit; text-decoration: none; }
  :focus-visible{ outline: 2px solid var(--accent); outline-offset: 3px; }

  .app{ display: flex; min-height: 100vh; }
  .sidebar{
    width: 240px; flex-shrink: 0; background: var(--primary-dark); color: #fff;
    display: flex; flex-direction: column; padding: 24px 18px;
    position: sticky; top: 0; height: 100vh;
  }
  .sidebar .logo{ font-family: 'Source Serif 4', serif; font-weight: 700; font-size: 22px; color: #fff; padding: 4px 10px 20px; }
  .side-nav{ display: flex; flex-direction: column; gap: 4px; flex: 1; }
  .side-link{ display: flex; align-items: center; gap: 10px; padding: 11px 12px; border-radius: 3px; font-size: 14.5px; font-weight: 700; color: #C9D6D2; }
  .side-link .ico{ width: 18px; text-align: center; }
  .side-link:hover{ background: rgba(255,255,255,0.06); color: #fff; }
  .side-link.active{ background: var(--accent); color: #24140A; }
  .side-count{ margin-left: auto; background: var(--accent); color: #24140A; font-size: 11.5px; font-weight: 700; border-radius: 10px; padding: 1px 7px; }
  .side-link.active .side-count{ background: rgba(0,0,0,0.18); color: #fff; }
  .side-account{ border-top: 1px solid rgba(255,255,255,0.14); padding-top: 16px; margin-top: 12px; }
  .side-account .who{ font-size: 13.5px; font-weight: 700; color: #fff; }
  .side-account .role{ font-size: 12.5px; color: #A9BDB8; margin-top: 2px; }
  .side-account .sair{ display: block; margin-top: 12px; font-size: 13px; font-weight: 700; color: #C9D6D2; background: none; border: none; cursor: pointer; font-family: inherit; padding: 0; }
  .side-account .sair:hover{ color: #fff; }

  .main{ flex: 1; min-width: 0; }
  .topbar{
    padding: 22px 32px; border-bottom: 1px solid var(--border); background: var(--bg);
    display: flex; align-items: flex-start; justify-content: space-between; gap: 16px;
  }
  .topbar-conteudo{ flex: 1; min-width: 0; }
  .topbar h1{ font-size: 24px; }
  .topbar .sub{ margin-top: 4px; font-size: 14.5px; }
  .back{ font-size: 13.5px; font-weight: 700; color: var(--ink-soft); display: inline-block; margin-bottom: 8px; }
  .back:hover{ color: var(--accent); }

  /* ---------- Central de notificações (RF-07.1) ---------- */
  .sino-wrap{ position: relative; flex-shrink: 0; }
  .sino{
    position: relative; background: none; border: 1.5px solid var(--border); border-radius: var(--radius-card);
    padding: 8px 12px; font-size: 17px; cursor: pointer; line-height: 1;
  }
  .sino:hover{ border-color: var(--primary); }
  .sino-badge{
    position: absolute; top: -7px; right: -7px; background: var(--danger); color: #fff;
    font-family: 'Atkinson Hyperlegible', sans-serif; font-size: 11px; font-weight: 700;
    border-radius: 10px; padding: 1px 6px; line-height: 1.4;
  }
  .sino-painel{
    position: absolute; right: 0; top: calc(100% + 8px); width: 340px; max-width: 86vw; z-index: 40;
    background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-card);
    box-shadow: 0 6px 20px rgba(28,38,36,0.13); max-height: 420px; overflow-y: auto;
  }
  .sino-cabecalho{
    display: flex; align-items: center; justify-content: space-between; gap: 10px;
    padding: 12px 16px; border-bottom: 1px solid var(--border); font-size: 14px;
  }
  .sino-marcar{
    background: none; border: none; cursor: pointer; padding: 0;
    font-family: inherit; font-size: 12.5px; font-weight: 700; color: var(--accent);
  }
  .sino-marcar:hover{ text-decoration: underline; }
  .sino-item{ display: block; padding: 13px 16px; border-bottom: 1px solid var(--border); }
  .sino-item:last-child{ border-bottom: none; }
  .sino-item:hover{ background: var(--bg); }
  .sino-titulo{ font-size: 13.5px; font-weight: 700; color: var(--ink); }
  .sino-msg{ margin-top: 3px; font-size: 13px; color: var(--ink-soft); }
  .sino-quando{ margin-top: 5px; font-size: 11.5px; color: var(--ink-soft); }
  .sino-vazio{ padding: 26px 16px; text-align: center; font-size: 13.5px; color: var(--ink-soft); }

  .content{ padding: 28px 32px 48px; }

  .section-title{ font-size: 15px; margin: 30px 0 14px; color: var(--ink-soft); text-transform: uppercase; letter-spacing: 0.03em; font-weight: 700; }
  .section-title:first-child{ margin-top: 0; }

  .stats{ display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 28px; }
  .stat-card{ background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-card); padding: 18px 20px; }
  .stat-card.alert{ border-color: var(--accent); background: var(--accent-soft); }
  .stat-card .num{ font-family: 'Source Serif 4', serif; font-size: 28px; font-weight: 700; color: var(--primary-dark); }
  .stat-card.alert .num{ color: #6A3E0F; }
  .stat-card .label{ margin-top: 4px; font-size: 13px; color: var(--ink-soft); }

  .card{ background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-card); padding: 24px 26px 28px; margin-bottom: 20px; }
  .card h2{ font-size: 17px; margin-bottom: 4px; }
  .card .sub-h{ font-size: 13.5px; margin-bottom: 16px; }

  .badge{ display: inline-block; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 3px; }
  .badge-pendente{ background: var(--surface-alt); color: var(--ink-soft); }
  .badge-aberta{ background: var(--accent-soft); color: #6A3E0F; }
  .badge-andamento{ background: var(--accent-soft); color: #6A3E0F; }
  .badge-execucao{ background: #E4EEEC; color: var(--primary-dark); }
  .badge-concluido, .badge-concluida, .badge-ativa{ background: #E1EADB; color: #3D5433; }
  .badge-abandonada, .badge-risco{ background: var(--danger-soft); color: #6A2A18; }
  .badge-reaberta, .badge-disputa{ background: var(--accent-soft); color: #6A3E0F; }

  .field{ margin-top: 20px; }
  .field:first-child{ margin-top: 0; }
  .field label{ display: block; font-size: 13.5px; font-weight: 700; color: var(--ink); margin-bottom: 6px; }
  .field input, .field select, .field textarea{
    width: 100%; padding: 11px 13px; font-size: 15px; font-family: 'Atkinson Hyperlegible', sans-serif;
    border: 1.5px solid var(--border); border-radius: 3px; background: var(--bg); color: var(--ink);
  }
  .field textarea{ resize: vertical; min-height: 90px; }
  .field input:focus, .field select:focus, .field textarea:focus{ outline: none; border-color: var(--primary); background: var(--surface); }
  .field-hint{ margin-top: 5px; font-size: 12.5px; color: var(--ink-soft); }
  .grid-2{ display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

  .btn{
    display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 11px 20px;
    font-family: 'Atkinson Hyperlegible', sans-serif; font-size: 14.5px; font-weight: 700;
    border-radius: var(--radius-card); border: 1.5px solid transparent; cursor: pointer;
  }
  .btn-primary{ background: var(--accent); color: #24140A; }
  .btn-primary:hover{ background: #B36F22; }
  .btn-outline{ background: transparent; border-color: var(--border); color: var(--ink-soft); }
  .btn-outline:hover{ border-color: var(--primary); color: var(--primary); }
  .btn-approve{ background: var(--support); color: #fff; }
  .btn-approve:hover{ background: #5C7A52; }
  .btn-reject{ background: transparent; border-color: var(--border); color: var(--danger); }
  .btn-reject:hover{ border-color: var(--danger); background: var(--danger-soft); }
  .btn-small{ padding: 8px 14px; font-size: 13px; }

  .alert-erros{
    margin-bottom: 20px; padding: 12px 14px; border-radius: 3px;
    background: var(--danger-soft); border-left: 3px solid var(--danger); color: #6A2A18; font-size: 13.5px;
  }
  .alert-status{
    margin-bottom: 20px; padding: 12px 14px; border-radius: 3px;
    background: #E1EADB; border-left: 3px solid var(--support); color: #3D5433; font-size: 13.5px;
  }

  @media (max-width: 980px){ .stats{ grid-template-columns: 1fr 1fr; } }
  @media (max-width: 760px){
    .sidebar{ display: none; }
    .content{ padding: 22px 18px 40px; }
    .topbar{ padding: 18px 18px; }
    .grid-2{ grid-template-columns: 1fr; }
  }
</style>
