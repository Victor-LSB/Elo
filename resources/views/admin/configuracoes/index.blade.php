@extends('layouts.dashboard')

@section('titulo', 'Configurações — Elo')
@section('sidebar-role', 'Elo · Administração')
@section('sidebar-nav')
  @include('partials.sidebar.admin')
@endsection

@push('estilos')
<style>
  .content{ max-width: 760px; }
  .setting-row{ display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 14px 0; border-bottom: 1px solid var(--border); }
  .setting-row:last-of-type{ border-bottom: none; }
  .setting-label{ font-size: 14.5px; font-weight: 700; color: var(--ink); }
  .setting-desc{ font-size: 12.5px; color: var(--ink-soft); margin-top: 2px; max-width: 380px; }
  .setting-input input{
    padding: 8px 12px; font-size: 14px; font-family: 'Atkinson Hyperlegible', sans-serif;
    border: 1.5px solid var(--border); border-radius: 3px; background: var(--bg); color: var(--ink);
    width: 120px; text-align: right;
  }
  .role-toggle{ display: flex; gap: 10px; margin-bottom: 18px; }
  .role-toggle-opt{
    flex: 1; text-align: center; padding: 10px; border: 1.5px solid var(--border); border-radius: 3px;
    font-size: 13.5px; font-weight: 700; color: var(--ink-soft); cursor: pointer; display: block;
  }
  .role-toggle-opt input{ display: none; }
  .role-toggle-opt.active{ border-color: var(--primary); background: #E4EEEC; color: var(--primary); }
  .user-row{ display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border); font-size: 13.5px; }
  .user-row:last-child{ border-bottom: none; }
  .user-row .name{ font-weight: 700; color: var(--ink); }
  .user-row .dept{ color: var(--ink-soft); font-size: 12.5px; }
  .empty{ text-align: center; padding: 20px; color: var(--ink-soft); font-size: 14px; }

  @media (max-width: 760px){
    .setting-row{ flex-direction: column; align-items: flex-start; gap: 8px; }
  }
</style>
@endpush

@section('topbar')
  <h1>Configurações</h1>
  <p class="sub">Parâmetros globais do sistema e contas de professor/coordenação.</p>
@endsection

@section('content')
  <div class="card">
    <h2>Prazos e jobs agendados</h2>
    <p class="sub-h">Valores padrão usados pelo scheduler do Laravel (RNF-04) — podem ser sobrescritos por demanda na triagem.</p>

    <form method="POST" action="{{ route('admin.configuracoes.atualizar') }}">
      @csrf
      @method('PUT')

      @foreach ([
        'prazo_candidatura_dias' => ['Prazo padrão de candidatura', 'Dias úteis que uma demanda fica "aberta para candidatura" antes de precisar de atenção da coordenação.'],
        'dias_ate_em_risco' => ['Prazo até marcar milestone "em risco"', 'Tempo sem atualização do grupo após o prazo do milestone vencer.'],
        'dias_ate_habilitar_reabertura' => ['Prazo até notificar admin sobre risco', 'Tempo adicional em risco antes de habilitar a reabertura para outro grupo (RF-04.4).'],
        'dias_ate_marcar_parada' => ['Demanda "parada" sem candidatura', 'Dias sem nenhuma candidatura recebida para exibir o indicador de "parada".'],
      ] as $chave => [$rotulo, $descricao])
        <div class="setting-row">
          <div>
            <div class="setting-label">{{ $rotulo }}</div>
            <div class="setting-desc">{{ $descricao }}</div>
          </div>
          <div class="setting-input">
            <input type="number" name="{{ $chave }}" value="{{ old($chave, $valores[$chave]) }}" min="1" required>
          </div>
        </div>
      @endforeach

      <div style="margin-top:20px;"><button type="submit" class="btn btn-primary">Salvar parâmetros</button></div>
    </form>
  </div>

  <div class="card">
    <h2>Criar conta de professor ou coordenação</h2>
    <p class="sub-h">RF-01.2 — esses papéis não se autocadastram; a conta é criada aqui pelo admin.</p>

    <form method="POST" action="{{ route('admin.usuarios.store') }}">
      @csrf

      <div class="role-toggle">
        <label class="role-toggle-opt {{ old('papel', 'professor') === 'professor' ? 'active' : '' }}">
          <input type="radio" name="papel" value="professor" @checked(old('papel', 'professor') === 'professor')>
          Professor
        </label>
        <label class="role-toggle-opt {{ old('papel') === 'coordenacao' ? 'active' : '' }}">
          <input type="radio" name="papel" value="coordenacao" @checked(old('papel') === 'coordenacao')>
          Coordenação de extensão
        </label>
      </div>

      <div class="field">
        <label for="nome">Nome completo</label>
        <input type="text" id="nome" name="nome" value="{{ old('nome') }}" placeholder="Nome do professor" required>
      </div>

      <div class="grid-2">
        <div class="field">
          <label for="email">E-mail institucional</label>
          <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="professor@univali.br" required>
        </div>
        <div class="field">
          <label for="departamento_id">Departamento</label>
          <select id="departamento_id" name="departamento_id">
            <option value="">— (apenas para professor)</option>
            @foreach (\App\Models\Departamento::all() as $departamento)
              <option value="{{ $departamento->id }}" @selected(old('departamento_id') == $departamento->id)>{{ $departamento->nome }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div style="margin-top:20px;">
        <button type="submit" class="btn btn-primary">Criar conta e enviar convite</button>
      </div>
    </form>
  </div>

  <div class="card">
    <h2>Professores cadastrados</h2>
    @forelse ($professores as $professor)
      <div class="user-row">
        <span class="name">{{ $professor->nome }}</span>
        <span class="dept">
          {{ $professor->departamento?->nome ?? '—' }} · {{ $professor->demandasComoProfessor()->count() }} demanda(s)
        </span>
      </div>
    @empty
      <div class="empty">Nenhum professor cadastrado ainda.</div>
    @endforelse
  </div>
@endsection

@push('scripts')
<script>
  document.querySelectorAll('.role-toggle-opt input').forEach(input => {
    input.addEventListener('change', () => {
      document.querySelectorAll('.role-toggle-opt').forEach(o => o.classList.remove('active'));
      input.closest('.role-toggle-opt').classList.add('active');
    });
  });
</script>
@endpush
