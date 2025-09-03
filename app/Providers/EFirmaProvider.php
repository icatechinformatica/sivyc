<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class EFirmaProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('cadena', function ($app) {
            return new EFirmaService();
        });
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
