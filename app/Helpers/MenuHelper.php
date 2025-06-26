<?php

namespace App\Helpers;

class MenuHelper
{
    public static function buildMenu($permissions)
    {
        $menu = [];

        foreach ($permissions as $perm) {
            $clave = $perm->clave_orden;
            if (!$clave) continue;

            // Menú principal
            if (preg_match('/^\d{2}0000$/', $clave)) {
                $menu[$clave] = [
                    'permiso' => $perm,
                    'submenus' => []
                ];
            }
            // Submenú
            elseif (preg_match('/^\d{4}00$/', $clave)) {
                $main = substr($clave, 0, 2) . '0000';
                if (!isset($menu[$main])) $menu[$main] = ['permiso' => null, 'submenus' => []];
                $menu[$main]['submenus'][$clave] = [
                    'permiso' => $perm,
                    'submenus' => []
                ];
            }
            // Sub-submenú
            elseif (preg_match('/^\d{6}$/', $clave)) {
                $main = substr($clave, 0, 2) . '0000';
                $sub = substr($clave, 0, 4) . '00';
                if (!isset($menu[$main])) $menu[$main] = ['permiso' => null, 'submenus' => []];
                if (!isset($menu[$main]['submenus'][$sub])) $menu[$main]['submenus'][$sub] = ['permiso' => null, 'submenus' => []];
                $menu[$main]['submenus'][$sub]['submenus'][$clave] = [
                    'permiso' => $perm,
                    'submenus' => []
                ];
            }
        }

        // Ordenar por clave_orden
        ksort($menu);
        foreach ($menu as &$m) {
            ksort($m['submenus']);
            foreach ($m['submenus'] as &$sm) {
                ksort($sm['submenus']);
            }
        }

        return $menu;
    }
}
