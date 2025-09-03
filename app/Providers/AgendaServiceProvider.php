<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Grupo\AgendaRepository;
use App\Repositories\Grupo\AgendaEloquentRepository;

class AgendaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AgendaRepository::class, AgendaEloquentRepository::class);
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
