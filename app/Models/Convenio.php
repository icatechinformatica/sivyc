<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Convenio extends Model
{
    // tabla de convenios
    protected $table = 'convenios';

    protected $fillable = ['id', 'no_convenio', 'tipo_sector', 'institucion', 'fecha_firma', 'fecha_vigencia',
                            'archivo_convenio', 'poblacion', 'municipio', 'nombre_titular', 'nombre_enlace', 'direccion', 'telefono', 'status'];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * mutator en laravel
     */

    // in your model
    public function getMyDateFormat($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }
}
