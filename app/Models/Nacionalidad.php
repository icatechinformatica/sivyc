<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nacionalidad extends Model
{
    use HasFactory;

    protected $table = 'tbl_cat_nacionalidades';
    protected $primaryKey = 'id_nacionalidad';

    protected $fillable = [
        'id_nacionalidad',
        'nacionalidad',
    ];

    public function alumnos()
    {
        return $this->hasMany(Alumno::class, 'nacionalidad_id');
    }
}
