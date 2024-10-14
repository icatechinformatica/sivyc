<?php

namespace App\Models\cat;

use Illuminate\Database\Eloquent\Model;

class CatConcepto extends Model
{
    //
    protected $table = 'cat_conceptos';

    protected $fillable = [
        'id',
        'concepto',
        'importe',
        'tipo',
        'activo'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
