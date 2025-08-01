<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class pais extends Model
{
    //
    protected $table = 'tbl_paises';

    protected $fillable = [
        'id','nombre'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function alumnos()
    {
        return $this->hasMany(Alumno::class, 'id_pais');
    }
}
