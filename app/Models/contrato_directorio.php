<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class contrato_directorio extends Model
{
    // tabla de convenios
    protected $table = 'contrato_directorio';

    protected $fillable = ['id','contrato_iddirector','contrato_idtestigo1','contrato_idtestigo2','contrato_idtestigo3','id_contrato'];

    protected $hidden = ['created_at', 'updated_at'];
}
