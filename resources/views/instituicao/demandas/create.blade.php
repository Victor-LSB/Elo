@extends('layouts.dashboard')

@section('titulo', 'Nova demanda — Elo')
@section('sidebar-role', 'Instituição parceira')
@section('sidebar-nav')
  @include('partials.sidebar.instituicao')
@endsection

@push('estilos')
<style>
  .topbar-conteudo{ display: flex; align-items: center; justify-content: space-between; gap: 20px; }
  .content{ max-width: 680px; }
  .level-pick{ display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 6px; }
  .level-opt{ border: 1.5px solid var(--border); border-radius: 3px; padding: 12px 10px; cursor: pointer; display: block; }
  .level-opt input{ display: none; }
  .level-opt.active{ border-color: var(--support); background: #E1EADB; }
  .level-opt .tag{ font-size: 12px; font-weight: 700; color: var(--support); }
  .level-opt .name{ font-size: 14px; font-weight: 700; margin-top: 4px; }
  .level-opt .desc{ font-size: 12px; color: var(--ink-soft); margin-top: 3px; }
  .note{
    margin-top: 24px; padding: 14px 16px; background: var(--surface-alt);
    border-left: 3px solid var(--primary); border-radius: 2px; font-size: 13.5px; color: var(--ink-soft);
  }
  .form-actions{ margin-top: 28px; display: flex; gap: 12px; }
  @media (max-width: 760px){
    .topbar-conteudo{ flex-direction: column; align-items: flex-start; }
    .level-pick{ grid-template-columns: 1fr; }
  }
</style>
@endpush

@section('topbar')
  <div>
    <h1>Nova demanda</h1>
    <p class="sub">Descreva a necessidade real. A coordenação de extensão faz a triagem antes de publicar.</p>
  </div>
  <a href="{{ route('instituicao.demandas.index') }}" class="back" style="margin:0;">← Voltar</a>
@endsection

@section('content')
  <div class="card">
    <form method="POST" action="{{ route('instituicao.demandas.store') }}">
      @csrf

      <div class="field">
        <label for="titulo">Título da demanda</label>
        <input type="text" id="titulo" name="titulo" value="{{ old('titulo') }}" placeholder="Ex.: Sistema de cadastro de beneficiários" required>
      </div>

      <div class="field">
        <label for="descricao">Descrição</label>
        <textarea id="descricao" name="descricao" placeholder="Explique o problema real que a instituição enfrenta hoje e o que espera como resultado." required>{{ old('descricao') }}</textarea>
      </div>

      <div class="field">
        <label for="area_sugerida">Área sugerida</label>
        <select id="area_sugerida" name="area_sugerida" required>
          <option value="" disabled {{ old('area_sugerida') ? '' : 'selected' }}>Selecione a área</option>
          @foreach (['Tecnologia', 'Educação', 'Design', 'Administração', 'Comunicação', 'Assistência social', 'Outra'] as $area)
            <option value="{{ $area }}" @selected(old('area_sugerida') === $area)>{{ $area }}</option>
          @endforeach
        </select>
      </div>

      <div class="field">
        <label>Nível de complexidade estimado</label>
        <div class="level-pick">
          @foreach ([
            1 => ['Simples', '4–10h · validado por presença'],
            2 => ['Médio', '15–30h · validado por relatório'],
            3 => ['Complexo', '40–80h+ · dividido em milestones'],
          ] as $nivel => [$nome, $desc])
            <label class="level-opt {{ (int) old('nivel_estimado', 2) === $nivel ? 'active' : '' }}">
              <input type="radio" name="nivel_estimado" value="{{ $nivel }}" @checked((int) old('nivel_estimado', 2) === $nivel)>
              <div class="tag">Nível {{ $nivel }}</div>
              <div class="name">{{ $nome }}</div>
              <div class="desc">{{ $desc }}</div>
            </label>
          @endforeach
        </div>
        <div class="field-hint">A coordenação confirma ou ajusta o nível na triagem.</div>
      </div>

      <div class="note">
        Depois de enviada, a demanda entra em <strong>triagem</strong>: a coordenação define o nível final, o departamento responsável e aplica o checklist ético antes de abrir para candidatura.
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Enviar para triagem</button>
        <a href="{{ route('instituicao.demandas.index') }}" class="btn btn-outline">Cancelar</a>
      </div>
    </form>
  </div>
@endsection

@push('scripts')
<script>
  document.querySelectorAll('.level-opt').forEach(opt => {
    opt.addEventListener('click', () => {
      document.querySelectorAll('.level-opt').forEach(o => o.classList.remove('active'));
      opt.classList.add('active');
    });
  });
</script>
@endpush
