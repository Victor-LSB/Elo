<?php

namespace App\Providers;

use App\View\Composers\SidebarComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Todas as telas autenticadas estendem este layout, então os
        // contadores da barra lateral e as notificações chegam sozinhos.
        // Registrado em '*' (e não só em 'layouts.dashboard') porque as telas
        // usam @extends('layouts.dashboard'): o composer roda quando a view
        // FILHA é montada, e os dados que ele injeta ali não "sobem" para a
        // view de topo. Isso também é o que faz assertViewHas() enxergar os
        // contadores nos testes.
        View::composer('*', SidebarComposer::class);
    }
}
