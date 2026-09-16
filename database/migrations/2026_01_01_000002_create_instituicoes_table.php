<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instituicoes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->enum('tipo', ['ong', 'escola_publica', 'associacao_bairro', 'orgao_publico', 'outro']);
            $table->string('responsavel');
            $table->string('contato_email');
            $table->string('contato_telefone')->nullable();
            $table->text('sobre')->nullable();
            // RF-01.1: cadastro fica pendente até a coordenação validar
            $table->enum('status', ['pendente', 'ativa', 'recusada'])->default('pendente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instituicoes');
    }
};
