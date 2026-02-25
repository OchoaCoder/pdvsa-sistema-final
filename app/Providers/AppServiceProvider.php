<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registramos los Aliases para PDF y Excel (Motores de reportes)
        $loader = AliasLoader::getInstance();
        $loader->alias('PDF', \Barryvdh\DomPDF\Facade::class);
        $loader->alias('Excel', \Maatwebsite\Excel\Facades\Excel::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Aquí podrías forzar el uso de Bootstrap 5 para la paginación si lo necesitas
        \Illuminate\Pagination\Paginator::useBootstrapFive();
    }
}