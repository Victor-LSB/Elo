<?php

namespace Tests\Feature;

use App\Models\Milestone;
use App\Models\Validacao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\MontaCenario;
use Tests\TestCase;

class SmokeReportedBugsRound3Test extends TestCase
{
    use RefreshDatabase;
    use MontaCenario;

    public function test_concluir_milestone_libera_o_proximo(): void
    {
        Notification::fake();

        $professor = $this->criarProfessor();
        $demanda = $this->criarDemanda(['nivel_complexidade' => 2, 'professor_id' => $professor->id, 'status' => 'em_execucao']);
        $grupo = $this->criarGrupo();
        $this->criarCandidatura($demanda, $grupo, 'aprovada');

        $m1 = $this->criarMilestone($demanda, ['ordem' => 1, 'status' => 'em_andamento']);
        $m2 = $this->criarMilestone($demanda, ['ordem' => 2, 'status' => 'nao_iniciado']);
        $m3 = $this->criarMilestone($demanda, ['ordem' => 3, 'status' => 'nao_iniciado']);

        $m1->update(['status' => 'aguardando_validacao']);
        $validacao = Validacao::create(['milestone_id' => $m1->id]);
        $validacao->aprovarComoProfessor($professor);

        $this->assertSame('concluido', $m1->fresh()->status);
        // O 2º milestone deve abrir para envio...
        $this->assertSame('em_andamento', $m2->fresh()->status);
        // ...e o 3º continua bloqueado até o 2º ser concluído.
        $this->assertSame('nao_iniciado', $m3->fresh()->status);
    }

    public function test_entrega_so_pode_ser_aberta_por_quem_tem_relacao_com_a_demanda(): void
    {
        Storage::fake('local');

        $professorCerto = $this->criarProfessor();
        $professorErrado = $this->criarProfessor();
        $demanda = $this->criarDemanda(['professor_id' => $professorCerto->id, 'status' => 'em_execucao']);
        $instUser = $demanda->instituicao->usuario;
        $estudante = $this->criarEstudante();
        $grupo = $this->criarGrupo([$estudante]);
        $this->criarCandidatura($demanda, $grupo, 'aprovada');

        $milestone = Milestone::create([
            'demanda_id' => $demanda->id, 'ordem' => 1, 'titulo' => 'M1',
            'horas_creditadas' => 10, 'prazo' => now()->addDays(7),
            'status' => 'aguardando_validacao',
            'entrega_path' => UploadedFile::fake()->create('relatorio.pdf', 100)->store('entregas/milestones'),
        ]);

        // Professor responsável, instituição dona e o próprio estudante conseguem abrir.
        $this->actingAs($professorCerto)->get(route('milestones.entrega', $milestone))->assertOk();
        $this->actingAs($instUser)->get(route('milestones.entrega', $milestone))->assertOk();
        $this->actingAs($estudante)->get(route('milestones.entrega', $milestone))->assertOk();

        // Um professor sem relação com a demanda não pode.
        $this->actingAs($professorErrado)->get(route('milestones.entrega', $milestone))->assertForbidden();
    }
}
