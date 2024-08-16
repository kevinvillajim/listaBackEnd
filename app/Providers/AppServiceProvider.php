<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Miembro;
use App\Observers\MiembroObserver;
use App\Models\Asistencia;
use App\Observers\AsistenciaObserver;

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
        Miembro::observe(MiembroObserver::class);
        Asistencia::observe(AsistenciaObserver::class);
    }
}
