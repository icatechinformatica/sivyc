<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instituto extends Model
{
    protected $table = 'tbl_instituto';

    protected $fillable = [
        'id','name','direccion','telefono', 'url', 'correo', 'distintivo'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
