<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demandas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instituicao_id')->constrained('instituicoes')->cascadeOnDelete();
            // Atribuído na triagem (RF-02.2) — por isso nullable
            $table->foreignId('departamento_id')->nullable()->constrained('departamentos')->nullOnDelete();
            $table->foreignId('professor_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('titulo');
            $table->text('descricao');
            $table->string('area_sugerida');

            // Nível estimado pela instituição x nível final definido na triagem
            $table->unsignedTinyInteger('nivel_estimado');
            $table->unsignedTinyInteger('nivel_complexidade')->nullable();

            $table->unsignedSmallInteger('horas_min')->nullable();
            $table->unsignedSmallInteger('horas_max')->nullable();

            $table->enum('status', [
                'pendente_triagem',
                'em_revisao_adicional', // RF-06.2: checklist ético sinalizou
                'aberta_candidatura',
                'em_execucao',
                'concluida',
                'abandonada',
                'reaberta',
                'recusada',
            ])->default('pendente_triagem');

            $table->unsignedTinyInteger('prazo_candidatura_dias')->nullable();
            $table->timestamp('data_abertura_candidatura')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demandas');
    }
};
