<?php

namespace Tests\Feature;

use App\Models\Certificado;
use App\Models\Validacao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\MontaCenario;
use Tests\TestCase;

/**
 * RF-05.3: em demandas de Nível 3, um milestone só é concluído (e as horas
 * só contam) com aprovação do professor E da instituição. Níveis 1 e 2
 * aceitam um único validador.
 *
 * A regra mora em Milestone::podeSerConcluido(), chamada indiretamente por
 * Validacao — se alguém trocar o && por ||, as horas passam a ser creditadas
 * com metade da validação e nada no sistema reclama. Daí os testes.
 */
class DuplaValidacaoMilestoneTest extends TestCase
{
    use RefreshDatabase, MontaCenario;

    public function test_nivel_3_nao_conclui_so_com_professor(): void
    {
        Notification::fake();

        $professor = $this->criarProfessor();
        $demanda = $this->criarDemanda([
            'nivel_complexidade' => 3,
            'professor_id' => $professor->id,
        ]);
        $milestone = $this->criarMilestone($demanda);
        $validacao = Validacao::create(['milestone_id' => $milestone->id]);

        $validacao->aprovarComoProfessor($professor);

        $this->assertFalse($milestone->refresh()->podeSerConcluido());
        $this->assertSame('aguardando_validacao', $milestone->status);
    }

    public function test_nivel_3_nao_conclui_so_com_instituicao(): void
    {
        Notification::fake();

        $demanda = $this->criarDemanda([
            'nivel_complexidade' => 3,
            'professor_id' => $this->criarProfessor()->id,
        ]);
        $milestone = $this->criarMilestone($demanda);
        $validacao = Validacao::create(['milestone_id' => $milestone->id]);

        $validacao->aprovarComoInstituicao();

        $this->assertFalse($milestone->refresh()->podeSerConcluido());
        $this->assertSame('aguardando_validacao', $milestone->status);
    }

    public function test_nivel_3_conclui_com_as_duas_aprovacoes(): void
    {
        Notification::fake();

        $professor = $this->criarProfessor();
        $demanda = $this->criarDemanda([
            'nivel_complexidade' => 3,
            'professor_id' => $professor->id,
        ]);
        $milestone = $this->criarMilestone($demanda);
        $validacao = Validacao::create(['milestone_id' => $milestone->id]);

        $validacao->aprovarComoProfessor($professor);
        $validacao->aprovarComoInstituicao();

        $milestone->refresh();
        $this->assertSame('concluido', $milestone->status);
        $this->assertNotNull($milestone->data_conclusao);
    }

    public function test_nivel_2_conclui_com_uma_aprovacao(): void
    {
        Notification::fake();

        $professor = $this->criarProfessor();
        $demanda = $this->criarDemanda([
            'nivel_complexidade' => 2,
            'professor_id' => $professor->id,
        ]);
        $milestone = $this->criarMilestone($demanda);
        $validacao = Validacao::create(['milestone_id' => $milestone->id]);

        $validacao->aprovarComoProfessor($professor);

        $this->assertSame('concluido', $milestone->refresh()->status);
    }

    public function test_conclusao_de_todos_os_milestones_emite_certificado(): void
    {
        Notification::fake();

        $professor = $this->criarProfessor();
        $estudanteA = $this->criarEstudante('Estudante A');
        $estudanteB = $this->criarEstudante('Estudante B');
        $grupo = $this->criarGrupo([$estudanteA, $estudanteB]);

        $demanda = $this->criarDemanda([
            'nivel_complexidade' => 3,
            'professor_id' => $professor->id,
            'status' => 'em_execucao',
        ]);
        $this->criarCandidatura($demanda, $grupo, 'aprovada');

        $m1 = $this->criarMilestone($demanda, ['ordem' => 1, 'horas_creditadas' => 12]);
        $m2 = $this->criarMilestone($demanda, ['ordem' => 2, 'horas_creditadas' => 18]);

        foreach ([$m1, $m2] as $milestone) {
            $validacao = Validacao::create(['milestone_id' => $milestone->id]);
            $validacao->aprovarComoProfessor($professor);
            $validacao->aprovarComoInstituicao();
        }

        $this->assertSame('concluida', $demanda->refresh()->status);

        // RF-05.4/05.5: cada membro recebe certificado com a soma das horas.
        $this->assertSame(2, Certificado::where('demanda_id', $demanda->id)->count());
        foreach ([$estudanteA, $estudanteB] as $estudante) {
            $this->assertDatabaseHas('certificados', [
                'estudante_id' => $estudante->id,
                'demanda_id' => $demanda->id,
                'horas_totais' => 30,
            ]);
        }
    }

    public function test_certificado_nao_e_emitido_com_milestone_pendente(): void
    {
        Notification::fake();

        $professor = $this->criarProfessor();
        $grupo = $this->criarGrupo();
        $demanda = $this->criarDemanda([
            'nivel_complexidade' => 3,
            'professor_id' => $professor->id,
            'status' => 'em_execucao',
        ]);
        $this->criarCandidatura($demanda, $grupo, 'aprovada');

        $m1 = $this->criarMilestone($demanda, ['ordem' => 1]);
        $this->criarMilestone($demanda, ['ordem' => 2, 'status' => 'em_andamento']);

        $validacao = Validacao::create(['milestone_id' => $m1->id]);
        $validacao->aprovarComoProfessor($professor);
        $validacao->aprovarComoInstituicao();

        $this->assertSame('em_execucao', $demanda->refresh()->status);
        $this->assertSame(0, Certificado::where('demanda_id', $demanda->id)->count());
    }
}
