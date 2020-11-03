<?php
// Elabor� Romelia P�rez Nang�el�
// rpnanguelu@gmail.com

namespace App\Http\Controllers\supervisionController\client;

use App\Http\Controllers\Controller;
use App\Models\supervision\Token;
use App\Models\supervision\alumno;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class frmAlumnoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $validationRules = [
        'file_photo' => ['required','mimes:jpeg,jpg,png','max:2048'],
        'nombre' => ['required', 'min:3'],
        'apellidoPaterno'=> ['required'],
        'apellidoMaterno'=> ['required'],
        'edad'=> ['required'],
        'escolaridad'=> ['required'],
        'fecha_inscripcion'=> ['required'],
        'documentos'=> ['required'],
        'curso'=> ['required'],        
        'fecha_autorizacion'=> ['required'],        
        'tipo'=> ['required'],
        'lugar'=> ['required'],
        'fecha_inicio'=> ['required'],
        'fecha_termino'=> ['required'],
        'hinicio'=> ['required'],
        'hfin'=> ['required'],
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
        'edad.required'=> 'Por favor ingrese su edad',
        'escolaridad.required'=> 'Por favor ingrese su escolaridad',
        'fecha_inscripcion.required'=> 'Por favor ingrese la fecha de inscripcion',
        'documentos.required'=>'Por favor ingrese que documentos proporciono',
        'curso.required'=>'Por favor ingrese el nombre del curso',        
        'fecha_autorizacion.required'=> 'Por favor ingrese la fecha de autorizacion',        
        'tipo.required'=> 'Por favor ingrese el tipo de curso',
        'lugar.required'=> 'Por favor ingrese el lugar',
        'cuota.required'=> 'Por favor ingrese la cuota de recuperacion pagada',
        'fecha_inicio.required'=> 'Por favor ingrese la fecha de inicio',
        'fecha_termino.required'=> 'Por favor ingrese la fecha de termino',
        'hinicio.required'=> 'Por favor ingrese la hora de inicio',
        'hfin.required'=> 'Por favor ingrese la hora de termino',
        'file_data.required' => 'Por favor suba las capturas de pantallas.',
        'file_data.*.mimes'=>'Acepta archivos jpg, jpge, png',
        'file_data.*.max'=>'Acepta archivos menores de 2MB'
    ];

    public function index(Request $request, $token)
    {

        $escolaridad = [
            'PRIMARIA INCONCLUSA' => 'PRIMARIA INCONCLUSA',
            'PRIMARIA TERMINADA' => 'PRIMARIA TERMINADA',
            'SECUNDARIA INCONCLUSA' => 'SECUNDARIA INCONCLUSA',
            'SECUNDARIA TERMINADA' => 'SECUNDARIA TERMINADA',
            'NIVEL MEDIO SUPERIOR INCONCLUSO' => 'NIVEL MEDIO SUPERIOR INCONCLUSO',
            'NIVEL MEDIO SUPERIOR TERMINADO' => 'NIVEL MEDIO SUPERIOR TERMINADO',
            'NIVEL SUPERIOR INCONCLUSO' => 'NIVEL SUPERIOR INCONCLUSO',
            'NIVEL SUPERIOR TERMINADO' => 'NIVEL SUPERIOR TERMINADO',
            'POSTGRADO' => 'POSTGRADO'
        ];
       return view('supervision.client.frmalumno',compact('escolaridad'));
    }

    public function guardar(Request $request, \Illuminate\Validation\Factory $validate)
    {
        $validator = $validate->make($request->all(), $this->validationRules,$this->validationMessages);
         if ($validator->fails()) {
                    $token = Token::where('tmp_token', $request->tmpToken)->first();
                    return redirect('/form/alumno/'.$token->url_token)
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
                    $id_alumno = $token->id_alumno;

                    $inscrip = DB::table('tbl_inscripcion')->where('id', $id_alumno)->first();
                    $curso = DB::table('tbl_cursos')->where('id', $inscrip->id_curso)->first();

                    $fields = ['ok_nombre','ok_edad','ok_escolaridad','ok_fecha_inscripcion',
                        'ok_documentos','ok_curso','ok_numero_apertura','ok_fecha_autorizacion',
                        'ok_modalidad','ok_fecha_inicio','ok_fecha_termino','ok_horario',
                        'ok_tipo','ok_lugar','ok_cuota'];

                    $alumno = new alumno();
                    $alumno->nombre = trim($request->nombre);
                    $alumno->apellido_paterno = trim($request->apellidoPaterno);
                    $alumno->apellido_materno = trim($request->apellidoMaterno);
                    $alumno->edad= $request->edad;
                    $alumno->escolaridad = trim($request->escolaridad);
                    $alumno->fecha_inscripcion = $request->fecha_inscripcion;
                    $alumno->documentos = trim($request->documentos);
                    $alumno->curso = trim($request->curso);
                    //$alumno->numero_apertura = trim($request->numero_apertura);
                    $alumno->fecha_autorizacion = $request->fecha_autorizacion;
                    //$alumno->modalidad = $request->modalidad;
                    $alumno->fecha_inicio = $request->fecha_inicio;
                    $alumno->fecha_termino = $request->fecha_termino;
                    $alumno->hinicio = trim($request->hinicio);
                    $alumno->hfin = trim($request->hfin);
                    $alumno->tipo = $request->tipo;
                    $alumno->lugar = trim($request->lugar);
                    $alumno->cuota = $request->cuota;
                    $alumno->id_tbl_cursos = $curso->id;
                    $alumno->id_curso = $curso->id_curso;
                    $alumno->id_alumno = $id_alumno;
                    $alumno->id_instructor = $curso->id_instructor;
                    $alumno->id_tbl_inscripcion = $inscrip->id;
                    $alumno->unidad = $curso->unidad;
                    $alumno->id_user = $id_supervisor;

                    foreach ($fields as $i => $value) {
                            $alumno->$value = true;
                    }
                    if($alumno->save()){

                       $id_supervision = $alumno->id;
                         /* CARGA FOTO DEL INSTRUCTOR*/
                        if ($request->hasFile('file_photo')) {
                            $ext = $request->file('file_photo')->getClientOriginalExtension();
                            $file_name =  $id_supervision."-".$fecha."-0.".$ext;
                            $request->file('file_photo')->storeAs('/supervisiones/'.$anio.'/alumnos', $file_name);
                        }
                        /*CARGA PANTALLAS DE CAPTURA*/
                        if ($request->hasFile('file_data')) {
                            $file_data = $request->file('file_data');
                            //var_dump($file_data);
                            $n=0;
                            foreach($file_data as $file){
                                $n++;
                                $ext = $file->getClientOriginalExtension();
                                $file_name =  $id_supervision."-".$fecha."-".$n.".".$ext;
                                $file->storeAs('/supervisiones/'.$anio.'/alumnos', $file_name);
                        	}
                        }

                        $token = $request->tmptoken;
                        $token->delete();

                        return redirect('/form/msg/1');
                    } else return redirect('/form/msg/0');
                } catch (Exception $e) {
                    return Redirect::back()->withErrors($e->getMessage());
                }
            }else   return redirect('/form/msg/0');

        }
    }

    public function msg($id)
    {
        if($id) $msg = " OPERACI&Oacute;N EXITOSA! ";
        else $msg = " OPERACI&Oacute;N INV&Aacute;LIDA ";
        return view('supervision.client.msg', compact('msg'));
    }
}
