<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class User extends Model
{
    use Notifiable;
    //
    protected $table = 'users';

    protected $fillable = [
        'id','name', 'email', 'unidad', 'puesto',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
