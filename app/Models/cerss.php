<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class cerss extends Model
{
    //
    protected $table = 'cerss';

    protected $fillable = [
        'id','nombre','numero','direccion','id_municipio','titular','telefono','telefono2','id_unidad','activo',
        'iduser_create','iduser_update'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function scopeBusquedaCerss($query, $tipo, $buscar)
    {
        $x = 'no entro';
        if (!empty($tipo)) {
            # si tipo no es vacio se hace la busqueda
            if (!empty(trim($buscar))) {
                # empezamos
                switch ($tipo) {
                    case 'nombre':
                        # el tipo
                        return $query->WHERE('nombre', '=', $buscar);
                        break;
                    case 'titular':
                        # unidad de capacitacion
                        return $query->WHERE('titular', 'LIKE', $buscar);
                        break;
                }
            }
        }
    }

}
