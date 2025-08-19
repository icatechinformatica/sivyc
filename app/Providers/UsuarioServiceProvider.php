<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Usuario\ListadoUsuariosService;
use App\Services\Funcionario\GetWithOutUserService as FuncionarioGetWithOutUserService;
use App\Services\Instructor\GetWithOutUserService as InstructorGetWithOutUserService;

class UsuarioServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ListadoUsuariosService::class, function ($app) {
            return new ListadoUsuariosService(
                $app->make(FuncionarioGetWithOutUserService::class),
                $app->make(InstructorGetWithOutUserService::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
