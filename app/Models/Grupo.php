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
        'id_modalidad',
        'id_curso',
        'id_organismo_publico',
        'id_municipio',
        'id_localidad',
        'programa',
        'efisico',
        'cespecifico',
        'fecha_cespecifico',
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
        'id_imparticion',
        'organismo_representante',
        'organismo_telefono_representante',
        'nombre_lugar',
        'colonia',
        'calle_numero',
        'codigo_postal',
        'referencias',
        'id_servicio',
    ];

    public $timestamps = false;

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
        return $this->belongsToMany(Estatus::class, 'tbl_grupo_estatus', 'id_grupo', 'id_estatus')
                    ->withPivot('created_at', 'updated_at');
    }

    /**
     * Obtener el estatus actual (más reciente) del grupo
     */
    public function estatusActual()
    {
        return $this->estatus()
                    ->orderBy('tbl_grupo_estatus.updated_at', 'desc')
                    ->orderBy('tbl_grupo_estatus.created_at', 'desc')
                    ->first();
    }

    /**
     * Verificar si el grupo tiene un estatus específico
     */
    public function tieneEstatus($nombreEstatus)
    {
        return $this->estatus()->where('estatus', $nombreEstatus)->exists();
    }

    public function tipoImparticion()
    {
        return $this->belongsTo(ImparticionCurso::class, 'id_tipo_imparticion');
    }

    public function modalidad()
    {
        return $this->belongsTo(ModalidadCurso::class, 'id_modalidad_curso');
    }
}
