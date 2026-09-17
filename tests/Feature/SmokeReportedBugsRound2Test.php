<?php

namespace Tests\Feature;

use App\Models\ConviteGrupo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\MontaCenario;
use Tests\TestCase;

class SmokeReportedBugsRound2Test extends TestCase
{
    use RefreshDatabase;
    use MontaCenario;

    private function criarAdmin(): User
    {
        return User::create([
            'nome' => 'Admin Teste',
            'email' => 'admin'.uniqid().'@elo.test',
            'password' => Hash::make('password'),
            'papel' => 'admin',
        ]);
    }

    public function test_erro1_admin_cria_professor_envia_convite_de_senha(): void
    {
        $admin = $this->criarAdmin();
        $dep = $this->criarDepartamento();

        $resp = $this->actingAs($admin)->post('/admin/usuarios', [
            'papel' => 'professor',
            'nome' => 'Novo Prof',
            'email' => 'novoprof'.uniqid().'@univali.br',
            'departamento_id' => $dep->id,
        ]);

        $resp->assertSessionHasNoErrors();
        $resp->assertRedirect();
    }

    public function test_erro1_fluxo_completo_de_redefinicao_de_senha(): void
    {
        $user = User::create([
            'nome' => 'Fulano', 'email' => 'fulano'.uniqid().'@elo.test',
            'password' => Hash::make('senhaAntiga'), 'papel' => 'estudante',
        ]);

        $token = \Illuminate\Support\Facades\Password::createToken($user);

        $resp = $this->get("/redefinir-senha/{$token}?email={$user->email}");
        $resp->assertOk();

        $resp2 = $this->post('/redefinir-senha', [
            'token' => $token, 'email' => $user->email,
            'password' => 'novaSenha123', 'password_confirmation' => 'novaSenha123',
        ]);
        $resp2->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('novaSenha123', $user->fresh()->password));
    }

    public function test_erro2_admin_ve_usuarios_e_demandas(): void
    {
        $admin = $this->criarAdmin();
        $this->criarDemanda();

        $this->actingAs($admin)->get('/admin/usuarios')->assertOk();
        $this->actingAs($admin)->get('/admin/demandas')->assertOk();
    }

    public function test_erro3_e_5_professor_ve_candidaturas_centralizadas_e_aprova(): void
    {
        $dep = $this->criarDepartamento();
        $prof = $this->criarProfessor($dep);
        $demanda = $this->criarDemanda(['departamento_id' => $dep->id, 'professor_id' => $prof->id, 'nivel_complexidade' => 1]);
        $grupo = $this->criarGrupo();
        $candidatura = $this->criarCandidatura($demanda, $grupo);

        $resp = $this->actingAs($prof)->get('/professor/candidaturas');
        $resp->assertOk();
        $resp->assertSee('Grupo Teste');

        $resp2 = $this->actingAs($prof)->post("/professor/candidaturas/{$candidatura->id}/aprovar");
        $resp2->assertSessionHasNoErrors();
        $this->assertSame('aprovada', $candidatura->fresh()->status);
        $this->assertSame('em_execucao', $demanda->fresh()->status);
    }

    public function test_erro4_convite_precisa_ser_aceito_e_bloqueia_duplicados(): void
    {
        $criador = $this->criarEstudante('Criador');
        $grupo = $this->criarGrupo([$criador]);
        $convidado = $this->criarEstudante('Convidado');

        $resp = $this->actingAs($criador)->post('/estudante/grupo/convidar', ['identificador' => $convidado->email]);
        $resp->assertSessionHasNoErrors();
        $this->assertFalse($grupo->fresh()->membros->contains($convidado->id));

        $convite = ConviteGrupo::where('estudante_id', $convidado->id)->first();
        $this->assertNotNull($convite);
        $this->assertSame('pendente', $convite->status);

        $resp2 = $this->actingAs($criador)->post('/estudante/grupo/convidar', ['identificador' => $convidado->email]);
        $resp2->assertSessionHasErrors('identificador');

        $resp3 = $this->actingAs($convidado)->post("/estudante/convites/{$convite->id}/aceitar");
        $resp3->assertSessionHasNoErrors();
        $this->assertTrue($grupo->fresh()->membros->contains($convidado->id));

        $resp4 = $this->actingAs($criador)->post('/estudante/grupo/convidar', ['identificador' => $convidado->email]);
        $resp4->assertSessionHasErrors('identificador');
    }

    public function test_erro4_convidado_pode_recusar(): void
    {
        $criador = $this->criarEstudante('Criador');
        $grupo = $this->criarGrupo([$criador]);
        $convidado = $this->criarEstudante('Convidado');

        $this->actingAs($criador)->post('/estudante/grupo/convidar', ['identificador' => $convidado->email]);
        $convite = ConviteGrupo::where('estudante_id', $convidado->id)->first();

        $resp = $this->actingAs($convidado)->post("/estudante/convites/{$convite->id}/recusar");
        $resp->assertSessionHasNoErrors();
        $this->assertSame('recusado', $convite->fresh()->status);
        $this->assertFalse($grupo->fresh()->membros->contains($convidado->id));
    }

    public function test_erro4_estudante_ve_candidatura_pendente_em_minhas_horas(): void
    {
        $estudante = $this->criarEstudante();
        $grupo = $this->criarGrupo([$estudante]);
        $demanda = $this->criarDemanda();
        $this->criarCandidatura($demanda, $grupo, 'pendente');

        $resp = $this->actingAs($estudante)->get('/estudante/horas');
        $resp->assertOk();
        $resp->assertSee('Aguardando resposta do professor');
    }
}
