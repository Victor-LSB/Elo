<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\NovaCandidatura;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\MontaCenario;
use Tests\TestCase;

/**
 * Integração da central de notificações no layout e dos contadores da barra
 * lateral (alimentados por App\View\Composers\SidebarComposer).
 */
class CentralNotificacoesTest extends TestCase
{
    use RefreshDatabase, MontaCenario;

    public function test_notificacao_aparece_no_dashboard(): void
    {
        $professor = $this->criarProfessor();
        $demanda = $this->criarDemanda(['professor_id' => $professor->id]);
        $candidatura = $this->criarCandidatura($demanda, $this->criarGrupo());

        $professor->notify(new NovaCandidatura($candidatura));

        $this->actingAs($professor)
            ->get(route('professor.demandas.index'))
            ->assertOk()
            ->assertSee('Nova candidatura recebida');
    }

    public function test_abrir_notificacao_marca_como_lida(): void
    {
        $professor = $this->criarProfessor();
        $demanda = $this->criarDemanda(['professor_id' => $professor->id]);
        $candidatura = $this->criarCandidatura($demanda, $this->criarGrupo());

        $professor->notify(new NovaCandidatura($candidatura));
        $notificacao = $professor->unreadNotifications()->first();

        $this->actingAs($professor)
            ->get(route('notificacoes.abrir', $notificacao->id))
            ->assertRedirect(route('professor.candidaturas.show', $demanda, absolute: false));

        $this->assertSame(0, $professor->unreadNotifications()->count());
    }

    public function test_usuario_nao_abre_notificacao_de_outro(): void
    {
        $professor = $this->criarProfessor();
        $outro = $this->criarProfessor();
        $demanda = $this->criarDemanda(['professor_id' => $professor->id]);

        $professor->notify(new NovaCandidatura($this->criarCandidatura($demanda, $this->criarGrupo())));
        $notificacao = $professor->unreadNotifications()->first();

        $this->actingAs($outro)
            ->get(route('notificacoes.abrir', $notificacao->id))
            ->assertNotFound();
    }

    public function test_marcar_todas_como_lidas(): void
    {
        $professor = $this->criarProfessor();
        $demanda = $this->criarDemanda(['professor_id' => $professor->id]);

        $professor->notify(new NovaCandidatura($this->criarCandidatura($demanda, $this->criarGrupo())));
        $professor->notify(new NovaCandidatura($this->criarCandidatura($demanda, $this->criarGrupo())));

        $this->assertSame(2, $professor->unreadNotifications()->count());

        $this->actingAs($professor)->post(route('notificacoes.lidas'));

        $this->assertSame(0, $professor->unreadNotifications()->count());
    }

    public function test_contador_de_candidaturas_do_professor(): void
    {
        $professor = $this->criarProfessor();
        $demanda = $this->criarDemanda(['professor_id' => $professor->id]);
        $this->criarCandidatura($demanda, $this->criarGrupo());

        $this->actingAs($professor)
            ->get(route('professor.demandas.index'))
            ->assertViewHas('candidaturasPendentes', 1);
    }

    public function test_contadores_da_coordenacao(): void
    {
        $coordenacao = User::create([
            'nome' => 'Coordenação',
            'email' => 'coord-sidebar@elo.test',
            'password' => Hash::make('password'),
            'papel' => 'coordenacao',
        ]);

        $this->criarDemanda(['status' => 'pendente_triagem']);
        $this->criarInstituicao('pendente');

        $this->actingAs($coordenacao)
            ->get(route('coordenacao.triagem.index'))
            ->assertViewHas('filaPendente', 1)
            ->assertViewHas('instituicoesPendentes', 1);
    }

    public function test_contador_de_excecoes_do_admin(): void
    {
        $admin = User::create([
            'nome' => 'Admin',
            'email' => 'admin-sidebar@elo.test',
            'password' => Hash::make('password'),
            'papel' => 'admin',
        ]);

        $demanda = $this->criarDemanda(['status' => 'em_execucao']);
        $this->criarMilestone($demanda, ['status' => 'em_risco']);

        $this->actingAs($admin)
            ->get(route('admin.excecoes.index'))
            ->assertViewHas('excecoesPendentes', 1);
    }
}
