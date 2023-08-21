<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banco extends Model
{
    //
    protected $table = 'bancos';

    protected $fillable = [
        'id','nombre','numero','activo'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
