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

    public function alumnos()
    {
        return $this->hasMany(Alumno::class, 'id_estado');
    }
    public function municipios()
    {
        return $this->hasMany(Municipio::class, 'id_estado');
    }
}
