<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Sede;


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
        View::composer('plantilla_web.navegacion', function ($view) {
            $sedes = Sede::select('id', 'nombre')->orderBy('nombre')->get();
            $view->with('sedes', $sedes);
        });
    }
}
