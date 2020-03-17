<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    //
    protected $table = 'instructor';

    protected $fillable = [
            'id','nombre','apellido_paterno','apellido_materno','curp','rfc',
            'cv'
        ];

    protected $hidden = ['created_at', 'updated_at'];
}
