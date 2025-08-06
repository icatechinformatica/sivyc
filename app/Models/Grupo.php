<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    use HasFactory;

    protected $table = 'tbl_grupos';

    protected $fillable = [
        'id_unidad',
        'id_instructor',
        'clave_grupo',
        'id_modalidad_curso',
        'id_curso',
        'id_modalidad_capacitacion',
        'id_organismo_publico',
        'id_municipio',
        'id_localidad',
        'programa',
        'efisico',
        'cespecifico',
        'id_tipo_exoneracion',
        'medio_virtual',
        'link_virtual',
        'id_cerss',
        'asis_finalizado',
        'calif_finalizado',
        'num_revision',
        'num_revision_arc02',
        'evidencia_fotografica',
        'vb_dg',
    ];

    public function unidad()
    {
        return $this->belongsTo(Unidad::class, 'id_unidad');
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'id_instructor');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'id_curso');
    }

    public function estatus()
    {
        return $this->belongsToMany(Estatus::class, 'tbl_grupo_estatus', 'id_grupo', 'id_estatus');
    }

    public function tipoImparticion()
    {
        return $this->belongsTo(TipoImparticion::class, 'id_tipo_imparticion');
    }

    public function modalidad()
    {
        return $this->belongsTo(ModalidadGrupo::class, 'id_modalidad_curso');
    }
}
