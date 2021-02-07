<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    // modelo de área
    protected $table = 'area';

    protected $fillable = ['id','formacion_profesional'];

    protected $hidden = ['created_at', 'updated_at'];

    public function curso()
    {
        return $this->hasMany(curso::class, 'area');
    }

    public function scopeBusqueda($query, $tipo, $buscar)
    {
        if (!empty($tipo)) {
            if (!empty(trim($buscar))) {
                switch ($tipo) {
                    case 'formacion_profesional':
                        return $query->where('area.formacion_profesional', '=', $buscar);
                        break;
                }
            }
        }
    }
}
