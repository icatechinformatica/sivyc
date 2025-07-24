<?php

namespace App\Providers\Catalogos;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\CatalogoInterface;
use App\Repositories\EloquentEspecialidadRepository;

class EspecialidadProvider extends ServiceProvider
{
    public function register(): void
    {
        // Este binding se usará solo para productos
        $this->app->bind(CatalogoInterface::class, EloquentEspecialidadRepository::class);
    }

    public function boot(): void
    {
        //
    }
}