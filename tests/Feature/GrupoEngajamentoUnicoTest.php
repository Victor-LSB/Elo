<?php

namespace Tests\Feature;

use App\Models\Candidatura;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\MontaCenario;
use Tests\TestCase;

/**
 * Regra de negócio: um grupo não pode ter mais de uma candidatura pendente
 * ou demanda em execução ao mesmo tempo.
 *
 * É validação de aplicação (não há constraint no banco), então é o tipo de
 * regra que quebra em silêncio se alguém mexer em Grupo::temEngajamentoAtivo()
 * ou esquecer a checagem no controller. Daí os testes cobrirem os dois níveis.
 */
class GrupoEngajamentoUnicoTest extends TestCase
{
    use RefreshDatabase, MontaCenario;

    public function test_grupo_sem_candidatura_esta_livre(): void
    {
        $grupo = $this->criarGrupo();

        $this->assertFalse($grupo->temEngajamentoAtivo());
    }

    public function test_candidatura_pendente_bloqueia_o_grupo(): void
    {
        $grupo = $this->criarGrupo();
        $this->criarCandidatura($this->criarDemanda(), $grupo);

        $this->assertTrue($grupo->temEngajamentoAtivo());
    }

    public function test_demanda_em_execucao_bloqueia_o_grupo(): void
    {
        $grupo = $this->criarGrupo();
        $demanda = $this->criarDemanda(['status' => 'em_execucao']);
        $this->criarCandidatura($demanda, $grupo, 'aprovada');

        $this->assertTrue($grupo->temEngajamentoAtivo());
    }

    public function test_candidatura_rejeitada_libera_o_grupo(): void
    {
        $grupo = $this->criarGrupo();
        $this->criarCandidatura($this->criarDemanda(), $grupo, 'rejeitada');

        $this->assertFalse($grupo->temEngajamentoAtivo());
    }

    public function test_demanda_concluida_libera_o_grupo(): void
    {
        $grupo = $this->criarGrupo();
        $demanda = $this->criarDemanda(['status' => 'concluida']);
        $this->criarCandidatura($demanda, $grupo, 'aprovada');

        $this->assertFalse($grupo->temEngajamentoAtivo());
    }

    public function test_controller_recusa_segunda_candidatura(): void
    {
        Notification::fake();

        $estudante = $this->criarEstudante();
        $grupo = $this->criarGrupo([$estudante]);

        // Grupo já engajado em uma demanda
        $this->criarCandidatura($this->criarDemanda(), $grupo);

        $outraDemanda = $this->criarDemanda();

        $resposta = $this->actingAs($estudante)->post(
            route('estudante.demandas.candidatar', $outraDemanda),
            ['mensagem' => 'Queremos participar desta demanda também, temos experiência.']
        );

        $resposta->assertSessionHasErrors('grupo');

        $this->assertDatabaseMissing('candidaturas', [
            'demanda_id' => $outraDemanda->id,
            'grupo_id' => $grupo->id,
        ]);
    }

    public function test_controller_aceita_candidatura_de_grupo_livre(): void
    {
        Notification::fake();

        $estudante = $this->criarEstudante();
        $grupo = $this->criarGrupo([$estudante]);
        $demanda = $this->criarDemanda(['professor_id' => $this->criarProfessor()->id]);

        $this->actingAs($estudante)->post(
            route('estudante.demandas.candidatar', $demanda),
            ['mensagem' => 'Temos experiência prévia com este tipo de projeto.']
        )->assertRedirect(route('estudante.demandas.index'));

        $this->assertDatabaseHas('candidaturas', [
            'demanda_id' => $demanda->id,
            'grupo_id' => $grupo->id,
            'status' => 'pendente',
        ]);
    }

    public function test_grupo_preterido_volta_a_ficar_livre(): void
    {
        Notification::fake();

        $professor = $this->criarProfessor();
        $demanda = $this->criarDemanda(['professor_id' => $professor->id]);

        $grupoVencedor = $this->criarGrupo();
        $grupoPreterido = $this->criarGrupo();

        $vencedora = $this->criarCandidatura($demanda, $grupoVencedor);
        $this->criarCandidatura($demanda, $grupoPreterido);

        $this->assertTrue($grupoPreterido->temEngajamentoAtivo());

        $this->actingAs($professor)->post(route('professor.candidaturas.aprovar', $vencedora));

        // O grupo preterido precisa poder se candidatar a outra coisa.
        $this->assertFalse($grupoPreterido->refresh()->temEngajamentoAtivo());
        $this->assertTrue($grupoVencedor->refresh()->temEngajamentoAtivo());

        $this->assertDatabaseHas('candidaturas', [
            'grupo_id' => $grupoPreterido->id,
            'status' => 'rejeitada_automatica',
        ]);
    }
}
