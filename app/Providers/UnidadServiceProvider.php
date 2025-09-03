<?php

namespace App\Providers;

use App\Repositories\UnidadRepository;
use Illuminate\Support\ServiceProvider;
use App\Interfaces\UnidadRepositoryInterface;

class UnidadServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UnidadRepositoryInterface::class, UnidadRepository::class);
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
