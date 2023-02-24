<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaqueteriasDidacticas extends Model
{
    //
    protected $table = 'paqueterias_didacticas';

    protected $fillable = [
        'id',
        'id_curso',
        'carta_descriptiva',
        'eval_alumno',
        'estatus',
        'id_user_created',
        'id_user_updated',
        'id_user_deleted',
        'created_at', 'updated_at', 'deleted_at'
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
