<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\GenerateService;

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
        $this->app->singleton(GenerateService::class, function($app){
            return new GenerateService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // forzar https en producción
        if(config('app.env') === 'production') {
            \URL::forceScheme('https');
        }
    }
}
