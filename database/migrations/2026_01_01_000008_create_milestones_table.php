<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demanda_id')->constrained('demandas')->cascadeOnDelete();
            $table->unsignedTinyInteger('ordem'); // 1, 2, 3...
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->unsignedSmallInteger('horas_creditadas');
            $table->date('prazo');
            $table->enum('status', ['nao_iniciado', 'em_andamento', 'em_risco', 'aguardando_validacao', 'concluido', 'abandonado'])
                ->default('nao_iniciado');
            $table->string('entrega_path')->nullable(); // relatório/entrega anexada pelo grupo
            $table->timestamp('data_entrega')->nullable();
            $table->timestamp('data_conclusao')->nullable();
            $table->timestamps();

            $table->unique(['demanda_id', 'ordem']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milestones');
    }
};
