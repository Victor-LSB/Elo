<?php

namespace Tests\Feature;

use App\Models\Candidatura;
use App\Models\Demanda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\MontaCenario;
use Tests\TestCase;

class SmokeReportedBugsTest extends TestCase
{
    use RefreshDatabase;
    use MontaCenario;

    private function criarCoordenacao(): User
    {
        return User::create([
            'nome' => 'Coord Teste',
            'email' => 'coord'.uniqid().'@elo.test',
            'password' => Hash::make('password'),
            'papel' => 'coordenacao',
        ]);
    }

    public function test_erro1_triagem_aprovar_candidatura(): void
    {
        $coord = $this->criarCoordenacao();
        $dep = $this->criarDepartamento();
        $prof = $this->criarProfessor($dep);
        $demanda = $this->criarDemanda(['status' => 'pendente_triagem', 'nivel_complexidade' => null, 'data_abertura_candidatura' => null]);

        $resp = $this->actingAs($coord)->post("/coordenacao/triagem/{$demanda->id}", [
            'nivel_complexidade' => 2, 'departamento_id' => $dep->id, 'professor_id' => $prof->id,
            'horas_min' => 20, 'horas_max' => 40, 'substitui_servico_profissional' => '0',
        ]);
        $resp->assertSessionHasNoErrors();
        $resp->assertRedirect();
        $this->assertSame('aberta_candidatura', $demanda->fresh()->status);
    }

    public function test_erro2_pagina_professores_coordenacao(): void
    {
        $coord = $this->criarCoordenacao();
        $this->criarProfessor();

        $resp = $this->actingAs($coord)->get('/coordenacao/professores');
        $resp->assertOk();
        $resp->assertSee('Prof. Teste');
    }

    public function test_erro3_instituicao_ve_detalhes_demanda_com_grupo(): void
    {
        $dep = $this->criarDepartamento();
        $prof = $this->criarProfessor($dep);
        $demanda = $this->criarDemanda(['departamento_id' => $dep->id, 'professor_id' => $prof->id, 'status' => 'em_execucao']);
        $instUser = $demanda->instituicao->usuario;
        $grupo = $this->criarGrupo();
        Candidatura::create(['demanda_id' => $demanda->id, 'grupo_id' => $grupo->id, 'status' => 'aprovada', 'mensagem' => 'Proposta de teste com mais de vinte caracteres.']);

        $resp = $this->actingAs($instUser)->get("/instituicao/demandas/{$demanda->id}");
        $resp->assertOk();
    }

    public function test_erro4_perfil_instituicao(): void
    {
        $instituicao = $this->criarInstituicao();
        $instUser = $instituicao->usuario;

        $resp = $this->actingAs($instUser)->get('/instituicao/perfil');
        $resp->assertOk();
        $resp->assertSee('ONG Teste');
    }

    public function test_erro5_convidar_email_inexistente_nao_da_404(): void
    {
        $estudante = $this->criarEstudante();
        $grupo = $this->criarGrupo([$estudante]);

        $resp = $this->actingAs($estudante)->post('/estudante/grupo/convidar', ['identificador' => 'naoexiste@x.com']);
        $resp->assertSessionHasErrors('identificador');
        $this->assertNotEquals(404, $resp->getStatusCode());
    }
}