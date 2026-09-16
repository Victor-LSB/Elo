<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditorias', function (Blueprint $table) {
            $table->id();
            // Entidade auditada de forma polimórfica: demanda ou milestone
            $table->string('auditavel_type'); // Demanda::class ou Milestone::class
            $table->unsignedBigInteger('auditavel_id');
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status_anterior')->nullable();
            $table->string('status_novo');
            $table->text('observacao')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['auditavel_type', 'auditavel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditorias');
    }
};
