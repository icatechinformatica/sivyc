<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\cat\catApertura;

class tbl_inscripcion extends Model
{ 
    use catApertura;
    protected function reemplazar($folio_grupo, $curp_anterior = null, $curp_nueva = null ){ //dd($curp_nueva);
        $message = 'La operación ha fallado, por favor intente de nuevo..'; 
        if($folio_grupo) {
            if ($curp_anterior AND $curp_nueva) {
                $id = DB::table('alumnos_registro as ar')
                    ->leftJoin('alumnos_pre as ap','ar.id_pre','=','ap.id')
                    ->where('ar.folio_grupo',$folio_grupo)
                    ->where('ap.curp',$curp_anterior)
                    ->value('ar.id');
                if ($id) {
                    $date = date('d-m-Y');
                    $alumno_nuevo = DB::table('alumnos_pre')
                        ->select('id as id_pre','curp', 'matricula', DB::raw("cast(EXTRACT(year from(age('$date', fecha_nacimiento))) as integer) as edad"),
                         DB::raw("cast(EXTRACT(year from(age('$date', fecha_nacimiento))) as integer) as edad"),'ultimo_grado_estudios as escolaridad','nombre','apellido_paterno','apellido_materno'
                        )
                        ->where('curp', $curp_nueva)
                        ->where('activo', true)
                        ->first();
                    if ($alumno_nuevo) {
                        $alumno = $alumno_nuevo->nombre." ".$alumno_nuevo->apellido_paterno." ".$alumno_nuevo->apellido_materno;
                        if ($alumno_nuevo->edad >= 15) {
                            if (substr($curp_anterior, 10, 1) == substr($curp_nueva, 10, 1)) {
                                $result = DB::table('alumnos_registro')
                                    ->where('folio_grupo', $folio_grupo)
                                    ->where('id', $id)
                                    ->update([
                                        'id_pre' => $alumno_nuevo->id_pre, 
                                        'no_control' => $alumno_nuevo->matricula,
                                        'nombre'=>$alumno_nuevo->nombre, 
                                        'apellido_paterno'=>$alumno_nuevo->apellido_paterno,
                                        'apellido_materno'=>$alumno_nuevo->apellido_materno,
                                        'curp'=>$alumno_nuevo->curp,
                                        'escolaridad'=>$alumno_nuevo->escolaridad,
                                        'iduser_updated' => Auth::user()->id,
                                        'updated_at' => date('Y-m-d H:i')
                                    ]);
                                if ($result) {
                                    //ACTUALIZAR tbl_inscripcion
                                    $result2 = $this->inscribir($folio_grupo, $curp_anterior, $curp_nueva);
                                    if($result2)$message = "Operación exitosa !!..";
                                }
                            } else $message = "Los generos no coiniciden..";   
                        }else $message = "La edad del alumno ".$alumno." es menor de 15 años. Por favor, verifique.";
                    }else $message = "Alumno no registrado " . $curp_nueva . ".";                    
                } else $message = "Alumno no registrado " . $curp_anterior . ".";                
            }else $message = "Ingrese la CURP del alumno..";            
        }
        return $message;
    }


