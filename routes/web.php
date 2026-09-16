<?php

use App\Http\Controllers\Admin\ConfiguracaoController;
use App\Http\Controllers\Admin\ExcecaoController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Coordenacao\InstituicaoController as CoordInstituicaoController;
use App\Http\Controllers\Coordenacao\ProfessorController as CoordProfessorController;
use App\Http\Controllers\Coordenacao\TriagemController;
use App\Http\Controllers\Estudante\DemandaController as EstudanteDemandaController;
use App\Http\Controllers\Estudante\GrupoController;
use App\Http\Controllers\Estudante\MilestoneController as EstudanteMilestoneController;
use App\Http\Controllers\Instituicao\DemandaController as InstDemandaController;
use App\Http\Controllers\Instituicao\MilestoneController as InstMilestoneController;
use App\Http\Controllers\Instituicao\PerfilController as InstPerfilController;
use App\Http\Controllers\NotificacaoController;
use App\Http\Controllers\Professor\CandidaturaController;
use App\Http\Controllers\Professor\MilestoneController as ProfMilestoneController;
use Illuminate\Support\Facades\Route;

// ---------- Público ----------
Route::view('/', 'landing')->name('landing');
Route::view('/login', 'auth.login')->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

// ---------- Notificações (RF-07.1) — comuns a todos os papéis ----------
Route::middleware('auth')->group(function () {
    Route::get('/notificacoes/{id}', [NotificacaoController::class, 'abrir'])->name('notificacoes.abrir');
    Route::post('/notificacoes/lidas', [NotificacaoController::class, 'marcarTodasLidas'])->name('notificacoes.lidas');
});

Route::view('/cadastro', 'auth.cadastro')->name('cadastro');
Route::view('/cadastro/estudante', 'auth.cadastro-estudante')->name('cadastro.estudante.form');
Route::post('/cadastro/estudante', [RegisterController::class, 'estudante'])->name('cadastro.estudante');
Route::view('/cadastro/instituicao', 'auth.cadastro-instituicao')->name('cadastro.instituicao.form');
Route::post('/cadastro/instituicao', [RegisterController::class, 'instituicao'])->name('cadastro.instituicao');
Route::view('/cadastro/pendente', 'auth.cadastro-pendente')->name('cadastro.pendente');

// ---------- Estudante (RF-01.3, RF-03) ----------
Route::middleware(['auth', 'role:estudante'])->prefix('estudante')->name('estudante.')->group(function () {
    Route::get('/demandas', [EstudanteDemandaController::class, 'index'])->name('demandas.index');
    Route::get('/demandas/{demanda}', [EstudanteDemandaController::class, 'show'])->name('demandas.show');
    Route::post('/demandas/{demanda}/candidatar', [EstudanteDemandaController::class, 'candidatar'])->name('demandas.candidatar');

    Route::get('/grupo', [GrupoController::class, 'show'])->name('grupo.show');
    Route::post('/grupo', [GrupoController::class, 'store'])->name('grupo.store');
    Route::post('/grupo/convidar', [GrupoController::class, 'convidar'])->name('grupo.convidar');
    Route::delete('/grupo/saida', [GrupoController::class, 'saidaDoGrupo'])->name('grupo.sair');

    Route::get('/horas', [EstudanteMilestoneController::class, 'index'])->name('horas.index');
    Route::post('/milestones/{milestone}/entrega', [EstudanteMilestoneController::class, 'registrarEntrega'])->name('milestones.entrega');
});

// ---------- Instituição (RF-02, RF-05.3) ----------
Route::middleware(['auth', 'role:instituicao'])->prefix('instituicao')->name('instituicao.')->group(function () {
    Route::get('/demandas', [InstDemandaController::class, 'index'])->name('demandas.index');
    Route::get('/demandas/nova', [InstDemandaController::class, 'create'])->name('demandas.create');
    Route::post('/demandas', [InstDemandaController::class, 'store'])->name('demandas.store');
    Route::get('/demandas/{demanda}', [InstDemandaController::class, 'show'])->name('demandas.show');

    Route::post('/milestones/{milestone}/validar', [InstMilestoneController::class, 'validar'])->name('milestones.validar');

    Route::get('/perfil', [InstPerfilController::class, 'show'])->name('perfil.show');
    Route::put('/perfil', [InstPerfilController::class, 'update'])->name('perfil.update');
});

// ---------- Professor (RF-03.3, RF-04, RF-05.3) ----------
Route::middleware(['auth', 'role:professor'])->prefix('professor')->name('professor.')->group(function () {
    Route::get('/demandas', [CandidaturaController::class, 'index'])->name('demandas.index');
    Route::get('/demandas/{demanda}/candidaturas', [CandidaturaController::class, 'show'])->name('candidaturas.show');
    Route::post('/candidaturas/{candidatura}/aprovar', [CandidaturaController::class, 'aprovar'])->name('candidaturas.aprovar');
    Route::post('/candidaturas/{candidatura}/rejeitar', [CandidaturaController::class, 'rejeitar'])->name('candidaturas.rejeitar');

    Route::get('/milestones', [ProfMilestoneController::class, 'index'])->name('milestones.index');
    Route::post('/milestones/{milestone}/validar', [ProfMilestoneController::class, 'validar'])->name('milestones.validar');
    Route::post('/milestones/{milestone}/ajustes', [ProfMilestoneController::class, 'pedirAjustes'])->name('milestones.ajustes');
});

// ---------- Coordenação (RF-01.1, RF-02.2/02.3, RF-06) ----------
Route::middleware(['auth', 'role:coordenacao'])->prefix('coordenacao')->name('coordenacao.')->group(function () {
    Route::get('/triagem', [TriagemController::class, 'index'])->name('triagem.index');
    Route::get('/triagem/{demanda}', [TriagemController::class, 'show'])->name('triagem.show');
    Route::post('/triagem/{demanda}', [TriagemController::class, 'store'])->name('triagem.store');
    // Alias usado no menu para manter a nomenclatura das telas (Fila de triagem = "demandas")
    Route::redirect('/demandas', '/coordenacao/triagem')->name('demandas.index');

    Route::get('/instituicoes', [CoordInstituicaoController::class, 'index'])->name('instituicoes.index');
    Route::post('/instituicoes/{instituicao}/validar', [CoordInstituicaoController::class, 'validar'])->name('instituicoes.validar');
    Route::post('/instituicoes/{instituicao}/recusar', [CoordInstituicaoController::class, 'recusar'])->name('instituicoes.recusar');

    Route::get('/professores', [CoordProfessorController::class, 'index'])->name('professores.index');
});

// ---------- Admin (RF-01.2, RNF-04, RF-04.4) ----------
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/excecoes', [ExcecaoController::class, 'index'])->name('excecoes.index');
    Route::get('/excecoes/{milestone}', [ExcecaoController::class, 'show'])->name('excecoes.show');
    Route::post('/excecoes/{milestone}/resolver', [ExcecaoController::class, 'resolver'])->name('excecoes.resolver');

    Route::get('/configuracoes', [ConfiguracaoController::class, 'index'])->name('configuracoes.index');
    Route::put('/configuracoes', [ConfiguracaoController::class, 'atualizar'])->name('configuracoes.atualizar');
    Route::post('/usuarios', [ConfiguracaoController::class, 'criarUsuario'])->name('usuarios.store');
});
