<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;   // arriba RAMA  PRUEBA


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {   
        Paginator::useBootstrapFive();//AÑADIDO POR LA RAMA PRUEBA PARA QUE EL PAGINADOR USE BOOTSTRAP 5
    }
}
