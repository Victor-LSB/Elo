<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudante_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('demanda_id')->constrained('demandas')->cascadeOnDelete();
            $table->unsignedSmallInteger('horas_totais');
            $table->string('pdf_path')->nullable();
            $table->timestamp('data_emissao');
            $table->timestamps();

            $table->unique(['estudante_id', 'demanda_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificados');
    }
};
