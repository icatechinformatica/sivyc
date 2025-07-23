<?php

namespace App\Providers;

use App\Interfaces\AlumnosInterface;
use App\Repositories\AlumnosRepository;
use Illuminate\Support\ServiceProvider;

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
