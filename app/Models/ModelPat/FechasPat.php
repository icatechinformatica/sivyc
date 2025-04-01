<?php

namespace App\Models\ModelPat;

use Illuminate\Database\Eloquent\Model;

class FechasPat extends Model
{
    protected $table = 'fechas_pat';

    protected $fillable = ['id', 'id_org', 'nombre_org', 'periodo', 'status', 'iduser_created', 'iduser_updated'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = ['fecha_meta' => 'json', 'fechas_avance' => 'json', 'status_meta' => 'json', 'status_avance' => 'json'];

    protected function scopeBusqueda($query, $buscar){
            if (!empty(trim($buscar))) {
                return $query->where('fechas_pat.fun_proc', 'iLIKE', "%$buscar%");
            }
    }
    protected function scopeBusquedaStatus($query, $sel_status, $mes, $sel_meta){

        if($sel_status == 'PENDIENTE'){
            $query->whereRaw("fechas_pat.status_avance->>'proceso' = '1'")
            ->whereRaw("fechas_pat.fechas_avance->'$mes'->>'fecenvioplane_a' != ''")
            ->whereRaw("fechas_pat.fechas_avance->'$mes'->>'statusmes' = ''")
            ->orderByRaw("fechas_pat.fechas_avance->'$mes'->>'fecenvioplane_a' ASC");
            return $query;

        }else if($sel_status == 'RETORNADO'){
            $query->whereRaw("fechas_pat.status_avance->>'retornado' = '1'")
            ->whereRaw("fechas_pat.fechas_avance->'$mes'->>'fecavanreturn' != ''")
            ->whereRaw("fechas_pat.fechas_avance->'$mes'->>'statusmes' = ''")
            ->orderByRaw("fechas_pat.fechas_avance->'$mes'->>'fecavanreturn' ASC");
            return $query;

        }else if($sel_status == 'AUTORIZADO'){
            $query->whereRaw("fechas_pat.fechas_avance->'$mes'->>'fecavanvalid' != ''")
            ->whereRaw("fechas_pat.fechas_avance->'$mes'->>'statusmes' = 'autorizado'")
            ->orderByRaw("fechas_pat.fechas_avance->'$mes'->>'fecavanvalid' ASC");
            return $query;

        }else if($sel_status == 'SIN_MOVIMIENTO'){
            $query->whereRaw("fechas_pat.fechas_avance->'$mes'->>'fecenvioplane_a' = '' ")
            ->whereRaw("fechas_pat.fechas_avance->'$mes'->>'fecavanreturn' = '' ")
            ->whereRaw("fechas_pat.fechas_avance->'$mes'->>'fecavanvalid' = '' ")
            ->orderByRaw("fechas_pat.id");
            return $query;

        //FILTRADO DE METAS

        }else if($sel_meta == 'PENDIENTES'){
            $query->whereRaw("fechas_pat.status_meta->>'proceso' = '1'")
            ->whereRaw("fechas_pat.fecha_meta->>'fecenvioplane_m' != ''")
            ->orderByRaw("fechas_pat.fecha_meta->>'fecenvioplane_m' ASC");
            return $query;

        }else if($sel_meta == 'RETORNADOS'){
            $query->whereRaw("fechas_pat.status_meta->>'retornado' = '1'")
            ->whereRaw("fechas_pat.fecha_meta->>'fecmetretorno' != ''")
            ->orderByRaw("fechas_pat.fecha_meta->>'fecmetretorno' ASC");
            return $query;

        }else if($sel_meta == 'VALIDADOS'){
            $query->whereRaw("fechas_pat.status_meta->>'validado' = '1'")
            ->whereRaw("fechas_pat.fecha_meta->>'fecmetvalid' != ''")
            ->orderByRaw("fechas_pat.fecha_meta->>'fecmetvalid' ASC");
            return $query;
        }else if($sel_meta == 'SIN_MOVIMIENTOS'){
            $query->whereRaw("fechas_pat.fecha_meta->>'fecenvioplane_m' = '' ")
            ->whereRaw("fechas_pat.fecha_meta->>'fecmetretorno' = '' ")
            ->whereRaw("fechas_pat.fecha_meta->>'fecmetvalid' = '' ")
            ->orderByRaw("fechas_pat.id");
            return $query;
        }else{
            $query->orderBy('fechas_pat.id', 'asc');
            return $query;
        }
    }
}
