<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradoEstudio extends Model
{
    use HasFactory;

    public $table = 'tbl_cat_grado_estudios';

    public $primaryKey = 'id_grado_estudio';

    protected $fillable = [
        'grado_estudio'
    ];

    public function alumnos()
    {
        return $this->hasMany(Alumno::class, 'id_ultimo_grado_estudios', 'id_grado_estudio');
    }
}
