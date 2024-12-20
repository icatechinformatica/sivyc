<?php

namespace App\Providers;

use App\Interfaces\DocumentacionPagoInstructorInterface;
use App\Repositories\DocumentacionPagoInstructorRepository;
use Illuminate\Support\ServiceProvider;

class DocumentacionPagoInstructorProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(DocumentacionPagoInstructorInterface::class, DocumentacionPagoInstructorRepository::class);
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
