<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class instructor extends Model
{
    protected $table = 'instructor';

    /*protected $fillable = ['id','nombre','apellido_paterno','apellido_materno','curp','rfc','sexo','estado_civil','fecha_nacimiento','lugar_nacimiento','lugar_residencia',
    'domicilio','telefono','correo','clabe','banco','grado_estudio','perfil_profesional','area_carrera','licenciatura','estatus','institucion_pais','institucion_entidad',
    'institucion_nombre','fecha_documento','folio_documento','capacitado_icatech','cv','numero_control','honorario','registro_agente','uncap_validacion','memo_validacion','memo_mod','observacion','numero_cuenta',];*/

    protected $fillable = ['id','nombre','apellido_paterno','apellido_materno','curp','rfc','cv'];

    protected $hidden = ['created_at', 'updated_at'];

            /**
     * método slug
     */
    protected function getSlugAttribute($value): string {
        return Str::slug($value, '-');
    }


}

