<?php

namespace App\Providers;

use App\Interfaces\AlumnosInterface;
use App\Repositories\AlumnosRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\AlumnoSeccionesRepository;
use App\Repositories\AlumnoSeccionesRepositoryInterface;

class AlumnoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AlumnosInterface::class, AlumnosRepository::class);
        $this->app->bind(AlumnoSeccionesRepositoryInterface::class, AlumnoSeccionesRepository::class);
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
