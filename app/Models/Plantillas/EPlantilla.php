<?php

namespace App\Models\Plantillas;

use Illuminate\Database\Eloquent\Model;

class EPlantilla extends Model
{
    protected $table = 'tbl_eplantillas';

    protected $fillable = ['id', 'tipo', 'cuerpo', 'vigencia', 'pie', 'encabezado', 'firmantes'];

    protected $hidden = ['created_at', 'updated_at'];
}
