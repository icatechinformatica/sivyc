<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\Transaction\TransactionRepositoryInterface;
use App\Repositories\Transaction\TransactionRepository;

class TransactionsProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
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
