<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;

class Funcionarios extends Model
{
    protected $table = 'tbl_funcionarios';

    protected $fillable = ['id', 'id_org', 'titular', 'nombre', 'adscripcion', 'cargo', 'direccion', 'telefono', 'correo',
                            'activo', 'curp', 'titulo', 'incapacidad', 'correo_institucional'];

    protected $hidden = ['created_at', 'updated_at'];

    protected function scopeBusqueda($query, $buscar){
            if (!empty(trim($buscar))) {
                // return $query->where('tbl_funcionarios.nombre', 'iLIKE', "%$buscar%");
                return $query->whereRaw(
                    "CONCAT(tbl_funcionarios.nombre, ' ', tbl_funcionarios.curp, ' ', tbl_funcionarios.cargo) ILIKE ?",
                    ["%$buscar%"]
                );
            }
    }
}
