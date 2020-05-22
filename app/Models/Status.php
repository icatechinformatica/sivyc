<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    //
    protected $table = 'status';

    protected $fillable = [
        'id','estatus','perfil_profesional'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
