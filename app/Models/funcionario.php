<?php

namespace App\Models;

use App\Models\ModelPat\Organismos;
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

    public function scopeBusquedaRH($query, $tipo, $buscar, $unidad)
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

    public function user()
    {
        return $this->morphOne(User::class, 'registro', 'registro_type', 'registro_id');
    }

    // Relación many-to-many con organismos
    public function organismos()
    {
        return $this->belongsToMany(
            Organismos::class,
            'tbl_func_org',
            'id_fun',
            'id_org'
        );
    }

    // Relación para obtener la unidad a través del organismo
    public function unidad()
    {
        return $this->hasOneThrough(
            Unidad::class,
            Organismos::class,
            'id', // Foreign key en tbl_organismos
            'id', // Foreign key en tbl_unidades
            'id', // Local key en tbl_funcionario
            'id_unidad' // Local key en tbl_organismos
        )->join('tbl_func_org', 'tbl_organismos.id', '=', 'tbl_func_org.id_org')
         ->where('tbl_func_org.id_fun', $this->id);
    }

    // Método auxiliar para obtener la primera unidad (si un funcionario puede tener múltiples organismos)
    public function getPrimeraUnidad()
    {
        $organismo = $this->organismos()->with('unidad')->first();
        return $organismo?->unidad;
    }

    // Método para obtener todas las unidades del funcionario
    public function todasLasUnidades()
    {
        return $this->organismos()->with('unidad')->get()->pluck('unidad')->filter();
    }
}
