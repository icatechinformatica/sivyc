<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tbl_Solicitudes extends Model {
    
    protected $table = 'tbl_solicitudes';

    protected $fillable = [
        'id','id_curso','tipo_solicitud','num_solicitud','fecha_solicitud','opcion_solicitud','obs_solicitud', 
        'archivo_solicitud', 'num_respuesta','fecha_respuesta','obs_respuesta', 'archivo_respuesta','status','turnado',
        'iduser_created','iduser_updated'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
