<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;

class Afolios extends Model
{
    //
    protected $table = 'tbl_afolios';

    protected $fillable = [
        'id', 'unidad', 'finicial', 'ffinal', 'total', 'mod', 'facta', 'realizo'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
