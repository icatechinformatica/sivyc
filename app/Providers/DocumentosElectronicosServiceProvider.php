<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\ElectronicDocument\ElectronicDocumentRepository;
use App\Interfaces\ElectronicDocument\ElectronicDocumentRepositoryInterface;
use Illuminate\Support\Facades\Log;

class DocumentosElectronicosServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind(ElectronicDocumentRepositoryInterface::class, ElectronicDocumentRepository::class);
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
