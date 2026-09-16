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
        View::composer('layouts.dashboard', SidebarComposer::class);
    }
}
