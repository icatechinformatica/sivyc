<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    //
    protected $table = 'tbl_municipios';

    protected $fillable = [
        'id','muni','ze','id_estado','region','clave'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function alumunos()
    {
        return $this->hasMany(Alumno::class, 'id_municipio', 'id');
    }

    public function localidades()
    {
        return $this->hasMany(localidad::class, 'clave_municipio', 'id');
    }

}
