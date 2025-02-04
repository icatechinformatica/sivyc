<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\CredencialesInterface;
use App\Repositories\CredencialRepository;

class CredencialProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind(CredencialesInterface::class, CredencialRepository::class);
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
