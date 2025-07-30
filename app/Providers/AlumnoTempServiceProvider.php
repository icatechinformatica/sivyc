<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AlumnoTempServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Interfaces\AlumnosTempInterface', 'App\Repositories\AlumnosTempRepository');
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
