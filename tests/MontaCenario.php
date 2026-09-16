<?php

namespace Tests;

use App\Models\Candidatura;
use App\Models\Demanda;
use App\Models\Departamento;
use App\Models\Grupo;
use App\Models\Instituicao;
use App\Models\Milestone;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Monta os cenários usados pelos testes. Evita factories para que o
 * projeto rode sem arquivos extras em database/factories.
 */
trait MontaCenario
{
    protected function criarDepartamento(string $nome = 'Computação'): Departamento
    {
        return Departamento::create(['nome' => $nome, 'area' => 'Tecnologia']);
    }

    protected function criarProfessor(?Departamento $departamento = null): User
    {
        return User::create([
            'nome' => 'Prof. Teste',
            'email' => 'prof'.uniqid().'@elo.test',
            'password' => Hash::make('password'),
            'papel' => 'professor',
            'departamento_id' => ($departamento ?? $this->criarDepartamento())->id,
        ]);
    }

    protected function criarInstituicao(string $status = 'ativa'): Instituicao
    {
        $instituicao = Instituicao::create([
            'nome' => 'ONG Teste',
            'tipo' => 'ong',
            'responsavel' => 'Responsável Teste',
            'contato_email' => 'ong'.uniqid().'@elo.test',
            'status' => $status,
        ]);

        User::create([
            'nome' => 'Responsável Teste',
            'email' => $instituicao->contato_email,
            'password' => Hash::make('password'),
            'papel' => 'instituicao',
            'instituicao_id' => $instituicao->id,
        ]);

        return $instituicao->refresh();
    }

    protected function criarEstudante(string $nome = 'Estudante Teste'): User
    {
        return User::create([
            'nome' => $nome,
            'email' => 'est'.uniqid().'@elo.test',
            'password' => Hash::make('password'),
            'papel' => 'estudante',
            'curso' => 'Sistemas para Internet',
            'matricula' => (string) random_int(100000, 999999),
        ]);
    }

    protected function criarGrupo(array $membros = []): Grupo
    {
        $membros = $membros ?: [$this->criarEstudante()];

        $grupo = Grupo::create(['nome' => 'Grupo Teste', 'criado_por' => $membros[0]->id]);
        $grupo->membros()->attach(collect($membros)->pluck('id'));

        return $grupo->refresh();
    }

    protected function criarDemanda(array $atributos = []): Demanda
    {
        return Demanda::create(array_merge([
            'instituicao_id' => $this->criarInstituicao()->id,
            'titulo' => 'Demanda Teste',
            'descricao' => 'Descrição da demanda de teste.',
            'area_sugerida' => 'Tecnologia',
            'nivel_estimado' => 2,
            'nivel_complexidade' => 2,
            'horas_min' => 15,
            'horas_max' => 30,
            'status' => 'aberta_candidatura',
            'prazo_candidatura_dias' => 5,
            'data_abertura_candidatura' => now(),
        ], $atributos));
    }

    protected function criarCandidatura(Demanda $demanda, Grupo $grupo, string $status = 'pendente'): Candidatura
    {
        return Candidatura::create([
            'demanda_id' => $demanda->id,
            'grupo_id' => $grupo->id,
            'mensagem' => 'Proposta de teste com mais de vinte caracteres.',
            'status' => $status,
        ]);
    }

    protected function criarMilestone(Demanda $demanda, array $atributos = []): Milestone
    {
        return Milestone::create(array_merge([
            'demanda_id' => $demanda->id,
            'ordem' => 1,
            'titulo' => 'Milestone Teste',
            'horas_creditadas' => 10,
            'prazo' => now()->addDays(7),
            'status' => 'aguardando_validacao',
        ], $atributos));
    }
}
