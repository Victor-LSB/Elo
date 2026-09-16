<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checklist_eticos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demanda_id')->unique()->constrained('demandas')->cascadeOnDelete();
            $table->boolean('substitui_servico_profissional');
            $table->text('justificativa')->nullable(); // obrigatória quando true (RF-06.2)
            $table->foreignId('respondido_por')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_eticos');
    }
};
