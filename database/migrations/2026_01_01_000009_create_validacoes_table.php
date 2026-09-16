<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('milestone_id')->constrained('milestones')->cascadeOnDelete();

            // Nível 1/2: um único validador (presença ou relatório).
            // Nível 3: exige DUAS linhas por milestone — uma do professor,
            // uma da instituição (RF-05.3) — cada uma marcando sua aprovação.
            $table->foreignId('professor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('professor_aprovou')->default(false);
            $table->timestamp('professor_data')->nullable();

            $table->boolean('instituicao_aprovou')->default(false);
            $table->timestamp('instituicao_data')->nullable();

            $table->text('comentario')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validacoes');
    }
};
