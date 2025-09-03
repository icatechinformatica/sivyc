<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Interfaces\Repositories\CatalogoRepositoryInterface;

class CatalogoRepository implements CatalogoRepositoryInterface
{
    /**
     * Obtiene CERSS por unidad
     */
    public function obtenerCerss($id_unidad = null)
    {
        $query = DB::table('cerss');
        
        if ($id_unidad) {
            $query = $query->where('id_unidad', $id_unidad)->where('activo', true);
        }
        
        return $query->orderby('nombre', 'ASC')->pluck('nombre', 'id');
    }

    /**
     * Obtiene municipios según criterios
     */
    public function obtenerMunicipios($es_cct_especial = false, $unidad = null)
    {
        $query = DB::table('tbl_municipios')->where('id_estado', '7');
        
        if (!$es_cct_especial && $unidad) {
            $query = $query->whereJsonContains('unidad_disponible', $unidad);
        }
        
        return $query->orderby('muni')->pluck('muni', 'id');
    }

    /**
     * Obtiene organismos públicos activos
     */
    public function obtenerOrganismosPublicos()
    {
        return DB::table('organismos_publicos')
            ->where('activo', true)
            ->orderby('organismo')
            ->pluck('organismo', 'organismo');
    }

    /**
     * Obtiene grupos vulnerables
     */
    public function obtenerGruposVulnerables()
    {
        return DB::table('grupos_vulnerables')
            ->orderBy('grupo')
            ->pluck('grupo', 'id');
    }

    /**
     * Obtiene localidades por municipio
     */
    public function obtenerLocalidadesPorMunicipio($id_municipio)
    {
        $clave = DB::table('tbl_municipios')->where('id', $id_municipio)->value('clave');
        
        return DB::table('tbl_localidades')
            ->where('id_estado', '7')
            ->where('clave_municipio', '=', $clave)
            ->pluck('localidad', 'clave');
    }
}
