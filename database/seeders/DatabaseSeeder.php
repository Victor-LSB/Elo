<?php

namespace Database\Seeders;

use App\Models\Candidatura;
use App\Models\ChecklistEtico;
use App\Models\Demanda;
use App\Models\Departamento;
use App\Models\Grupo;
use App\Models\Instituicao;
use App\Models\Milestone;
use App\Models\User;
use App\Models\Validacao;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- Departamentos ----------
        $computacao = Departamento::create(['nome' => 'Computação', 'area' => 'Tecnologia']);
        $design = Departamento::create(['nome' => 'Design', 'area' => 'Design']);
        Departamento::create(['nome' => 'Administração', 'area' => 'Administração']);
        Departamento::create(['nome' => 'Pedagogia', 'area' => 'Educação']);

        // ---------- Usuários base ----------
        $admin = User::create([
            'nome' => 'Admin', 'email' => 'admin@elo.test',
            'password' => Hash::make('password'), 'papel' => 'admin',
        ]);

        $coordenacao = User::create([
            'nome' => 'Coordenação de Extensão', 'email' => 'coordenacao@elo.test',
            'password' => Hash::make('password'), 'papel' => 'coordenacao',
        ]);

        $marcos = User::create([
            'nome' => 'Prof. Marcos Vinícius', 'email' => 'marcos@elo.test',
            'password' => Hash::make('password'), 'papel' => 'professor', 'departamento_id' => $computacao->id,
        ]);
        User::create([
            'nome' => 'Profa. Ana Tereza Lopes', 'email' => 'ana.tereza@elo.test',
            'password' => Hash::make('password'), 'papel' => 'professor', 'departamento_id' => $design->id,
        ]);

        // ---------- Instituição ativa: ONG Girassol ----------
        $girassol = Instituicao::create([
            'nome' => 'ONG Girassol', 'tipo' => 'ong', 'responsavel' => 'Marina Duarte',
            'contato_email' => 'contato@onggirassol.test', 'contato_telefone' => '(47) 99999-0001',
            'sobre' => 'Atende famílias em situação de vulnerabilidade no bairro Fazenda.',
            'status' => 'ativa',
        ]);
        $girassolUser = User::create([
            'nome' => 'Marina Duarte', 'email' => 'girassol@elo.test',
            'password' => Hash::make('password'), 'papel' => 'instituicao', 'instituicao_id' => $girassol->id,
        ]);

        // Instituição pendente de validação (RF-01.1)
        $bancoAlimentos = Instituicao::create([
            'nome' => 'Banco de Alimentos Solidário', 'tipo' => 'ong', 'responsavel' => 'Fernanda Rocha',
            'contato_email' => 'contato@bancosolidario.test', 'contato_telefone' => '(47) 98888-2211',
            'sobre' => 'Coleta e redistribui alimentos não perecíveis para famílias da região do Fazenda.',
            'status' => 'pendente',
        ]);
        User::create([
            'nome' => 'Fernanda Rocha', 'email' => 'bancosolidario@elo.test',
            'password' => Hash::make('password'), 'papel' => 'instituicao', 'instituicao_id' => $bancoAlimentos->id,
        ]);

        // ---------- Estudantes e grupo "Vetor4" ----------
        $larissa = User::create([
            'nome' => 'Larissa Costa', 'email' => 'larissa@elo.test', 'password' => Hash::make('password'),
            'papel' => 'estudante', 'curso' => 'Sistemas para Internet', 'matricula' => '2023001',
        ]);
        $bruno = User::create([
            'nome' => 'Bruno Ferreira', 'email' => 'bruno@elo.test', 'password' => Hash::make('password'),
            'papel' => 'estudante', 'curso' => 'Ciência da Computação', 'matricula' => '2023002',
        ]);
        $julia = User::create([
            'nome' => 'Julia Andrade', 'email' => 'julia@elo.test', 'password' => Hash::make('password'),
            'papel' => 'estudante', 'curso' => 'Design', 'matricula' => '2023003',
        ]);
        $rafael = User::create([
            'nome' => 'Rafael Pinto', 'email' => 'rafael@elo.test', 'password' => Hash::make('password'),
            'papel' => 'estudante', 'curso' => 'Sistemas para Internet', 'matricula' => '2023004',
        ]);

        $vetor4 = Grupo::create(['nome' => 'Vetor4', 'criado_por' => $larissa->id]);
        $vetor4->membros()->attach([$larissa->id, $bruno->id, $julia->id, $rafael->id]);

        // ---------- Demanda Nível 3 em execução: Sistema de cadastro de beneficiários ----------
        $demandaCadastro = Demanda::create([
            'instituicao_id' => $girassol->id,
            'departamento_id' => $computacao->id,
            'professor_id' => $marcos->id,
            'titulo' => 'Sistema de cadastro de beneficiários',
            'descricao' => 'Sistema para cadastro e consulta de beneficiários, com relatório mensal de atendimentos.',
            'area_sugerida' => 'Tecnologia',
            'nivel_estimado' => 3,
            'nivel_complexidade' => 3,
            'horas_min' => 40,
            'horas_max' => 80,
            'status' => 'em_execucao',
            'prazo_candidatura_dias' => 5,
            'data_abertura_candidatura' => now()->subDays(20),
        ]);

        ChecklistEtico::create([
            'demanda_id' => $demandaCadastro->id,
            'substitui_servico_profissional' => false,
            'respondido_por' => $coordenacao->id,
        ]);

        $candidaturaAprovada = Candidatura::create([
            'demanda_id' => $demandaCadastro->id, 'grupo_id' => $vetor4->id,
            'mensagem' => 'Já desenvolvemos um sistema parecido na disciplina de Projeto Integrador.',
            'status' => 'aprovada', 'data_resposta' => now()->subDays(18),
        ]);

        $m1 = Milestone::create([
            'demanda_id' => $demandaCadastro->id, 'ordem' => 1,
            'titulo' => 'Levantamento de requisitos e prototipação',
            'horas_creditadas' => 12, 'prazo' => now()->subDays(10),
            'status' => 'concluido', 'data_conclusao' => now()->subDays(10),
        ]);
        Validacao::create([
            'milestone_id' => $m1->id, 'professor_id' => $marcos->id,
            'professor_aprovou' => true, 'professor_data' => now()->subDays(10),
            'instituicao_aprovou' => true, 'instituicao_data' => now()->subDays(10),
        ]);

        $m2 = Milestone::create([
            'demanda_id' => $demandaCadastro->id, 'ordem' => 2,
            'titulo' => 'Cadastro e busca de beneficiários (CRUD)',
            'horas_creditadas' => 18, 'prazo' => now()->subDays(2),
            'status' => 'aguardando_validacao', 'entrega_path' => 'entregas/milestones/demo.pdf',
            'data_entrega' => now()->subDay(),
        ]);
        Validacao::create([
            'milestone_id' => $m2->id, 'professor_id' => $marcos->id,
            'professor_aprovou' => true, 'professor_data' => now(),
            'instituicao_aprovou' => false,
        ]);

        Milestone::create([
            'demanda_id' => $demandaCadastro->id, 'ordem' => 3,
            'titulo' => 'Relatório mensal e exportação',
            'horas_creditadas' => 10, 'prazo' => now()->addDays(15),
            'status' => 'nao_iniciado',
        ]);

        // ---------- Demanda Nível 2 aberta para candidatura: Site institucional ----------
        Demanda::create([
            'instituicao_id' => $girassol->id,
            'departamento_id' => $design->id,
            'professor_id' => null,
            'titulo' => 'Site institucional para divulgação de eventos',
            'descricao' => 'Site simples para divulgar eventos, com formulário de inscrição de voluntários.',
            'area_sugerida' => 'Tecnologia',
            'nivel_estimado' => 2,
            'nivel_complexidade' => 2,
            'horas_min' => 15,
            'horas_max' => 30,
            'status' => 'aberta_candidatura',
            'prazo_candidatura_dias' => 5,
            'data_abertura_candidatura' => now()->subDays(3),
        ]);

        // ---------- Demanda pendente de triagem: Planilha de controle de doações ----------
        Demanda::create([
            'instituicao_id' => $girassol->id,
            'titulo' => 'Planilha de controle de doações',
            'descricao' => 'Registro de doações recebidas e resumo mensal para prestação de contas.',
            'area_sugerida' => 'Tecnologia',
            'nivel_estimado' => 1,
            'status' => 'pendente_triagem',
        ]);

        // ---------- Demanda pendente de triagem: instituição recém-cadastrada ----------
        Demanda::create([
            'instituicao_id' => $bancoAlimentos->id,
            'titulo' => 'Reorganização do fluxo de doação de alimentos',
            'descricao' => 'Sistema simples para controlar entrada e saída de alimentos do estoque.',
            'area_sugerida' => 'Tecnologia',
            'nivel_estimado' => 2,
            'status' => 'pendente_triagem',
        ]);

        $this->command?->info('Seed concluído. Login de teste: qualquer e-mail acima, senha "password".');
    }
}
