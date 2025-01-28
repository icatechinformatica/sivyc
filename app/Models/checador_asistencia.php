<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class checador_asistencia extends Model
{
    //
    protected $table = 'tbl_checador_asistencias';

    protected $fillable = [
        'id','numero_enlace','fecha','entrada','salida','retraso','inasistencia','justificante','observaciones'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
