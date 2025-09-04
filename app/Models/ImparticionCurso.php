<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImparticionCurso extends Model
{
    use HasFactory;

    protected $table = 'tbl_aux_tipo_curso';
    protected $primaryKey = 'id_tipo_curso';
    protected $fillable = ['tipo_curso'];
}
