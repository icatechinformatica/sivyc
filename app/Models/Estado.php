<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    //
    protected $table = 'estados';

    protected $fillable = [
        'id','nombre'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
