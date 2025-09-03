<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\DBFuncionariosRepository;
use App\Interfaces\FuncionariosRepositoryInterface;

class FuncionariosServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
         $this->app->bind(FuncionariosRepositoryInterface::class, DBFuncionariosRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
