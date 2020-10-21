<?php
// Elaboró Romelia Pérez Nangüelú 
// rpnanguelu@gmail.com
namespace App\Models\supervision;

use Illuminate\Database\Eloquent\Model;

class funcionario extends Model
{
    protected $table = 'supervision_funcionarios';

    protected $fillable = ['id','fecha','nombre','apellido_paterno','apellido_materno',
    'rfc','enlace','escolaridad','partida_presupuestal','antiguedad_gobierno',
    'antiguedad_icatech','categoria','puesto','adscripcion_nominal','adscripcion_comision',
    'jefe_nominal','jefe_comision','tiempo_comision','mobiliario_equipo','reporta_actividades',
    'comision_fuera','cuantas_personas','actividades'];
    
    protected $hidden = [ 'created_at'];
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
    
    
}
