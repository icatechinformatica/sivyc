<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    //
    protected $table = 'tbl_municipios';

    protected $fillable = [
        'id','muni','ze'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
