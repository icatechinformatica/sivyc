<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class supre extends Model
{
    //
    protected $table = 'tabla_supre';

    protected $fillable = [
        'id', 'unidad_capacitacion', 'no_memo', 'fecha','nombre_para','puesto_para','nombre_remitente','puesto_remitente',
        'nombre_valida','puesto_valida','nombre_elabora','puesto_elabora','nombre_ccp1','puesto_ccp1','nombre_ccp2','puesto_ccp2',
        'status','observacion','folio_validacion','fecha_validacion','nombre,firmante','puesto_firmante','val_ccp1','val_ccpp1',
        'val_ccp2','val_ccpp2','val_ccp3','val_ccpp3','val_ccp4','val_ccpp4','fecha_status','fecha_rechazado'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * obtener el instructor que pertenece al perfil
     */
    public function tabla_supre()
    {
        return $this->hasMany(tablaFolio::class);
    }
    public function folios(){
        return $this->hasMany(folio::class);
    }

    public function contratos()
    {
        return $this->hasMany(contratos::class, 'id_supre');
    }
    /**
     * creación de un scope
     */
    public function scopeBusquedaSupre($query, $tipo, $buscar, $tipo_status, $unidad)
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
                    case 'no_memorandum':
                        # el tipo
                        return $query->WHERE('no_memo', '=', $buscar);
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
