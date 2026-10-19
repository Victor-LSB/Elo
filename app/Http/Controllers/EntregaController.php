<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Serve o arquivo de entrega de um milestone só para quem tem relação direta
 * com a demanda: o professor responsável, a instituição dona da demanda ou
 * um membro do grupo que fez o upload. O arquivo fica no disco "local"
 * (privado) de propósito — não é servido via URL pública.
 */
class EntregaController extends Controller
{
    public function show(Milestone $milestone, Request $request)
    {
        abort_unless($milestone->entrega_path, 404);

        $user = $request->user();
        $demanda = $milestone->demanda;

        $autorizado = match ($user->papel) {
            'professor' => $demanda->professor_id === $user->id,
            'instituicao' => $demanda->instituicao_id === $user->instituicao_id,
            'estudante' => $demanda->grupoAtivo()?->membros->contains($user->id) ?? false,
            'admin', 'coordenacao' => true,
            default => false,
        };

        abort_unless($autorizado, 403);
        abort_unless(Storage::exists($milestone->entrega_path), 404);

        return Storage::response($milestone->entrega_path);
    }
}
