<?php

namespace App\Providers;

use App\Interfaces\Reporterf001Interface;
use App\Repositories\Reporterf001Repository;
use Illuminate\Support\ServiceProvider;

class ReporteRf001Provider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind(Reporterf001Interface::class, Reporterf001Repository::class);
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
