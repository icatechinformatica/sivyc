<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class MorphMapServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            'funcionario' => \App\Models\funcionario::class,
            'instructor'  => \App\Models\instructor::class,
        ]);
        \Log::info('✅ morphMap registrado correctamente');
    }
}
