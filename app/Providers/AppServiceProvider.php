<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Helpers\MenuHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // forzar https en producción
        if (config('app.env') === 'production') {
            \URL::forceScheme('https');
        }

        // View Composer para menú dinámico
        View::composer('theme.sivyc.menuDinamico', function ($view) {
            $user = auth()->user();
            $menu = [];
            if ($user) {
                // Si tiene rol all-access, mostrar todos los permisos activos
                if ($user->roles()->where('especial', 'all-access')->exists()) {
                    $allPermissions = \App\Models\Permission::where('activo', true)
                        ->whereNotNull('clave_orden')
                        ->get()
                        ->sortBy('clave_orden');
                } else {
                    // Permisos directos
                    $directPermissions = $user->permissions()
                        ->where('activo', true)
                        ->whereNotNull('clave_orden')
                        ->get();

                    // Permisos por roles
                    $rolePermissions = \App\Models\Permission::whereHas('roles', function ($q) use ($user) {
                        $q->whereIn('tblz_roles.id', $user->roles->pluck('id'));
                    })
                        ->where('activo', true)
                        ->whereNotNull('clave_orden')
                        ->get();

                    // Unir y eliminar duplicados
                    $allPermissions = $directPermissions->merge($rolePermissions)->unique('id')->sortBy('clave_orden');
                }

                $menu = MenuHelper::buildMenu($allPermissions);
            }
            $view->with('menuDinamico', $menu);
        });
    }
}
