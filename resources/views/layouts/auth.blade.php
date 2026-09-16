<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('titulo', 'Elo')</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:opsz,wght@8..60,400;8..60,500;8..60,600;8..60,700&family=Atkinson+Hyperlegible:wght@400;700&display=swap" rel="stylesheet">
<style>
  :root{
    --bg: #EEEBE1; --surface: #FFFFFF; --surface-alt: #E4E0D2;
    --ink: #1C2624; --ink-soft: #4C5652;
    --primary: #24474A; --primary-dark: #163030;
    --accent: #C97E2A; --accent-soft: #EAD3AC; --support: #6E8F63;
    --danger: #A6432B; --border: #D6D0BE; --radius-card: 4px;
  }
  *{ box-sizing: border-box; }
  body{
    margin: 0; background: var(--bg); color: var(--ink);
    font-family: 'Atkinson Hyperlegible', Arial, sans-serif; font-size: 17px; line-height: 1.55;
    -webkit-font-smoothing: antialiased; min-height: 100vh;
  }
  h1, h2, h3{ font-family: 'Source Serif 4', Georgia, serif; font-weight: 600; color: var(--ink); margin: 0; line-height: 1.15; }
  p{ margin: 0; color: var(--ink-soft); }
  a{ color: inherit; text-decoration: none; }
  :focus-visible{ outline: 2px solid var(--accent); outline-offset: 3px; }

  .top{ display: flex; align-items: center; justify-content: space-between; padding: 28px 24px 0; max-width: 560px; margin: 0 auto; }
  .top.centered{ justify-content: center; }
  .logo{ font-family: 'Source Serif 4', serif; font-weight: 700; font-size: 24px; color: var(--primary-dark); }
  .back{ font-size: 14px; font-weight: 700; color: var(--ink-soft); }
  .back:hover{ color: var(--accent); }

  .auth-card{
    background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-card);
    padding: 36px 32px; box-shadow: 0 1px 0 var(--border);
  }
  .tag{
    display: inline-block; font-size: 13px; font-weight: 700; color: var(--primary-dark);
    background: #E4EEEC; padding: 3px 10px; border-radius: 3px; margin-bottom: 14px;
  }
  .auth-card h1{ font-size: 26px; }
  .auth-card .sub{ margin-top: 8px; font-size: 15px; }

  .field{ margin-top: 20px; }
  .field label{ display: block; font-size: 13.5px; font-weight: 700; color: var(--ink); margin-bottom: 6px; }
  .field input, .field select, .field textarea{
    width: 100%; padding: 11px 13px; font-size: 15px; font-family: 'Atkinson Hyperlegible', sans-serif;
    border: 1.5px solid var(--border); border-radius: 3px; background: var(--bg); color: var(--ink);
  }
  .field textarea{ resize: vertical; min-height: 72px; }
  .field input:focus, .field select:focus, .field textarea:focus{ outline: none; border-color: var(--primary); background: var(--surface); }
  .field-hint{ margin-top: 5px; font-size: 12.5px; color: var(--ink-soft); }
  .grid-2{ display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

  .row-between{ margin-top: 18px; display: flex; align-items: center; justify-content: space-between; font-size: 14px; }
  .checkbox-line{ display: flex; align-items: flex-start; gap: 10px; font-size: 14px; }
  .checkbox-line input{ width: 16px; height: 16px; margin-top: 3px; accent-color: var(--primary); flex-shrink: 0; }
  .link-accent{ color: var(--accent); font-weight: 700; }
  .link-accent:hover{ text-decoration: underline; }

  .btn{
    display: inline-flex; width: 100%; align-items: center; justify-content: center; padding: 13px 24px;
    font-family: 'Atkinson Hyperlegible', sans-serif; font-size: 16px; font-weight: 700;
    border-radius: var(--radius-card); border: 1.5px solid transparent; cursor: pointer;
  }
  .btn-primary{ background: var(--accent); color: #24140A; margin-top: 26px; }
  .btn-primary:hover{ background: #B36F22; }

  .divider{ margin: 26px 0 20px; display: flex; align-items: center; gap: 12px; font-size: 13px; color: var(--ink-soft); }
  .divider::before, .divider::after{ content: ""; flex: 1; height: 1px; background: var(--border); }

  .note{
    margin-top: 20px; padding: 14px 16px; background: var(--surface-alt);
    border-left: 3px solid var(--support); border-radius: 2px; font-size: 13.5px; color: var(--ink-soft);
  }
  .alert{
    margin-top: 20px; padding: 12px 14px; border-radius: 3px; background: #F5E4DC;
    border-left: 3px solid var(--danger); color: #6A2A18; font-size: 13.5px;
  }
  .foot-note{ margin-top: 22px; text-align: center; font-size: 14.5px; }

  @media (max-width: 480px){ .grid-2{ grid-template-columns: 1fr; } }
</style>
@stack('estilos')
</head>
<body>

@yield('body')

</body>
</html>
