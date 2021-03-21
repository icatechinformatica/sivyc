<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class contratos extends Model
{
    //
    protected $table = 'contratos';
    protected $primaryKey = 'id_contrato';

    protected $fillable = ['id_contrato','numero_contrato','cantidad_letras1','fecha_firma','municipio',
    'id_folios','instructor_perfilid','unidad_capacitacion','docs','observacion','cantidad_numero','arch_factura','fecha_status'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function supre()
    {
        return $this->belongsTo(supre::class, 'id_supre');
    }
    public function perfil_instructor()
    {
        return $this->belongsTo(InstructorPerfil::class, 'id_folios');
    }

    /**
     * scope de busqueda por contratos
     */
    public function scopeBusquedaPorContrato($query, $tipo, $buscar, $tipo_status, $unidad, $mes)
    {
        if (!empty($tipo)) {
            # se valida el tipo
            if($tipo == 'unidad_capacitacion')
            {
                # busqueda por unidad capacitacion...
                if (!empty($tipo_status))
                {
                    return $query->WHERE('tabla_supre.unidad_capacitacion', '=', $unidad)->WHERE('folios.status', '=', $tipo_status);
                }
                else
                {
                    return $query->WHERE('tabla_supre.unidad_capacitacion', '=', $unidad);
                }
            }
            if($tipo == 'mes')
            {
                $now = Carbon::now();
                $dateini = $now->year.'-'.$mes.'-01';
                if($mes == '01' || $mes == '03' || $mes == '05' || $mes == '07' || $mes == '08' || $mes == '10' || $mes == '12')
                {
                    $datefin = $now->year.'-'.$mes.'-31';
                }
                if($mes == '04' || $mes == '06' || $mes == '09' || $mes == '11')
                {
                    $datefin = $now->year.'-'.$mes.'-30';
                }
                if($mes == '02')
                {
                    $datefin = $now->year.'-'.$mes.'-28';
                }
                //dd($datefin);
                # busqueda por unidad capacitacion...
                if (!empty($tipo_status))
                {
                    return $query->whereDate('contratos.created_at', '>=', $dateini)->whereDate('contratos.created_at', '<=', $datefin)->WHERE('folios.status', '=', $tipo_status);
                }
                else
                {
                    return $query->whereDate('contratos.created_at', '>=', $dateini)->whereDate('contratos.created_at', '<=', $datefin);
                }
            }
            if (!empty(trim($buscar))) {
                # busqueda
                switch ($tipo) {
                    case 'no_memorandum':
                        # busqueda por memorandum...
                        if (!empty($tipo_status)) {
                            return $query->WHERE('tabla_supre.no_memo', '=', $buscar)->WHERE('folios.status', '=', $tipo_status);
                        }
                        else {
                            return $query->WHERE('tabla_supre.no_memo', '=', $buscar);
                        }
                        break;
                    case 'fecha':
                        # busqueda por fecha ...
                        if (!empty($tipo_status)) {
                            return $query->WHERE('tabla_supre.fecha', '=', $buscar)->WHERE('folios.status', '=', $tipo_status);
                        }
                        else {
                            return $query->WHERE('tabla_supre.fecha', '=', $buscar);
                        }
                        break;
                    case 'folio_validacion':
                        # busqueda por folio de validacion
                        return $query->WHERE('folios.folio_validacion', '=', $buscar);
                        break;
                }
            }
        }
        if (!empty($tipo_status)) {
            return $query->WHERE('folios.status', '=', $tipo_status);
        }
    }

    /**
     * busqueda scope por pagos
     */
    public function scopeBusquedaPorPagos($query, $tipo, $buscar, $tipo_status, $unidad, $mes)
    {
        if (!empty($tipo)) {
            # se valida el tipo
            if($tipo == 'unidad_capacitacion')
            {
                # busqueda por unidad capacitacion...
                if (!empty($tipo_status))
                {
                    return $query->WHERE('contratos.unidad_capacitacion', '=', $unidad)->WHERE('folios.status', '=', $tipo_status);
                }
                else
                {
                    return $query->WHERE('contratos.unidad_capacitacion', '=', $unidad);
                }
            }
            if($tipo == 'mes')
            {
                $now = Carbon::now();
                $dateini = $now->year.'-'.$mes.'-01';
                if($mes == '01' || $mes == '03' || $mes == '05' || $mes == '07' || $mes == '08' || $mes == '10' || $mes == '12')
                {
                    $datefin = $now->year.'-'.$mes.'-31';
                }
                if($mes == '04' || $mes == '06' || $mes == '09' || $mes == '11')
                {
                    $datefin = $now->year.'-'.$mes.'-30';
                }
                if($mes == '02')
                {
                    $datefin = $now->year.'-'.$mes.'-28';
                }
                //dd($datefin);
                # busqueda por unidad capacitacion...
                if (!empty($tipo_status))
                {
                    return $query->whereDate('pagos.created_at', '>=', $dateini)->whereDate('pagos.created_at', '<=', $datefin)->WHERE('folios.status', '=', $tipo_status);
                }
                else
                {
                    return $query->whereDate('pagos.created_at', '>=', $dateini)->whereDate('pagos.created_at', '<=', $datefin);
                }
            }
            if (!empty(trim($buscar))) {
                # busqueda
                switch ($tipo) {
                    case 'no_contrato':
                        # busqueda por número de contrato
                        return $query->WHERE('contratos.numero_contrato', '=', $buscar);
                        break;
                    case 'fecha_firma':
                        # busqueda por fechas
                        if (!empty($tipo_status)) {
                            return $query->WHERE('contratos.fecha_firma', '=', $buscar)->WHERE('folios.status', '=', $tipo_status);;
                        }
                        else {
                            return $query->WHERE('contratos.fecha_firma', '=', $buscar);
                        }
                        break;
                }
            }
        }
        if (!empty($tipo_status)) {
            return $query->WHERE('folios.status', '=', $tipo_status);
        }
    }
}
