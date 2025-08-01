<?php

namespace App\Providers;

use App\Repositories\Alumno\ActualizarEstatusRepository;
use App\Interfaces\ActualizarEstatusRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class EstatusServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ActualizarEstatusRepositoryInterface::class, ActualizarEstatusRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
