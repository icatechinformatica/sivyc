<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModalidadCurso extends Model
{
    use HasFactory;

    protected $table = 'tbl_aux_modalidad_curso';
    protected $primaryKey = 'id_modalidad_curso';
    protected $fillable = ['modalidad'];

}
