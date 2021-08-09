<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    //
    protected $table = 'directorio';

    protected $fillable = [
        'id', 'nombre', 'apellidoPaterno', 'apellidoMaterno', 'puesto', 'numero_enlace', 'categoria' , 'area_adscripcion_id',
        'activo', 'qr_generado','curp','email'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function adscripcion()
    {
        return $this->belongsTo(AreaAdscripcion::class);
    }

    // scopes
    public function scopeBusqueda($query, $tipo, $buscar)
    {
        if (!empty($tipo)) {
            # entramos y validamos
            if (!empty(trim($buscar))) {
                # empezamos
                switch ($tipo) {
                    case 'numero_enlace':
                        # code...
                        return $query->where('numero_enlace', '=', $buscar);
                        break;
                    case 'nombres':
                        # code...
                        return $query->where( \DB::raw('CONCAT("apellidoPaterno", '."' '".' ,"apellidoMaterno",'."' '".',nombre)'), 'LIKE', "%$buscar%");
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
    }
}
