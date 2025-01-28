<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class checador_asistencia extends Model
{
    //
    protected $table = 'tbl_checador_asistencias';

    protected $fillable = [
        'id','numero_enlace','fecha','entrada','salida','retardo','inasistencia','justificante','observaciones'
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
                    case 'fecha':
                        # fecha
                        if (!empty($tipo_status)) {
                            return $query->WHERE('fecha', '=', $buscar)->WHERE('tabla_supre.status', '=', $tipo_status);
                        }
                        else {
                            return $query->WHERE('fecha', '=', $buscar);
                        }
                        break;
                }
            }
        }
        if (!empty($tipo_status)) {
            return $query->WHERE('tabla_supre.status', '=', $tipo_status);
        }
    }
}
