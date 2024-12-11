<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\cat\Cargos;
use App\Models\Organismos;

class Funcionarios extends Model
{
    //
    protected $table = 'tbl_funcionarios';
    protected $fillable = ['id', 'id_org', 'titular', 'nombre', 'adscripcion', 'cargo', 'direccion', 'telefono', 'correo', 'activo', 'iduser_created', 'iduser_updated', 'curp', 'titulo', 'incapacidad', 'correo_institucional', 'cargo_id'];
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Get the user that owns the Funcionarios
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cargos(): BelongsTo
    {
        return $this->belongsTo(Cargos::class, 'cargo_id', 'id');
    }

    /**
     * Get the organismos that owns the Funcionarios
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organismos(): BelongsTo
    {
        return $this->belongsTo(Organismos::class, 'id_org', 'id');
    }
}
