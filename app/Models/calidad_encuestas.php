<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class calidad_encuestas extends Model
{
    //
    protected $table = 'calidad_encuestas';

    protected $fillable = [
        'id','idparent','nombre','activo','dirigido_a'
    ];

    protected $casts = [
        'respuestas' => 'array'
    ];

    protected $hidden = ['created_at', 'updated_at'];

}
