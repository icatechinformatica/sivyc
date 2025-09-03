<?php

namespace App\Providers;

use App\Interfaces\InstructoresRepositoryInterface;
use App\Repositories\DBInstructorRepository;
use Illuminate\Support\ServiceProvider;

class InstructorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(InstructoresRepositoryInterface::class, DBInstructorRepository::class);
    }


    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
