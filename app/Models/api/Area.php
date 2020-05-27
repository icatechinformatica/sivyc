<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    //
    protected $table = 'area';

    protected $fillable = [
        'id','formacion_profesional'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function curso()
    {
        return $this->hasMany(curso::class, 'area');
    }
}
