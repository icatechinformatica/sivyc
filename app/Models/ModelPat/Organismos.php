<?php

namespace App\Models\ModelPat;

use App\Models\Unidad;
use App\Models\funcionario;
use Illuminate\Database\Eloquent\Model;

class Organismos extends Model
{
    protected $table = 'tbl_organismos';

    protected $fillable = ['id', 'id_parent', 'dif_dpto', 'nombre', 'siglas', 'clave', 'activo', 'id_unidad', 'created_at', 'updated_at', 'iduser_created', 'iduser_updated'];

    protected $hidden = ['created_at', 'updated_at'];

    // Relación con Unidad
    public function unidad()
    {
        return $this->belongsTo(Unidad::class, 'id_unidad');
    }

    // Relación many-to-many con funcionarios
    public function funcionarios()
    {
        return $this->belongsToMany(
            funcionario::class,
            'tbl_func_org',
            'id_org',
            'id_fun'
        );
    }
}
