<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->foreignId('criado_por')->constrained('users');
            $table->timestamps();
        });

        // Pivot estudante <-> grupo (N:N)
        Schema::create('grupo_membros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_id')->constrained('grupos')->cascadeOnDelete();
            $table->foreignId('estudante_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['grupo_id', 'estudante_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupo_membros');
        Schema::dropIfExists('grupos');
    }
};
