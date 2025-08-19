<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sexo extends Model
{
    use HasFactory;

    protected $table = 'tbl_sexo';

    protected $fillable = ['id', 'sexo'];

    public function alumnos()
    {
        return $this->hasMany(Alumno::class, 'id_sexo', 'id');
    }
}
