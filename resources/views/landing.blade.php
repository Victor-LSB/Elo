<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Elo — Extensão universitária que chega ao bairro</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:opsz,wght@8..60,400;8..60,500;8..60,600;8..60,700&family=Atkinson+Hyperlegible:wght@400;700&display=swap" rel="stylesheet">
<style>
  :root{
    --bg: #EEEBE1;
    --surface: #FFFFFF;
    --surface-alt: #E4E0D2;
    --ink: #1C2624;
    --ink-soft: #4C5652;
    --primary: #24474A;
    --primary-dark: #163030;
    --accent: #C97E2A;
    --accent-soft: #EAD3AC;
    --support: #6E8F63;
    --border: #D6D0BE;
    --radius-card: 4px;
    --max: 1120px;
  }

  *{ box-sizing: border-box; }
  html{ scroll-behavior: smooth; }

  body{
    margin: 0;
    background: var(--bg);
    color: var(--ink);
    font-family: 'Atkinson Hyperlegible', Arial, sans-serif;
    font-size: 17px;
    line-height: 1.55;
    -webkit-font-smoothing: antialiased;
  }

  h1, h2, h3{
    font-family: 'Source Serif 4', Georgia, serif;
    font-weight: 600;
    color: var(--ink);
    margin: 0;
    line-height: 1.15;
  }

  p{ margin: 0; color: var(--ink-soft); }

  a{ color: inherit; }

  img, svg{ display: block; max-width: 100%; }

  .wrap{
    max-width: var(--max);
    margin: 0 auto;
    padding: 0 24px;
  }

  :focus-visible{
    outline: 2px solid var(--accent);
    outline-offset: 3px;
  }

  /* ---------- Buttons ---------- */
  .btn{
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 13px 24px;
    font-family: 'Atkinson Hyperlegible', sans-serif;
    font-size: 16px;
    font-weight: 700;
    text-decoration: none;
    border-radius: var(--radius-card);
    border: 1.5px solid transparent;
    cursor: pointer;
    transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease;
  }
  .btn-primary{
    background: var(--accent);
    color: #24140A;
  }
  .btn-primary:hover{ background: #B36F22; }
  .btn-outline{
    background: transparent;
    border-color: var(--primary);
    color: var(--primary);
  }
  .btn-outline:hover{ background: var(--primary); color: #fff; }
  .btn-inverse{
    background: var(--accent);
    color: #24140A;
  }
  .btn-inverse:hover{ background: #DB8F3E; }

  /* ---------- Header ---------- */
  header{
    position: sticky;
    top: 0;
    z-index: 40;
    background: rgba(238,235,225,0.92);
    backdrop-filter: blur(6px);
    border-bottom: 1px solid var(--border);
  }
  .nav{
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 72px;
  }
  .logo{
    font-family: 'Source Serif 4', serif;
    font-weight: 700;
    font-size: 24px;
    color: var(--primary-dark);
    text-decoration: none;
    letter-spacing: 0.2px;
  }
  .nav-links{
    display: flex;
    align-items: center;
    gap: 32px;
    list-style: none;
    margin: 0;
    padding: 0;
  }
  .nav-links a{
    text-decoration: none;
    color: var(--ink);
    font-size: 15px;
    font-weight: 700;
  }
  .nav-links a:hover{ color: var(--accent); }
  .nav-cta{ display: flex; align-items: center; gap: 14px; }
  .nav-enter{
    font-size: 15px;
    font-weight: 700;
    text-decoration: none;
    color: var(--primary-dark);
    padding: 9px 18px;
    border: 1.5px solid var(--primary-dark);
    border-radius: var(--radius-card);
  }
  .nav-enter:hover{ background: var(--primary-dark); color: #fff; }
  .menu-toggle{
    display: none;
    background: none;
    border: none;
    cursor: pointer;
    padding: 8px;
  }
  .menu-toggle span{
    display: block;
    width: 24px;
    height: 2px;
    background: var(--ink);
    margin: 5px 0;
  }

  /* ---------- Hero ---------- */
  .hero{
    padding: 84px 0 96px;
  }
  .hero-grid{
    display: grid;
    grid-template-columns: 1.1fr 0.9fr;
    gap: 64px;
    align-items: center;
  }
  .hero h1{
    font-size: clamp(32px, 4vw, 46px);
    max-width: 15ch;
  }
  .hero .lede{
    margin-top: 22px;
    font-size: 18px;
    max-width: 46ch;
    color: var(--ink-soft);
  }
  .hero-actions{
    margin-top: 34px;
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
  }

  .hero-card{
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-card);
    padding: 22px 22px 20px;
    box-shadow: 0 1px 0 var(--border);
  }
  .hero-card-tag{
    display: inline-block;
    font-size: 13px;
    font-weight: 700;
    color: var(--primary);
    background: #E4EEEC;
    padding: 3px 10px;
    border-radius: 3px;
    margin-bottom: 14px;
  }
  .hero-card h3{
    font-size: 19px;
    margin-bottom: 8px;
  }
  .hero-card p{ font-size: 14.5px; }
  .hero-card-meta{
    margin-top: 18px;
    padding-top: 14px;
    border-top: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    color: var(--ink-soft);
  }
  .hero-card-meta strong{ color: var(--ink); }
  .hero-note{
    margin-top: 14px;
    font-size: 13.5px;
    color: var(--ink-soft);
    display: flex;
    gap: 8px;
    align-items: flex-start;
  }
  .dot-live{
    width: 8px; height: 8px; border-radius: 50%;
    background: var(--support);
    margin-top: 6px;
    flex-shrink: 0;
  }

  /* ---------- Bridge divider ---------- */
  .bridge-divider{
    width: 100%;
    height: 64px;
    display: block;
  }

  /* ---------- Section shell ---------- */
  section{ padding: 88px 0; }
  .section-alt{ background: var(--surface-alt); }
  .section-head{
    max-width: 60ch;
    margin-bottom: 52px;
  }
  .section-head h2{
    font-size: clamp(26px, 3vw, 34px);
  }
  .section-head p{
    margin-top: 14px;
    font-size: 16.5px;
  }

  /* ---------- Como funciona ---------- */
  .steps{
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1px;
    background: var(--border);
    border: 1px solid var(--border);
  }
  .step{
    background: var(--surface);
    padding: 32px 28px;
  }
  .step-num{
    font-family: 'Source Serif 4', serif;
    font-size: 15px;
    font-weight: 700;
    color: var(--accent);
    margin-bottom: 14px;
  }
  .step h3{
    font-size: 18px;
    margin-bottom: 8px;
  }
  .step p{ font-size: 15px; }

  /* ---------- Papéis ---------- */
  .roles{
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
  }
  .role-card{
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-card);
    padding: 26px;
  }
  .role-mark{
    width: 38px; height: 38px;
    border-radius: 3px;
    display: flex; align-items: center; justify-content: center;
    font-family: 'Source Serif 4', serif;
    font-weight: 700;
    font-size: 17px;
    color: #fff;
    margin-bottom: 16px;
  }
  .role-card h3{ font-size: 17px; margin-bottom: 8px; }
  .role-card p{ font-size: 14.5px; }

  /* ---------- Ética ---------- */
  .etica{
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 56px;
    align-items: center;
  }
  .etica h2{ font-size: clamp(26px, 3vw, 32px); max-width: 16ch; }
  .etica .lede{ margin-top: 16px; font-size: 16.5px; max-width: 48ch; }
  .checklist-mock{
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-card);
    padding: 24px;
  }
  .checklist-mock .label{
    font-size: 13px;
    font-weight: 700;
    color: var(--primary);
    margin-bottom: 10px;
  }
  .checklist-mock .question{
    font-size: 16px;
    color: var(--ink);
    line-height: 1.5;
    margin-bottom: 18px;
  }
  .checklist-toggle{
    display: flex;
    gap: 10px;
    margin-bottom: 18px;
  }
  .toggle-opt{
    padding: 8px 18px;
    border-radius: 3px;
    font-size: 14px;
    font-weight: 700;
    border: 1.5px solid var(--border);
  }
  .toggle-opt.active{
    border-color: var(--accent);
    background: var(--accent-soft);
    color: #6A3E0F;
  }
  .checklist-result{
    font-size: 14px;
    color: var(--ink-soft);
    padding: 14px;
    background: var(--bg);
    border-left: 3px solid var(--accent);
    border-radius: 2px;
  }

  /* ---------- Níveis ---------- */
  .levels{
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
  }
  .level-card{
    padding: 26px;
    border: 1px solid var(--border);
    border-radius: var(--radius-card);
    background: var(--surface);
  }
  .level-tag{
    font-size: 13px;
    font-weight: 700;
    color: var(--support);
    margin-bottom: 10px;
  }
  .level-card h3{ font-size: 17px; margin-bottom: 8px; }
  .level-card p{ font-size: 14.5px; }

  /* ---------- CTA final ---------- */
  .cta-band{
    background: var(--primary-dark);
    color: #fff;
  }
  .cta-inner{
    text-align: left;
    max-width: 62ch;
  }
  .cta-band h2{
    color: #fff;
    font-size: clamp(26px, 3vw, 34px);
  }
  .cta-band p{
    color: #C9D6D2;
    margin-top: 14px;
    font-size: 16.5px;
  }
  .cta-band .hero-actions{ margin-top: 30px; }

  /* ---------- Footer ---------- */
  footer{
    background: var(--bg);
    border-top: 1px solid var(--border);
    padding: 56px 0 32px;
  }
  .footer-grid{
    display: grid;
    grid-template-columns: 1.3fr 1fr 1fr;
    gap: 40px;
  }
  .footer-brand .logo{ display: inline-block; margin-bottom: 12px; }
  .footer-brand p{ font-size: 14.5px; max-width: 34ch; }
  .footer-col h4{
    font-size: 14px;
    font-weight: 700;
    color: var(--ink);
    margin-bottom: 14px;
  }
  .footer-col ul{ list-style: none; margin: 0; padding: 0; }
  .footer-col li{ margin-bottom: 10px; }
  .footer-col a{
    font-size: 14.5px;
    color: var(--ink-soft);
    text-decoration: none;
  }
  .footer-col a:hover{ color: var(--accent); }
  .footer-bottom{
    margin-top: 48px;
    padding-top: 20px;
    border-top: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    font-size: 13px;
    color: var(--ink-soft);
  }

  /* ---------- Motion ---------- */
  @media (prefers-reduced-motion: no-preference){
    .hero h1, .hero .lede, .hero-actions, .hero-card{
      opacity: 0;
      animation: rise 0.7s ease forwards;
    }
    .hero h1{ animation-delay: 0.05s; }
    .hero .lede{ animation-delay: 0.18s; }
    .hero-actions{ animation-delay: 0.30s; }
    .hero-card{ animation-delay: 0.22s; }
    @keyframes rise{
      from{ opacity: 0; transform: translateY(10px); }
      to{ opacity: 1; transform: translateY(0); }
    }
  }

  /* ---------- Responsive ---------- */
  @media (max-width: 880px){
    .nav-links, .nav-cta .nav-enter{ display: none; }
    .menu-toggle{ display: block; }
    .hero-grid{ grid-template-columns: 1fr; gap: 40px; }
    .hero{ padding: 56px 0 64px; }
    .steps{ grid-template-columns: 1fr; }
    .roles{ grid-template-columns: 1fr 1fr; }
    .etica{ grid-template-columns: 1fr; gap: 32px; }
    .levels{ grid-template-columns: 1fr; }
    .footer-grid{ grid-template-columns: 1fr; gap: 28px; }
    section{ padding: 64px 0; }
  }
  @media (max-width: 520px){
    .roles{ grid-template-columns: 1fr; }
    .hero-actions .btn{ width: 100%; }
  }

  .nav-links.open{
    display: flex;
    flex-direction: column;
    position: absolute;
    top: 72px; left: 0; right: 0;
    background: var(--bg);
    border-bottom: 1px solid var(--border);
    padding: 20px 24px 24px;
    gap: 18px;
  }
</style>
</head>
<body>

<header>
  <div class="wrap nav">
    <a href="{{ route('landing') }}" class="logo">Elo</a>
    <nav>
      <ul class="nav-links" id="navLinks">
        <li><a href="#como-funciona">Como funciona</a></li>
        <li><a href="#papeis">Papéis</a></li>
        <li><a href="#etica">Ética</a></li>
      </ul>
    </nav>
    <div class="nav-cta">
      <a href="{{ route('login') }}" class="nav-enter">Entrar</a>
      <button class="menu-toggle" id="menuToggle" aria-label="Abrir menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>

<main>
  <section class="hero">
    <div class="wrap hero-grid">
      <div>
        <h1>Uma ONG precisa de um sistema. Um grupo de estudantes sabe construir um.</h1>
        <p class="lede">A Elo liga demandas reais de instituições parceiras — ONGs, escolas públicas, associações de bairro — a estudantes de todos os cursos, em troca de horas complementares.</p>
        <div class="hero-actions">
          <a href="{{ route('cadastro.instituicao.form') }}" class="btn btn-primary">Sou uma instituição parceira</a>
          <a href="{{ route('cadastro.estudante.form') }}" class="btn btn-outline">Sou estudante</a>
        </div>
      </div>
      <div class="hero-card">
        <span class="hero-card-tag">Nível 2 · Educação</span>
        <h3>Sistema de cadastro de beneficiários</h3>
        <p>ONG Girassol precisa organizar o cadastro de famílias atendidas, hoje feito em papel.</p>
        <div class="hero-card-meta">
          <span>Departamento: <strong>Computação</strong></span>
          <span>40–60h</span>
        </div>
        <div class="hero-note">
          <span class="dot-live"></span>
          <span>Aberta para candidatura há 3 dias</span>
        </div>
      </div>
    </div>
  </section>

  <svg class="bridge-divider" viewBox="0 0 1200 64" preserveAspectRatio="none" aria-hidden="true">
    <path d="M0 40 C 200 10, 300 10, 400 40 S 700 10, 800 40 S 1100 10, 1200 40" fill="none" stroke="#C97E2A" stroke-width="2"/>
    <circle cx="400" cy="40" r="4" fill="#24474A"/>
    <circle cx="800" cy="40" r="4" fill="#24474A"/>
  </svg>

  <section id="como-funciona">
    <div class="wrap">
      <div class="section-head">
        <h2>Da demanda ao certificado</h2>
        <p>Seis etapas conectam quem precisa a quem pode construir — com triagem, aprovação e validação em cada ponto.</p>
      </div>
      <div class="steps">
        <div class="step">
          <div class="step-num">1</div>
          <h3>A instituição cadastra</h3>
          <p>Título, descrição, área e nível de complexidade estimado.</p>
        </div>
        <div class="step">
          <div class="step-num">2</div>
          <h3>A coordenação faz a triagem</h3>
          <p>Define nível, prazo, departamento responsável e aplica o checklist ético.</p>
        </div>
        <div class="step">
          <div class="step-num">3</div>
          <h3>Um grupo se candidata</h3>
          <p>Com uma proposta: interesse, experiência prévia e cronograma.</p>
        </div>
        <div class="step">
          <div class="step-num">4</div>
          <h3>O professor aprova</h3>
          <p>Escolhe o grupo, define milestones e acompanha o progresso.</p>
        </div>
        <div class="step">
          <div class="step-num">5</div>
          <h3>A entrega é validada</h3>
          <p>Por presença, relatório ou aprovação dupla, conforme o nível.</p>
        </div>
        <div class="step">
          <div class="step-num">6</div>
          <h3>O estudante recebe</h3>
          <p>Horas creditadas automaticamente e certificado consolidado.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="section-alt" id="papeis">
    <div class="wrap">
      <div class="section-head">
        <h2>Cinco papéis, um fluxo só</h2>
        <p>Cada pessoa entra na plataforma com permissões e uma tarefa clara.</p>
      </div>
      <div class="roles">
        <div class="role-card">
          <div class="role-mark" style="background:var(--primary)">I</div>
          <h3>Instituição parceira</h3>
          <p>Cadastra demandas reais, acompanha a execução e valida as entregas.</p>
        </div>
        <div class="role-card">
          <div class="role-mark" style="background:var(--accent)">C</div>
          <h3>Coordenação de extensão</h3>
          <p>Faz a triagem, aplica o checklist ético e atribui a demanda a um departamento.</p>
        </div>
        <div class="role-card">
          <div class="role-mark" style="background:var(--support)">P</div>
          <h3>Professor orientador</h3>
          <p>Aprova candidaturas, define milestones e valida o trabalho do grupo.</p>
        </div>
        <div class="role-card">
          <div class="role-mark" style="background:var(--primary-dark)">E</div>
          <h3>Estudante</h3>
          <p>Forma grupo, se candidata, executa a demanda e acumula horas complementares.</p>
        </div>
        <div class="role-card">
          <div class="role-mark" style="background:#7A6A4F">A</div>
          <h3>Admin</h3>
          <p>Configura o sistema e resolve exceções — reabertura de demandas, disputas.</p>
        </div>
      </div>
    </div>
  </section>

  <section id="etica">
    <div class="wrap etica">
      <div>
        <h2>Extensão não é mão de obra barata.</h2>
        <p class="lede">Antes de aprovar qualquer demanda, a coordenação verifica se ela substitui um serviço que a instituição deveria contratar profissionalmente. Se for o caso, a demanda exige justificativa por escrito e passa por uma revisão adicional antes de ser publicada.</p>
      </div>
      <div class="checklist-mock">
        <div class="label">Checklist ético · triagem</div>
        <div class="question">Esta demanda substitui um serviço que a instituição deveria contratar profissionalmente?</div>
        <div class="checklist-toggle">
          <div class="toggle-opt active">Sim</div>
          <div class="toggle-opt">Não</div>
        </div>
        <div class="checklist-result">Justificativa obrigatória. Demanda sinalizada para revisão adicional antes da aprovação.</div>
      </div>
    </div>
  </section>

  <section class="section-alt">
    <div class="wrap">
      <div class="section-head">
        <h2>Três níveis, três formas de validar</h2>
        <p>A complexidade da demanda define como a entrega é conferida.</p>
      </div>
      <div class="levels">
        <div class="level-card">
          <div class="level-tag">Nível 1</div>
          <h3>Presença</h3>
          <p>Validado pela confirmação de presença no dia do evento ou mutirão.</p>
        </div>
        <div class="level-card">
          <div class="level-tag">Nível 2</div>
          <h3>Relatório</h3>
          <p>Validado por um relatório de entrega anexado pelo grupo ao final.</p>
        </div>
        <div class="level-card">
          <div class="level-tag">Nível 3</div>
          <h3>Milestones</h3>
          <p>Dividido em etapas com prazo próprio, cada uma validada pelo professor e pela instituição.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="cta-band">
    <div class="wrap cta-inner">
      <h2>Sua instituição tem uma demanda real?</h2>
      <p>Cadastre-se e a coordenação de extensão faz a triagem em até 5 dias úteis.</p>
      <div class="hero-actions">
        <a href="{{ route('cadastro.instituicao.form') }}" class="btn btn-inverse">Cadastrar instituição</a>
      </div>
    </div>
  </section>
</main>

<footer>
  <div class="wrap footer-grid">
    <div class="footer-brand">
      <a href="{{ route('landing') }}" class="logo">Elo</a>
      <p>Extensão universitária que chega ao bairro — demandas reais, resolvidas por quem está aprendendo a resolver.</p>
    </div>
    <div class="footer-col">
      <h4>Plataforma</h4>
      <ul>
        <li><a href="#como-funciona">Como funciona</a></li>
        <li><a href="#papeis">Papéis</a></li>
        <li><a href="#etica">Checklist ético</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Acesso</h4>
      <ul>
        <li><a href="{{ route('cadastro.instituicao.form') }}">Sou uma instituição</a></li>
        <li><a href="{{ route('cadastro.estudante.form') }}">Sou estudante</a></li>
        <li><a href="{{ route('login') }}">Entrar</a></li>
      </ul>
    </div>
  </div>
  <div class="wrap footer-bottom">
    <span>© 2026 Elo. Extensão universitária.</span>
    <span>Compatível com a infraestrutura institucional da UNIVALI.</span>
  </div>
</footer>

<script>
  const toggle = document.getElementById('menuToggle');
  const links = document.getElementById('navLinks');
  toggle.addEventListener('click', () => {
    const isOpen = links.classList.toggle('open');
    toggle.setAttribute('aria-expanded', isOpen);
  });
  links.querySelectorAll('a').forEach(a => a.addEventListener('click', () => {
    links.classList.remove('open');
    toggle.setAttribute('aria-expanded', 'false');
  }));
</script>

</body>
</html>
