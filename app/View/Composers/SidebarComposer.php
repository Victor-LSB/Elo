<?php

namespace App\View\Composers;

use App\Models\Candidatura;
use App\Models\Demanda;
use App\Models\Instituicao;
use App\Models\Milestone;
use Illuminate\View\View;

/**
 * Alimenta o layout de dashboard com os contadores da barra lateral e as
 * notificações não lidas.
 *
 * Fica num composer, e não em cada controller, porque toda tela do papel
 * mostra a mesma barra lateral — se dependesse do controller, qualquer tela
 * nova nasceria com os badges zerados sem ninguém perceber.
 */
class SidebarComposer
{
    public function compose(View $view): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        $view->with('notificacoes', $user->unreadNotifications()->latest()->take(8)->get());
        $view->with('totalNaoLidas', $user->unreadNotifications()->count());

        match ($user->papel) {
            'professor' => $this->professor($view, $user),
            'coordenacao' => $this->coordenacao($view),
            'admin' => $this->admin($view),
            default => null,
        };
    }

    private function professor(View $view, $user): void
    {
        $view->with('candidaturasPendentes', Candidatura::where('status', 'pendente')
            ->whereHas('demanda', fn ($q) => $q->where('professor_id', $user->id))
            ->count());
    }

    private function coordenacao(View $view): void
    {
        $view->with('filaPendente', Demanda::whereIn('status', ['pendente_triagem', 'em_revisao_adicional'])->count());
        $view->with('instituicoesPendentes', Instituicao::where('status', 'pendente')->count());
    }

    private function admin(View $view): void
    {
        $view->with('excecoesPendentes', Milestone::where('status', 'em_risco')->count());
    }
}
