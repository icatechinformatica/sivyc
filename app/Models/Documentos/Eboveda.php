<?php

namespace App\Models\Documentos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eboveda extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tbl_eboveda';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'folio',
        'consecutivo',
        'data',
        'id_grupo',
        'id_estado',
        'cadena_original',
        'uuid_sellado',
        'fecha_sellado',
        'cadena_sellado',
        'documento_md5',
        'documento_xml',
        'contenido',
        'id_tipo',
        'link_verificacion',
        'id_status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'capacidad' => 'integer',
        'fecha_creacion' => 'datetime',
        'estado' => 'boolean',
    ];
}