    protected function inscribir($folio_grupo, $curp_anterior = null, $curp_nueva = null ){// dd($curp_nueva);
        $return = false;
        if($folio_grupo){
            $grupo = DB::table('tbl_cursos')->where('status_curso','AUTORIZADO')->where('status','NO REPORTADO')->where('folio_grupo',$folio_grupo)->first();
            if($grupo){
                $abrinscri = $this->abrinscri();
                $anio_hoy = date('y');          

                $alumnos = DB::table('alumnos_registro as ar')->select('ar.id as id_reg','ar.curp','ar.nombre','ar.apellido_paterno','ar.apellido_materno',
                    'ap.fecha_nacimiento AS FN','ap.sexo AS SEX','ar.id_cerss', 'ap.lgbt',DB::raw("CONCAT(ar.apellido_paterno,' ', ar.apellido_materno,' ',ar.nombre) as alumno"),
                    'ap.estado_civil','ap.discapacidad','ap.nacionalidad','ap.etnia','ap.indigena','ap.inmigrante','ap.madre_soltera','ap.familia_migrante',
                    'ar.costo','ar.tinscripcion',DB::raw("'0' as calificacion"),'ar.escolaridad','ap.empleado','ar.abrinscri',
                    'ap.matricula', 'ar.id_pre','ar.id', DB::raw("substring(ar.curp,11,1) as sexo"),'ap.id_gvulnerable',
                    DB::raw("substring(ar.curp,5,2) as anio_nac"),
                    DB::raw("CASE WHEN substring(ar.curp,5,2) <='".$anio_hoy."' THEN CONCAT('20',substring(ar.curp,5,2),'-',substring(ar.curp,7,2),'-',substring(ar.curp,9,2))
                        ELSE CONCAT('19',substring(ar.curp,5,2),'-',substring(ar.curp,7,2),'-',substring(ar.curp,9,2)) END AS fecha_nacimiento
                    "),                                    
                    DB::raw("EXTRACT(year from (age('".$grupo->inicio."',ap.fecha_nacimiento))) as edad"),                    
                    DB::raw("
                        CASE 
                            WHEN ap.id_gvulnerable IS NULL THEN NULL
                            ELSE ( SELECT STRING_AGG(grupo, ', ') FROM grupos_vulnerables WHERE id IN ( SELECT CAST(jsonb_array_elements_text(ap.id_gvulnerable) AS bigint)))
                        END
                        as grupos "), 'ap.inmigrante','es_cereso','ap.requisitos',
                    DB::raw("'INSERT' as mov"))
                    ->join('alumnos_pre as ap','ap.id','ar.id_pre')->where('ar.folio_grupo',$folio_grupo)
                    ->where('ar.eliminado',false)->orderby('ap.apellido_paterno','ASC')->orderby('ap.apellido_materno','ASC')->orderby('ap.nombre','ASC')->get();
                    if($curp_nueva) $alumnos = $alumnos->where('curp',$curp_nueva);

                foreach($alumnos as $a){                    
                    $tinscripcion = $a->tinscripcion;
                    $abrinscriTMP = $a->abrinscri;
                    $matricula = $a->matricula;
                    if(!$matricula AND $a->curp AND $grupo->cct){
                        $matricula = $this->genera_matricula($a->curp, $grupo->cct);
                    }
                    
                    if($matricula){
                        DB::table('alumnos_pre')->where('id', $a->id_pre)->where('matricula',null)->update(['matricula'=>$matricula]);
                        DB::table('alumnos_registro')->where('id_pre', $a->id_pre)->where('no_control',null)->where('folio_grupo',$folio_grupo)->update(['no_control'=>$matricula]);
                        
                        if($curp_anterior) $data_crit =  ['curp' =>  $curp_anterior, 'folio_grupo' =>  $grupo->folio_grupo];
                        else $data_crit = ['curp' =>  $a->curp, 'folio_grupo' =>  $grupo->folio_grupo];

                        $result = Inscripcion::updateOrCreate(                        
                        $data_crit,
                        [
                        'curp' => $a->curp,                        
                        'unidad' => $grupo->unidad,
                        'alumno' =>  $a->alumno,
                        'id_curso' =>  $grupo->id, 
                        'curso' =>  $grupo->curso,
                        'instructor' =>  $grupo->nombre,
                        'inicio' =>  $grupo->inicio,
                        'termino' =>  $grupo->termino,
                        'hinicio' =>  $grupo->hini,
                        'hfin' =>  $grupo->hfin,
                        'tinscripcion' =>  $tinscripcion,
                        'abrinscri' =>  $abrinscriTMP,
                        'munidad' =>  $grupo->munidad,
                        'costo' =>  $a->costo,
                        'motivo' =>  null,
                        'status' =>  'INSCRITO',
                        //'realizo' =>  $this->realizo,
                        'id_pre' =>  $a->id_pre,
                        'id_cerss' =>  $a->id_cerss,
                        'fecha_nacimiento' =>  $a->fecha_nacimiento,
                        'estado_civil' =>  $a->estado_civil,
                        'discapacidad' =>  $a->discapacidad,
                        'escolaridad' =>  $a->escolaridad,
                        'nacionalidad' =>  $a->nacionalidad,
                        'etnia' =>  $a->etnia,
                        'indigena' =>  $a->indigena,
                        'inmigrante' =>  $a->inmigrante,
                        'madre_soltera' =>  $a->madre_soltera,
                        'familia_migrante' =>  $a->familia_migrante,
                        //'calificacion' =>  $a->calificacion,
                        //'iduser_created' =>  $this->id_user,
                        'iduser_updated' =>  $this->id_user,
                        'activo' =>  true,
                        //'id_folio' =>  null,
                        'reexpedicion' =>  false,
                        'sexo'=> $a->sexo,
                        'lgbt' => $a->lgbt,                        
                        'empleado'=>$a->empleado,
                        'id_gvulnerable'=>$a->id_gvulnerable,
                        'requisitos'=>json_decode($a->requisitos),
                        'iduser_updated' => Auth::user()->id,
                        'updated_at' => date('Y-m-d H:i')                    
                        ]);                        
                        
                    }                    
                }
            }
        }
        return $result;
    }

    public function genera_matricula($curp, $cct){
        $matricula_sice = DB::table('registro_alumnos_sice')->where('eliminado',false)->where('curp',$curp)->value('no_control');
        $matricula = NULL;
        if(!$matricula_sice){
            $matricula_pre = DB::table('alumnos_pre')->where('curp',$curp)->value('matricula');
            if(!$matricula_pre){
                $anio = date('y');
                $clave = $anio.substr($cct,0,2).substr($cct,5,9);
                $max_sice = DB::table('registro_alumnos_sice')->where('eliminado',false)->where('no_control','like',$clave.'%')->max(DB::raw('no_control'));
                $max_pre = DB::table('alumnos_pre')->where('matricula','like',$clave.'%')->max('matricula');

                if($max_sice > $max_pre) $maX = $max_sice;
                elseif($max_sice < $max_pre) $max = $max_pre;
                else $max = '0';

                $max =  str_pad(intval(substr($max,9,13))+1, 4, "0", STR_PAD_LEFT);
                $matricula = $clave.$max;
            }else $matricula = $matricula_pre;
        }else{
            $matricula = $matricula_sice;
            DB::table('registro_alumnos_sice')->where('curp',$curp)->update(['eliminado'=>true]);
        }
        return $matricula;
    }
  
}
