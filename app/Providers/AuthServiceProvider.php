<?php

namespace App\Providers;

use App\User;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Estas rutas permiten a Passport hacer issue y revoke de access tokens y clients.
        Passport::routes();
        //Passport::tokensExpireIn(Carbon::now()->addDays(10));
        //Passport::refreshTokensExpireIn(Carbon::now()->addDays(10));
        Passport::tokensCan([
            'api' => 'User Type',
            'api_sice' => 'sice User type',
        ]);

        \Illuminate\Support\Facades\Auth::provider('customuserprovider', function($app, array $config) {
            return new AuthValidateStatusServiceProvider($app['hash'], $config['model']);
        });

        Gate::define('permission', function (User $user, $permission) {
            return $user->hasPermission($permission);
        });

        Gate::before(function ($user, $ability) {
            return $user->hasPermission($ability) ?: null;
        });
    }
}
