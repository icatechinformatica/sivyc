<?php

namespace App\Http\Controllers\adminController;


use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Permission::whereNotNull('clave_orden')
            ->select('id', 'name', 'slug', 'description', 'activo', 'clave_orden')
            ->orderBy('clave_orden')
            ->get();

        // Organizar los menús en estructura de árbol
        $menuTree = $this->buildMenuTree($menus);

        return view('layouts.pages_admin.menus', compact('menuTree'));
    }

    private function buildMenuTree($menus, $nivel = 1, $claveProveniente = '')
    {
        $tree = [];

        $filtered = $menus->filter(function ($menu) use ($nivel, $claveProveniente) {
            $segmentos = str_split($menu->clave_orden, 2);
            if ($nivel === 1) {
                return $segmentos[0] !== '00' && $segmentos[1] === '00' && $segmentos[2] === '00';
            }
            $parentSeg = str_split($claveProveniente, 2);
            if ($nivel === 2) {
                return $segmentos[0] === $parentSeg[0] && $segmentos[1] !== '00' && $segmentos[2] === '00';
            }
            if ($nivel === 3) {
                return $segmentos[0] === $parentSeg[0] && $segmentos[1] === $parentSeg[1] && $segmentos[2] !== '00';
            }
            return false;
        });

        foreach ($filtered as $menu) {
            $item = $menu->toArray();
            $submenu = $this->buildMenuTree($menus, $nivel + 1, $menu->clave_orden);
            if (!empty($submenu)) {
                $item['submenu'] = $submenu;
            }
            $tree[] = $item;
        }

        return $tree;
    }

    public function statusUpdate(Request $request, $id)
    {
        $menu = Permission::find($id);
        if ($menu) {
            $newStatus = !$menu->activo;
            $menu->activo = $newStatus;
            $menu->save();

            $updatedChildIds = $this->updateSubmenusStatus($menu, $newStatus);
            // Incluir el ID del menú padre
            $allUpdated = array_merge([$menu->id], $updatedChildIds);
            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado correctamente',
                'updatedIds' => $allUpdated
            ]);
        }
        return response()->json(['success' => false, 'message' => 'Menu no encontrado'], 404);
    }

    /**
     * Actualiza el estado de los submenus y devuelve IDs actualizados.
     *
     * @param Permission $menu
     * @param bool $status
     * @return array
     */
    private function updateSubmenusStatus(Permission $menu, bool $status): array
    {
        // * Determinar nivel y prefijo según nomenclatura de clave_orden
        $clave = $menu->clave_orden;
        if (substr($clave, 2, 2) === '00' && substr($clave, 4, 2) === '00') {
            // * Nivel 1: prefix 2 caracteres
            $prefixLen = 2;
        } elseif (substr($clave, 4, 2) === '00') {
            // * Nivel 2: prefix 4 caracteres
            $prefixLen = 4;
        } else {
            // * Nivel 3 o más: todo clave (6 caracteres)
            $prefixLen = strlen($clave);
        }
        $prefix = substr($clave, 0, $prefixLen);
        // Obtener IDs de todos los descendientes que comiencen con el prefijo
        $query = Permission::where('clave_orden', 'like', $prefix . '%')
            ->where('id', '!=', $menu->id);
        $ids = $query->pluck('id')->toArray();
        // Actualizar estado
        $query->update(['activo' => $status]);
        return $ids;
    }
}
