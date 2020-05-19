<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class criterio_pago extends Model
{
    //
    protected $table = 'criterio_pago';

    protected $fillable = [
        'id','perfil_profesional','monto_hora_ze2','monto_hora_ze3'
    ];

    protected $hidden = ['created_at', 'updated_at'];

}
