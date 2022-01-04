<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class grupos_vulnerbales extends Model
{
    // tabla de grupos_vulnerables
    protected $table = 'grupos_vulnerables';

    protected $fillable = ['id','grupo'];

    protected $hidden = ['created_at', 'updated_at'];
}
