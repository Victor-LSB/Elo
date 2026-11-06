<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificados', function (Blueprint $table) {
            $table->string('codigo', 20)->nullable()->unique()->after('demanda_id');
        });

        // Certificados já emitidos antes desta migration também recebem um código.
        DB::table('certificados')->whereNull('codigo')->pluck('id')->each(function ($id) {
            DB::table('certificados')->where('id', $id)->update([
                'codigo' => 'CEX-'.Str::upper(Str::random(4)).'-'.Str::upper(Str::random(4)),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('certificados', function (Blueprint $table) {
            $table->dropUnique(['codigo']);
            $table->dropColumn('codigo');
        });
    }
};
