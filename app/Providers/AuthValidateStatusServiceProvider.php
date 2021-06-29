<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\EloquentUserProvider as UserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
/**
 * imports
 */
use App\Traits\LogTrait;

class AuthValidateStatusServiceProvider extends UserProvider
{
    use LogTrait;
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
     * Overrides the framework defaults validate credentials method 
     *
     * @param UserContract $user
     * @param array $credentials
     * @return bool
     */
    public function validateCredentials(UserContract $user, array $credentials)
    {
        $plain = $credentials['password'];
        /**
         * enviamos la información a una tabla para guardar los registros de quién se ha registrado (inicio de sesion)
         * en el sistema
         */
        $fechaActual = Carbon::now()->format('Y-m-d');
        $usuario = $user->name;
        $currentEmail = $user->email;
        $currentUser = $usuario." - ".$currentEmail;
        $currentTime = Carbon::now()->format('H:i:s');
        $accion = 'Inicio de Sesión al sistema Sivyc';
        $modulo = 'Inicio';
        $this->settingLog($accion, $currentUser, $fechaActual, $currentTime, $modulo);

        return $this->hasher->check($plain, $user->getAuthPassword());
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
