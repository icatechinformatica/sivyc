<?php

namespace App\Models\ModelPat;

use Illuminate\Database\Eloquent\Model;

class RegistrosProced extends Model
{
    protected $table = 'metas_avances_pat';



    protected $fillable = ['id', 'id_proced', 'ejercicio', 'total', 'iduser_created', 'iduser_updated', 'observaciones', 'observmeta', 'updated_at'];

    protected $hidden = ['created_at'];
    protected $casts = ['enero' => 'json', 'febrero' => 'json', 'marzo' => 'json', 'abril' => 'json', 'mayo' => 'json',
                        'junio' => 'json', 'julio' => 'json', 'agosto' => 'json', 'septiembre' => 'json', 'octubre' => 'json',
                        'noviembre' => 'json', 'diciembre' => 'json'];
}
