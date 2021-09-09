<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    //
    protected $table = 'users';

    protected $fillable = [
        'id','name', 'email', 'unidad', 'puesto',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
