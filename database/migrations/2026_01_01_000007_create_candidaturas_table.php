<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidaturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demanda_id')->constrained('demandas')->cascadeOnDelete();
            $table->foreignId('grupo_id')->constrained('grupos')->cascadeOnDelete();
            $table->text('mensagem');
            $table->enum('status', ['pendente', 'aprovada', 'rejeitada', 'rejeitada_automatica'])->default('pendente');
            $table->timestamp('data_resposta')->nullable();
            $table->timestamps();

            // Um grupo não envia duas candidaturas para a mesma demanda.
            // A regra "um grupo só pode ter UMA candidatura/demanda ativa por
            // vez, entre demandas diferentes" é validada na aplicação
            // (ver App\Models\Grupo::temEngajamentoAtivo()), pois depende do
            // status de várias linhas e não pode ser expressa como unique().
            $table->unique(['demanda_id', 'grupo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidaturas');
    }
};
