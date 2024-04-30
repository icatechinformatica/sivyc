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
    'id_folios','instructor_perfilid','unidad_capacitacion','docs','observacion','cantidad_numero','arch_factura','arch_factura_xml',
    'fecha_status','chk_rechazado','fecha_rechazo','tipo_factura','arch_contrato','folio_fiscal','id_curso'
    ];

    protected $hidden = ['created_at', 'updated_at'];
    protected $casts = ['fecha_rechazo' => 'array'];


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
            if (!empty(trim($buscar)))
            {
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
                    case 'agendar_fecha':
                        return $query->WHEREIN('folios.status',['Verificando_Pago','Pago_Verificado','Pago_Rechazado'])
                                    ->WHERE('pagos.recepcion',NULL);
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
                    if($tipo_status == 'En Espera' || $tipo_status == 'VALIDADO')
                    {
                        if($tipo_status == 'En Espera')
                        {
                            return $query->WHERE('contratos.unidad_capacitacion', '=', $unidad)->WHERE('pagos.status_recepcion', '=', $tipo_status)->WHERE('folios.status', '!=', 'Finalizado')->ORDERBY('pagos.fecha_envio','ASC');
                        }
                        else
                        {
                            return $query->WHERE('contratos.unidad_capacitacion', '=', $unidad)->WHERE('pagos.status_recepcion', '=', $tipo_status)->WHERE('folios.status', '!=', 'Finalizado')->ORDERBY('pagos.updated_at','ASC');
                        }
                    }
                    else
                    {
                        return $query->WHERE('contratos.unidad_capacitacion', '=', $unidad)->WHERE('folios.status', '=', $tipo_status);
                    }
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
                    if($tipo_status == 'En Espera' || $tipo_status == 'VALIDADO')
                    {
                        return $query->whereDate('pagos.fecha_envio', '>=', $dateini)->whereDate('pagos.fecha_envio', '<=', $datefin)->WHERE('pagos.status_recepcion', '=', $tipo_status)->WHERE('folios.status', '!=', 'Finalizado');
                    }
                    return $query->whereDate('pagos.created_at', '>=', $dateini)->whereDate('pagos.created_at', '<=', $datefin)->WHERE('folios.status', '=', $tipo_status)->WHERE('folios.status', '!=', 'Finalizado');
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
            if($tipo == 'agendar_fecha')
            {
                if(isset($unidad))
                {
                    $query->WHERE('tabla_supre.unidad_capacitacion', '=', $unidad)->WHERE('pagos.status_recepcion', 'VALIDADO');
                }
                return $query->WHEREIN('folios.status',['Verificando_Pago','Pago_Verificado','Pago_Rechazado'])
                                ->WHERE('pagos.status_recepcion', 'VALIDADO')
                                ->WHERE('pagos.recepcion',NULL);
            }
            if($tipo == 'entregado_fisicamente')
            {
                if(isset($unidad))
                {
                    $query->WHERE('tabla_supre.unidad_capacitacion', '=', $unidad);
                }
                return $query->WHEREIN('folios.status',['Verificando_Pago','Pago_Verificado','Pago_Rechazado'])
                                ->WHERE('pagos.status_recepcion', 'VALIDADO')
                                ->WHERE('pagos.recepcion','!=',NULL);
            }
        }
        if (!empty($tipo_status)) {
            if($tipo_status == 'En Espera' || $tipo_status == 'VALIDADO')
            {
                if($tipo_status == 'En Espera')
                {
                    return $query->WHERE('pagos.status_recepcion', '=', $tipo_status)->WHERE('folios.status', '!=', 'Finalizado')->ORDERBY('pagos.fecha_envio','ASC');
                }
                else
                {
                    return $query->WHERE('pagos.status_recepcion', '=', $tipo_status)->WHERE('folios.status', '!=', 'Finalizado')->ORDERBY('pagos.updated_at','ASC');
                }
            }
            else
            {
                return $query->WHERE('folios.status', '=', $tipo_status);
            }
        }
    }
}
