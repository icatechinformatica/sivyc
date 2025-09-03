<?php

namespace App\Providers;

use App\Interfaces\MunicipioRepositoryInterface;
use App\Repositories\MunicipioRepository;
use App\Services\Municipio\MunicipioService;
use Illuminate\Support\ServiceProvider;

class MunicipioServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(MunicipioRepositoryInterface::class, MunicipioRepository::class);
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
