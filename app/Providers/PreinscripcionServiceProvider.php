<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Repository Interfaces
use App\Interfaces\Repositories\GrupoRepositoryInterface;
use App\Interfaces\Repositories\CursoRepositoryInterface;
use App\Interfaces\Repositories\CatalogoRepositoryInterface;
use App\Interfaces\Repositories\InstructorRepositoryInterface;

// Repository Implementations
use App\Repositories\GrupoRepository;
use App\Repositories\CursoRepository;
use App\Repositories\CatalogoRepository;
use App\Repositories\InstructorRepository;

class PreinscripcionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind Repository Interfaces to Implementations for Preinscripcion Module
        $this->app->bind(GrupoRepositoryInterface::class, GrupoRepository::class);
        $this->app->bind(CursoRepositoryInterface::class, CursoRepository::class);
        $this->app->bind(CatalogoRepositoryInterface::class, CatalogoRepository::class);
        $this->app->bind(InstructorRepositoryInterface::class, InstructorRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
