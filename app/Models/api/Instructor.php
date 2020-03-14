<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    //
    protected $table = 'tbl_instructor';

    protected $fillable = [
            'id','unidad','nombre','fnacimiento','sexo','rfc','curp',
            'muni','domi','turno','col','codigo','tcasa','tcelular','email','espe','inst','valida',
            'mcontrato','contrato','hononetos','mocontrato','nrhono','escol','doc'
        ];

    protected $hidden = ['created_at', 'updated_at'];
}
