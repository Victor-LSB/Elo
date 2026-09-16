<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('email')->unique();
            $table->string('password');

            // RF-01.2: papel decidido no cadastro, distinto por permissões.
            // 'instituicao', 'professor', 'coordenacao' e 'admin' não são
            // necessariamente autocadastrados (ver RF-01.1 e nota de negócio:
            // apenas estudante e instituicao se autocadastram).
            $table->enum('papel', ['estudante', 'instituicao', 'professor', 'coordenacao', 'admin']);

            // Específico de estudante
            $table->string('curso')->nullable();
            $table->string('matricula')->nullable()->unique();

            // Específico de professor
            $table->foreignId('departamento_id')->nullable()->constrained('departamentos')->nullOnDelete();

            // Específico de conta de instituição: aponta para o perfil da organização
            $table->foreignId('instituicao_id')->nullable()->constrained('instituicoes')->nullOnDelete();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
