<?php

namespace Tests\Feature;

use App\Models\Validacao;
use App\Notifications\CadastroInstituicaoAnalisado;
use App\Notifications\CandidaturaRespondida;
use App\Notifications\DemandaTriada;
use App\Notifications\NovaCandidatura;
use App\Notifications\ValidacaoConcluida;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\MontaCenario;
use Tests\TestCase;

/**
 * RF-07.1: notificar instituição, professor e grupo em mudanças de status
 * (candidatura aprovada, milestone em risco, validação concluída).
 */
class NotificacoesTest extends TestCase
{
    use RefreshDatabase, MontaCenario;

    public function test_professor_e_notificado_de_nova_candidatura(): void
    {
        Notification::fake();

        $professor = $this->criarProfessor();
        $demanda = $this->criarDemanda(['professor_id' => $professor->id]);
        $estudante = $this->criarEstudante();
        $this->criarGrupo([$estudante]);

        $this->actingAs($estudante)->post(
            route('estudante.demandas.candidatar', $demanda),
            ['mensagem' => 'Temos experiência prévia com este tipo de projeto.']
        );

        Notification::assertSentTo($professor, NovaCandidatura::class);
    }

    public function test_grupos_sao_notificados_da_resposta_a_candidatura(): void
    {
        Notification::fake();

        $professor = $this->criarProfessor();
        $demanda = $this->criarDemanda(['professor_id' => $professor->id]);

        $vencedor = $this->criarEstudante('Vencedor');
        $preterido = $this->criarEstudante('Preterido');
        $grupoVencedor = $this->criarGrupo([$vencedor]);
        $grupoPreterido = $this->criarGrupo([$preterido]);

        $vencedora = $this->criarCandidatura($demanda, $grupoVencedor);
        $this->criarCandidatura($demanda, $grupoPreterido);

        $this->actingAs($professor)->post(route('professor.candidaturas.aprovar', $vencedora));

        Notification::assertSentTo($vencedor, CandidaturaRespondida::class);
        Notification::assertSentTo($preterido, CandidaturaRespondida::class);
    }

    public function test_partes_sao_notificadas_quando_a_validacao_fecha(): void
    {
        Notification::fake();

        $professor = $this->criarProfessor();
        $instituicao = $this->criarInstituicao();
        $estudante = $this->criarEstudante();
        $grupo = $this->criarGrupo([$estudante]);

        $demanda = $this->criarDemanda([
            'instituicao_id' => $instituicao->id,
            'professor_id' => $professor->id,
            'nivel_complexidade' => 3,
            'status' => 'em_execucao',
        ]);
        $this->criarCandidatura($demanda, $grupo, 'aprovada');
        $milestone = $this->criarMilestone($demanda);

        $validacao = Validacao::create(['milestone_id' => $milestone->id]);
        $validacao->aprovarComoProfessor($professor);
        $validacao->aprovarComoInstituicao();

        Notification::assertSentTo($estudante, ValidacaoConcluida::class);
        Notification::assertSentTo($professor, ValidacaoConcluida::class);
        Notification::assertSentTo($instituicao->usuario, ValidacaoConcluida::class);
    }

    public function test_contraparte_e_avisada_quando_falta_a_aprovacao_dela(): void
    {
        Notification::fake();

        $professor = $this->criarProfessor();
        $instituicao = $this->criarInstituicao();
        $demanda = $this->criarDemanda([
            'instituicao_id' => $instituicao->id,
            'professor_id' => $professor->id,
            'nivel_complexidade' => 3,
        ]);
        $milestone = $this->criarMilestone($demanda);

        Validacao::create(['milestone_id' => $milestone->id])->aprovarComoProfessor($professor);

        Notification::assertSentTo(
            $instituicao->usuario,
            ValidacaoConcluida::class,
            fn ($n) => $n->aguardandoContraparte === true
        );
    }

    public function test_instituicao_e_professor_sao_notificados_apos_a_triagem(): void
    {
        Notification::fake();

        $coordenacao = User::create([
            'nome' => 'Coordenação',
            'email' => 'coord@elo.test',
            'password' => Hash::make('password'),
            'papel' => 'coordenacao',
        ]);
        $departamento = $this->criarDepartamento();
        $professor = $this->criarProfessor($departamento);
        $instituicao = $this->criarInstituicao();

        $demanda = $this->criarDemanda([
            'instituicao_id' => $instituicao->id,
            'status' => 'pendente_triagem',
            'nivel_complexidade' => null,
        ]);

        $this->actingAs($coordenacao)->post(route('coordenacao.triagem.store', $demanda), [
            'nivel_complexidade' => 2,
            'departamento_id' => $departamento->id,
            'professor_id' => $professor->id,
            'horas_min' => 15,
            'horas_max' => 30,
            'substitui_servico_profissional' => 0,
        ]);

        Notification::assertSentTo($instituicao->usuario, DemandaTriada::class);
        Notification::assertSentTo($professor, DemandaTriada::class);
    }

    public function test_instituicao_e_notificada_da_validacao_do_cadastro(): void
    {
        Notification::fake();

        $coordenacao = User::create([
            'nome' => 'Coordenação',
            'email' => 'coord2@elo.test',
            'password' => Hash::make('password'),
            'papel' => 'coordenacao',
        ]);
        $instituicao = $this->criarInstituicao('pendente');

        $this->actingAs($coordenacao)
            ->post(route('coordenacao.instituicoes.validar', $instituicao));

        Notification::assertSentTo($instituicao->usuario, CadastroInstituicaoAnalisado::class);
        $this->assertSame('ativa', $instituicao->refresh()->status);
    }
}
