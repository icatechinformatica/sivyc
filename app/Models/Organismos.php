<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Funcionarios;

class Organismos extends Model
{
    protected $table = 'tbl_organismos';
    protected $fillable = ['id', 'id_parent', 'dif_dpto', 'nombre', 'siglas', 'clave', 'activo', 'iduser_created', 'iduser_updated', 'id_unidad', 'acc_movil'];
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Get all of the funcionarios for the Organismos
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function funcionarios(): HasMany
    {
        return $this->hasMany(Funcionarios::class, 'id_org', 'id');
    }


}
