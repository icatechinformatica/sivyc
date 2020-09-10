<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curso extends Model
{

    protected $table = 'tbl_cursos';

    protected $fillable = [
            'id','cct','unidad','nombre','curp','rfc','clave',
            'mvalida','mod','area','espe','curso','inicio','termino','dia',
            'dura','hini','hfin','horas','ciclo','plantel','depen','muni','sector','programa',
            'nota','munidad','efisico','cespecifico','mpaqueteria','mexoneracion','hombre','mujer',
            'tipo','fcespe','cgeneral','fcgen','opcion','motivo','cp','ze','id_curso','id_instructor', 'modinstructor',
            'nmunidad', 'nmacademico', 'observaciones', 'status', 'realizo', 'valido', 'arc', 'tcapacitacion', 'status_curso',
            'fecha_apertura', 'fecha_modificacion'
        ];

    protected $hidden = ['created_at', 'updated_at'];

    public function calificacion()
    {
        return $this->hasMany(Calificacion::class, 'idcurso');
    }

}
