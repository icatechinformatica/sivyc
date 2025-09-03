<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Unidad extends Model
{
    //com
    protected $table = 'tbl_unidades';

    protected $fillable = [
        'id',
        'unidad',
        'cct',
        'dunidad',
        'dgeneral',
        'plantel',
        'academico',
        'vinculacion',
        'dacademico',
        'pdgeneral',
        'pdacademico',
        'pdunidad',
        'pacademico',
        'pvinculacion',
        'jcyc',
        'pjcyc',
        'ubicacion',
        'direccion',
        'telefono',
        'correo',
        'coordenadas',
        'codigo_postal',
        'order_poa'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->hasMany(User::class, 'id');
    }

    public function scopeBusquedaUnidad($query, $tipo, $buscar)
    {
        if (!empty($tipo)) {
            # si tipo no es vacio se hace la busqueda
            if (!empty(trim($buscar))) {
                # empezamos
                switch ($tipo) {
                    case 'unidad':
                        # el tipo
                        return $query->WHERE('unidad', '=', $buscar);
                    case 'cct':
                        # unidad de capacitacion
                        return $query->WHERE('cct', 'LIKE', $buscar);
                    case 'director':
                        # fecha
                        return $query->WHERE('dunidad', 'LIKE', $buscar);
                    case 'ubicacion':
                        # fecha
                        return $query->WHERE('ubicacion', '=', $buscar);
                }
            }
        }
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'id_unidad');
    }
}
