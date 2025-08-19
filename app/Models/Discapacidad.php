<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discapacidad extends Model
{
    use HasFactory;

    public $table = 'tbl_cat_discapacidades';

    public $primaryKey = 'id_discapacidad';

    protected $fillable = [
        'discapacidad',
    ];
}
