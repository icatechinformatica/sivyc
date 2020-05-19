<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class estado_civil extends Model
{
    //
    protected $table = 'estado_civil';

    protected $fillable = [
        'id','nombre',
    ];

    protected $hidden = ['created_at', 'updated_at'];

}
