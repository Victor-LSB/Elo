<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('convites_grupo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_id')->constrained('grupos')->cascadeOnDelete();
            $table->foreignId('estudante_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('convidado_por')->constrained('users');
            $table->enum('status', ['pendente', 'aceito', 'recusado', 'cancelado'])->default('pendente');
            $table->timestamp('respondido_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('convites_grupo');
    }
};
