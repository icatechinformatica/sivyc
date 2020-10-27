<?php

namespace App\Models\supervision;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class instructor extends Model
{
    protected $table = 'supervision_instructores';

    protected $fillable = ['id','nombre','apellido_paterno','apellido_materno',
    'fecha_padron','fecha_contrato','cct','clave_curso','nombre_curso','horas_curso','inicio_curso',
    'termino_curso','modalidad_curso','tipo_curso','total_mujeres','total_hombres','monto_honorarios','lugar_curso','id_tbl_cursos','created_at'];

    protected $hidden = [ 'updated_at'];

    /**
     * método slug
     */
    protected function getSlugAttribute($value): string {
        return Str::slug($value, '-');
    }

    public function setFechaAttribute($value) {
       // return Carbon::parse($value)->format('Y-m-d');
    }

    // in your model
    public function getMyDateFormat($value)
    {
       // return Carbon::parse($value)->format('d-m-Y');
    }

    public function scopeFiltrar($query, $campo, $valor){
        $id_user = Auth::user()->id;
        if (!empty($campo) AND !empty(trim($valor))) {
            switch ($campo) {
                case 'id_tbl_cursos':
                    $query = $query->where('supervision_instructores.id_tbl_cursos',$valor);
                    break;
            }
            $query = $query->where('supervision_instructores.id_user',$id_user);
            return $query->orderBy('supervision_instructores.created_at', 'DESC');
        }
    }

    /**
     * obtener los perfiles del instructor

    public function perfil()
    {
        return $this->hasMany(InstructorPerfil::class);
    }

    function curso_Validado()
    {
        return $this->hasMany(cursoValidado::class);
    }

    public function setFechaNacAttribute($value) {
        return $this->attributes['fecha_nacimiento'] = Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d');
     }
     */
}
