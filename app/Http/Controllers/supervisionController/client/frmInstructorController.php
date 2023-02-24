<?php
// Elabor� Romelia P�rez Nang�el�
// rpnanguelu@gmail.com

namespace App\Http\Controllers\supervisionController\client;

use App\Http\Controllers\Controller;
use App\Models\supervision\Token;
use App\Models\supervision\instructor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class frmInstructorController extends Controller
{
    //use RegisterTrait;
    private $validationRules = [
        'file_photo' => ['required','mimes:jpeg,jpg,png','max:2048'],
        'nombre' => ['required', 'min:3'],
        'apellidoPaterno'=> ['required'],
        'apellidoMaterno'=> ['required'],
        'fecha_contrato'=> ['required'],
        'fecha_padron'=> ['required'],
        'monto_honorarios'=> ['required'],
        'nombre_curso'=> ['required'],        
        'fecha_autorizacion'=> ['required'],
        'modalidad'=> ['required'],
        'inicio_curso'=> ['required'],
        'termino_curso'=> ['required'],
        'total_mujeres'=> ['required'],
        'total_hombres'=> ['required'],
        'hini_curso'=> ['required'],
        'hfin_curso'=> ['required'],
        'horas_diarias' => ['required'],
        'horas_curso'=> ['required'],
        'tipo_curso'=> ['required'],
        'lugar_curso'=> ['required'],
        'file_data' => ['required'],
        'file_data.*' => ['mimes:jpeg,jpg,png','max:2048']

    ];
    private $validationMessages = [
        'file_photo.required' => 'Por favor suba su foto.',
        'file_photo.mimes'=>'Acepta archivos jpg, jpge, png',
        'file_photo.max'=>'Acepta archivos menores de 2MB',
        'nombre.required' => 'Por favor ingrese su Nombre.',
        'apellidoPaterno.required'=> 'Por favor ingrese su Apellido Paterno',
        'apellidoMaterno.required'=> 'Por favor ingrese su Apellido Materno',
        'fecha_contrato.required'=> 'Por favor ingrese la fecha del contrato',
        'fecha_padron.required'=> 'Por favor ingrese la fecha de inscripcion al padron',
        'monto_honorarios.required'=> 'Por favor ingrese el monto de honorarios',
        'nombre_curso.required'=>'Por favor ingrese el nombre del curso',        
        'fecha_autorizacion.required'=> 'Por favor ingrese la fecha de autorizacion',
        'modalidad.required'=> 'Por favor ingrese el proposito del curso' ,
        'inicio_curso.required'=> 'Por favor ingrese la fecha de inicio',
        'termino_curso.required'=> 'Por favor ingrese la fecha de termino',
        'total_mujeres.required'=> 'Por favor ingrese el total de mujeres',
        'total_hombres.required'=> 'Por favor ingrese el total de hombres',
        'hini_curso.required'=> 'Por favor ingrese la hora de inicio',
        'hfin_curso.required'=> 'Por favor ingrese la hora de termino',
        'horas_diarias.required'=> 'Por favor ingrese las horas diarias',
        'horas_curso.required'=> 'Por favor ingrese el total de horas',
        'tipo_curso.required'=> 'Por favor ingrese el tipo de curso',
        'lugar_curso.required'=> 'Por favor ingrese el lugar',
        'file_data.required' => 'Por favor suba las capturas de pantallas.',
        'file_data.*.mimes'=>'Acepta archivos jpg, jpge, png',
        'file_data.*.max'=>'Acepta archivos menores de 2MB'

    ];

    public function guardar(Request $request, \Illuminate\Validation\Factory $validate )
    {
         $validator = $validate->make($request->all(), $this->validationRules,$this->validationMessages);
         if ($validator->fails()) {
                    $token = Token::where('tmp_token', $request->tmpToken)->first();
                    return redirect('/form/instructor/'.$token->url_token)
                        ->withErrors($validator)
                        ->withInput();
         }else{
            if ($request->session()->has('tmpToken')) {
                $request->session()->forget('tmpToken');
                try {
                    $anio = date("Y");
                    $fecha = date("dmy");
                    $token = Token::where('tmp_token', $request->tmpToken)->first();
                    $id_curso = $token->id_curso;
                    $id_supervisor = $token->id_supervisor;
                    $curso = DB::table('tbl_cursos')->where('id', $id_curso )->first();
                    $fields = ['ok_nombre','ok_fecha_contrato','ok_fecha_padron',
                        'ok_honorarios','ok_curso','ok_modalidad',
                        'ok_horario','ok_horas_diarias','ok_horas_curso',
                        'ok_fecha_inicio','ok_fecha_termino','ok_mujeres','ok_hombres',
                        'ok_tipo','ok_lugar','ok_numero_apertura','ok_fecha_autorizacion'];

                    $instructor = new instructor();
                    $instructor->nombre = trim($request->nombre);
                    $instructor->apellido_paterno = trim($request->apellidoPaterno);
                    $instructor->apellido_materno = trim($request->apellidoMaterno);
                    $instructor->fecha_contrato = $request->fecha_contrato;
                    $instructor->fecha_padron = $request->fecha_padron;
                    $instructor->monto_honorarios = $request->monto_honorarios;
                    $instructor->nombre_curso = trim($request->nombre_curso);
                   // $instructor->numero_apertura = trim($request->numero_apertura);
                    $instructor->fecha_autorizacion = $request->fecha_autorizacion;
                    $instructor->horas_curso = trim($request->horas_curso);
                    $instructor->hini_curso = trim($request->hini_curso);
                    $instructor->hfin_curso = trim($request->hfin_curso);
                    $instructor->horas_diarias = trim($request->horas_diarias);
                    $instructor->modalidad_curso = $request->modalidad;
                    $instructor->inicio_curso = $request->inicio_curso;
                    $instructor->termino_curso = $request->termino_curso;
                    $instructor->tipo_curso = $request->tipo_curso;
                    $instructor->lugar_curso = trim($request->lugar_curso);
                    $instructor->total_mujeres = $request->total_mujeres;
                    $instructor->total_hombres = $request->total_hombres;
                    $instructor->id_tbl_cursos = $id_curso;
                    $instructor->cct = $curso->cct;
                    $instructor->id_instructor = $curso->id_instructor;
                    $instructor->id_curso = $curso->id_curso;
                    $instructor->unidad = $curso->unidad;
                    $instructor->id_user = $id_supervisor;
                    foreach ($fields as $i => $value) {
                            $instructor->$value = true;
                    }

                    if($instructor->save()){
                       /* CARGA FOTO DEL INSTRUCTOR*/
                       $id_supervision = $instructor->id;
                        if ($request->hasFile('file_photo')) {
                            $ext = $request->file('file_photo')->getClientOriginalExtension();
                            $file_name =  $id_supervision."-".$fecha."-0.".$ext;
                            $request->file('file_photo')->storeAs('/supervisiones/'.$anio.'/instructores', $file_name);
                        }
                        /*CARGA PANTALLAS DE CAPTURA*/
                        if ($request->hasFile('file_data')) {
                            $file_data = $request->file('file_data');
                            $n=0;
                            foreach($file_data as $file){
                                $n++;
                                $ext = $file->getClientOriginalExtension();
                                $file_name =  $id_supervision."-".$fecha."-".$n.".".$ext;
                                $file->storeAs('/supervisiones/'.$anio.'/instructores', $file_name);
                        	}
                        }

                        /**ELIMINANDO TOKEN**/
                        $token = $request->tmptoken;
                        $token->delete();

                        return redirect('/form/msg/1');
                    } else return redirect('/form/msg/0');

                } catch (Exception $e) {
                    return Redirect::back()->withErrors($e->getMessage());
                }

            }else   return redirect('/form/msg/0');
        }//FIN DE VALIDATOR
    }

    public function msg($id)
    {
        if($id) $msg = " OPERACI&Oacute;N EXITOSA! ";
        else $msg = " OPERACI&Oacute;N INV&Aacute;LIDA ";
        return view('supervision.client.msg', compact('msg'));
    }
}
