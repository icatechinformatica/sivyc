<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // forzar https en producción
        if ($this->app->environment('production')) {
            # forzamos el esquema a trabajar con https
            \URL::forceScheme('https');
        }
    }
}
