@extends('layouts.dashboard')

@section('titulo', 'Triagem — Elo')
@section('sidebar-role', 'Coordenação de extensão')
@section('sidebar-nav')
  @include('partials.sidebar.coordenacao')
@endsection

@push('estilos')
<style>
  .content{ max-width: 760px; }
  .desc-text{ font-size: 14.5px; }
  .meta-grid{ display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 16px; }
  .meta-item .k{ font-size: 12px; font-weight: 700; color: var(--ink-soft); text-transform: uppercase; letter-spacing: 0.03em; }
  .meta-item .v{ margin-top: 3px; font-size: 14.5px; color: var(--ink); }

  .level-pick{ display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 6px; }
  .level-opt{ border: 1.5px solid var(--border); border-radius: 3px; padding: 12px 10px; cursor: pointer; display: block; }
  .level-opt input{ display: none; }
  .level-opt.active{ border-color: var(--support); background: #E1EADB; }
  .level-opt .tag{ font-size: 12px; font-weight: 700; color: var(--support); }
  .level-opt .name{ font-size: 14px; font-weight: 700; margin-top: 4px; }
  .level-opt .desc{ font-size: 12px; color: var(--ink-soft); margin-top: 3px; }

  .ethics-card{ border-color: var(--accent); }
  .ethics-q{ font-size: 14.5px; font-weight: 700; color: var(--ink); margin-bottom: 10px; }
  .yn-pick{ display: flex; gap: 10px; }
  .yn-opt{
    flex: 1; text-align: center; padding: 10px; border: 1.5px solid var(--border); border-radius: 3px;
    font-size: 14px; font-weight: 700; color: var(--ink-soft); cursor: pointer; display: block;
  }
  .yn-opt input{ display: none; }
  .yn-opt.active-yes{ border-color: var(--danger); background: var(--danger-soft); color: #6A2A18; }
  .yn-opt.active-no{ border-color: var(--support); background: #E1EADB; color: #3D5433; }
  .justificativa{ display: none; }
  .justificativa.show{ display: block; }
  .flag-note{
    margin-top: 14px; padding: 12px 14px; background: var(--danger-soft); border-left: 3px solid var(--danger);
    border-radius: 2px; font-size: 13px; color: #6A2A18; display: none;
  }
  .flag-note.show{ display: block; }
  .form-actions{ margin-top: 8px; display: flex; gap: 12px; }

  @media (max-width: 760px){
    .meta-grid, .level-pick{ grid-template-columns: 1fr; }
  }
</style>
@endpush

@section('topbar')
  <a href="{{ route('coordenacao.triagem.index') }}" class="back">← Fila de triagem</a>
  <h1>Triagem de demanda</h1>
  <p class="sub">{{ $demanda->titulo }} · {{ $demanda->instituicao->nome }}</p>
@endsection

@section('content')
  <div class="card">
    <h2>O que a instituição enviou</h2>
    <p class="desc-text">{{ $demanda->descricao }}</p>
    <div class="meta-grid">
      <div class="meta-item"><div class="k">Área sugerida</div><div class="v">{{ $demanda->area_sugerida }}</div></div>
      <div class="meta-item"><div class="k">Nível estimado pela instituição</div><div class="v">{{ $demanda->nivel_estimado }}</div></div>
    </div>
  </div>

  <form method="POST" action="{{ route('coordenacao.triagem.store', $demanda) }}">
    @csrf

    <div class="card">
      <h2>Classificação</h2>
      <p class="sub-h">Confirme ou ajuste o nível e defina o departamento responsável.</p>

      <div class="field">
        <label>Nível de complexidade final</label>
        <div class="level-pick">
          @foreach ([
            1 => ['Simples', '4–10h · validado por presença'],
            2 => ['Médio', '15–30h · validado por relatório'],
            3 => ['Complexo', '40–80h+ · milestones'],
          ] as $nivel => [$nome, $desc])
            <label class="level-opt {{ (int) old('nivel_complexidade', $demanda->nivel_estimado) === $nivel ? 'active' : '' }}">
              <input type="radio" name="nivel_complexidade" value="{{ $nivel }}" @checked((int) old('nivel_complexidade', $demanda->nivel_estimado) === $nivel)>
              <div class="tag">Nível {{ $nivel }}</div>
              <div class="name">{{ $nome }}</div>
              <div class="desc">{{ $desc }}</div>
            </label>
          @endforeach
        </div>
      </div>

      <div class="grid-2">
        <div class="field">
          <label for="departamento_id">Departamento responsável</label>
          <select id="departamento_id" name="departamento_id" required>
            <option value="" disabled selected>Selecione o departamento</option>
            @foreach ($departamentos as $departamento)
              <option value="{{ $departamento->id }}" @selected(old('departamento_id') == $departamento->id)>{{ $departamento->nome }}</option>
            @endforeach
          </select>
        </div>
        <div class="field">
          <label for="professor_id">Professor responsável</label>
          <select id="professor_id" name="professor_id" required>
            <option value="" disabled selected>Selecione o professor</option>
            @foreach ($professores as $professor)
              <option value="{{ $professor->id }}" @selected(old('professor_id') == $professor->id)>
                {{ $professor->nome }}{{ $professor->departamento ? ' — '.$professor->departamento->nome : '' }}
              </option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="grid-2">
        <div class="field">
          <label for="horas_min">Horas mínimas</label>
          <input type="number" id="horas_min" name="horas_min" value="{{ old('horas_min', 4) }}" min="1" required>
        </div>
        <div class="field">
          <label for="horas_max">Horas máximas</label>
          <input type="number" id="horas_max" name="horas_max" value="{{ old('horas_max', 10) }}" min="1" required>
        </div>
      </div>

      <div class="field">
        <label for="prazo_candidatura_dias">Prazo para candidatura (dias úteis)</label>
        <select id="prazo_candidatura_dias" name="prazo_candidatura_dias">
          @foreach ([5, 10, 15] as $dias)
            <option value="{{ $dias }}" @selected(old('prazo_candidatura_dias', 5) == $dias)>
              {{ $dias }} dias úteis{{ $dias === 5 ? ' (padrão)' : '' }}
            </option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="card ethics-card">
      <h2>Checklist ético</h2>
      <p class="sub-h">Obrigatório antes de aprovar (RF-06) — previne que a demanda substitua um serviço que deveria ser contratado.</p>

      <div class="ethics-q">Esta demanda substitui um serviço que a instituição deveria contratar profissionalmente?</div>
      <div class="yn-pick">
        <label class="yn-opt {{ old('substitui_servico_profissional') == '1' ? 'active-yes' : '' }}" id="ynSim">
          <input type="radio" name="substitui_servico_profissional" value="1" @checked(old('substitui_servico_profissional') == '1')>
          Sim
        </label>
        <label class="yn-opt {{ old('substitui_servico_profissional') == '1' ? '' : 'active-no' }}" id="ynNao">
          <input type="radio" name="substitui_servico_profissional" value="0" @checked(old('substitui_servico_profissional') != '1')>
          Não
        </label>
      </div>

      <div class="justificativa {{ old('substitui_servico_profissional') == '1' ? 'show' : '' }}" id="justificativaBox">
        <div class="field" style="margin-top:14px;">
          <label for="justificativa">Justificativa (obrigatória)</label>
          <textarea id="justificativa" name="justificativa" placeholder="Explique por que, mesmo assim, a demanda deve seguir para revisão adicional em vez de ser recusada.">{{ old('justificativa') }}</textarea>
          <div class="field-hint">Mínimo de 20 caracteres.</div>
        </div>
      </div>

      <div class="flag-note {{ old('substitui_servico_profissional') == '1' ? 'show' : '' }}" id="flagNote">
        ⚠ Esta demanda será sinalizada para <strong>revisão adicional</strong> antes de poder ser aprovada, conforme a justificativa registrada acima.
      </div>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary" id="btnAprovar">
        {{ old('substitui_servico_profissional') == '1' ? 'Enviar para revisão adicional' : 'Aprovar e abrir para candidatura' }}
      </button>
      <a href="{{ route('coordenacao.triagem.index') }}" class="btn btn-outline">Cancelar</a>
    </div>
  </form>
@endsection

@push('scripts')
<script>
  document.querySelectorAll('.level-opt').forEach(opt => {
    opt.addEventListener('click', () => {
      document.querySelectorAll('.level-opt').forEach(o => o.classList.remove('active'));
      opt.classList.add('active');
    });
  });

  const simEl = document.getElementById('ynSim');
  const naoEl = document.getElementById('ynNao');
  const justBox = document.getElementById('justificativaBox');
  const flagNote = document.getElementById('flagNote');
  const btnAprovar = document.getElementById('btnAprovar');

  function marcarEtico(substitui){
    simEl.classList.toggle('active-yes', substitui);
    naoEl.classList.toggle('active-no', !substitui);
    justBox.classList.toggle('show', substitui);
    flagNote.classList.toggle('show', substitui);
    btnAprovar.textContent = substitui ? 'Enviar para revisão adicional' : 'Aprovar e abrir para candidatura';
  }

  simEl.addEventListener('click', () => marcarEtico(true));
  naoEl.addEventListener('click', () => marcarEtico(false));
</script>
@endpush
