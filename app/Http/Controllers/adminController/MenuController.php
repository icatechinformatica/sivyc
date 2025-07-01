<?php

namespace App\Http\Controllers\adminController;

use App\Http\Controllers\Controller;
use Icatech\PermisoRolMenu\Models\Permiso;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Mostrar el árbol de menús
     */
    public function index()
    {
        return $this->treeIndex();
    }

    /**
     * Mostrar el árbol de menús (compatible con el paquete)
     */
    public function treeIndex()
    {
        $menus = Permiso::whereNotNull('clave_orden')
            ->select('id', 'nombre', 'ruta_corta', 'descripcion', 'activo', 'clave_orden')
            ->orderBy('clave_orden')
            ->get();

        // Delegar la construcción del árbol al controlador del paquete
        $packageController = new \Icatech\PermisoRolMenu\Http\Controllers\MenuController();
        $reflection = new \ReflectionClass($packageController);
        $method = $reflection->getMethod('buildMenuTree');
        $method->setAccessible(true);
        $menuTree = $method->invoke($packageController, $menus);

        return view('layouts.pages_admin.menu', compact('menuTree'));
    }

    /**
     * Delegar creación de menú al controlador del paquete
     */
    public function treeStore(Request $request)
    {
        $packageController = new \Icatech\PermisoRolMenu\Http\Controllers\MenuController();
        return $packageController->treeStore($request);
    }

    /**
     * Delegar actualización de estado al controlador del paquete
     */
    public function statusUpdate(Request $request, $id)
    {
        $packageController = new \Icatech\PermisoRolMenu\Http\Controllers\MenuController();
        return $packageController->statusUpdate($request, $id);
    }
}
