<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganoAdministrativo extends Model
{
    //
    protected $table = 'organo_administrativo';

    protected $fillable = [
        'id', 'organo', 'descripcion'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function adscripcion()
    {
        return $this->hasMany(AreaAdscripcion::class, 'organo_id');
    }
}
