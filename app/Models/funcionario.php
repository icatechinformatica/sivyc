<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class funcionario extends Model
{
    //
    protected $table = 'tbl_funcionario';

    protected $fillable = [
        'id','clave_cat','categoria_estatal','clave_puesto','puesto_estatal','nombre_adscripcion','clave_empleado','fecha_ingreso','fecha_baja',
        'nombre_trabajador','rfc_usuario','curp_usuario','num_comisionados','fecha_comision','comision_direccion_o_unidad','comision:depto',
        'comision_accion_movil','titular','direccion','telefono','correo','estatus','titulo','incapacidad','correo_institucional','id_user_created',
        'id_user_updated'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function scopeBusquedaRH($query, $tipo, $buscar)
    {
        if (!empty($tipo)) {
            # si tipo no es vacio se hace la busqueda
            if($tipo == 'unidad_capacitacion')
            {
                # busqueda por unidad capacitacion...
                if (!empty($tipo_status))
                {
                    return $query->WHERE('tabla_supre.unidad_capacitacion', '=', $unidad)->WHERE('tabla_supre.status', '=', $tipo_status);
                }
                else
                {
                    return $query->WHERE('tabla_supre.unidad_capacitacion', '=', $unidad);
                }
            }
            if (!empty(trim($buscar))) {
                # empezamos
                switch ($tipo) {
                    case 'nombre':
                        # el tipo
                        return $query->WHERE('tbl_funcionario.nombre_trabajador', 'LIKE', '%'.$buscar.'%');
                        break;
                    case 'no_enlace':
                        # el tipo
                        return $query->WHERE('tbl_funcionario.clave_empleado', '=', $buscar);
                    break;
                }
            }
        }
        if (!empty($tipo_status)) {
            return $query->WHERE('tabla_supre.status', '=', $tipo_status);
        }
    }
}
