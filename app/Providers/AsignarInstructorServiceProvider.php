<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Grupo\AsignarInstructorRepository;
use App\Interfaces\Repositories\InstructorRepositoryInterface;

class AsignarInstructorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(InstructorRepositoryInterface::class, AsignarInstructorRepository::class);
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
