<?php

namespace App\Http\Controllers\Preinscripcion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\tbl_grupos;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use App\Models\cat\catUnidades;
use App\Models\cat\catApertura;
use App\Models\Alumno;
use GuzzleHttp\Psr7\Message;
use Illuminate\Support\Facades\Storage;


class grupoController extends Controller
{
    use catUnidades;
    use catApertura;
    function __construct()
    {
        session_start();
        $this->ejercicio = date("y");
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->id_user = Auth::user()->id;
            $this->realizo = Auth::user()->name;
            $this->id_unidad = Auth::user()->unidad;
            $this->path_files = env("APP_URL").'/storage/uploadFiles';

            $this->data = $this->unidades_user('vincula');  //vincula
            $_SESSION['unidades'] =  $this->data['unidades'];

            return $next($request);
        });
    }

    public function index(Request $request)
    {

        $curso = $grupo = $cursos = $localidad  = $alumnos = [];
        $es_vulnerable = false;
        $unidades = $this->data['unidades'];
        $unidad = $this->data['unidad'];
        $message = $comprobante = NULL;
        if (isset($_SESSION['folio_grupo'])) {  //echo $_SESSION['folio_grupo'];exit;
            $anio_hoy = date('y');  //dd($_SESSION);
            $alumnos = DB::table('alumnos_registro as ar')->select(
                'ar.id as id_reg',
                'ar.no_control',
                'ar.turnado',
                'ap.nombre',
                'apellido_paterno',
                'apellido_materno',
                'ar.id_curso',
                'ar.mod',
                'ar.tipo_curso',
                'ar.id_cerss',
                'ar.horario',
                'ar.inicio',
                'ar.termino',
                'ar.costo',
                'id_muni',
                'ar.clave_localidad',
                'ar.organismo_publico',
                'ar.id_organismo',
                'ar.grupo_vulnerable',
                'ar.id_vulnerable',
                'ap.ultimo_grado_estudios',
                'ar.tinscripcion',
                'ar.unidad',
                'ar.folio_grupo',
                'ap.curp',
                'comprobante_pago',
                'ap.requisitos',
                'ap.documento_curp',
                'ap.id_gvulnerable',
                DB::raw("substring(curp,11,1) as sex"),
                DB::raw("CASE WHEN substring(curp,5,2) <='" . $anio_hoy . "'
                THEN CONCAT('20',substring(curp,5,2),'-',substring(curp,7,2),'-',substring(curp,9,2))
                ELSE CONCAT('19',substring(curp,5,2),'-',substring(curp,7,2),'-',substring(curp,9,2))
                END AS fnacimiento")
            )->join('alumnos_pre as ap', 'ap.id', 'ar.id_pre')->where('ar.folio_grupo', $_SESSION['folio_grupo'])->where('ar.eliminado', false)
            ->orderBy('apellido_paterno','ASC')->orderby('apellido_materno','ASC')->orderby('nombre','ASC')->get();
            //var_dump($alumnos);exit;
            if (count($alumnos) > 0) {
                foreach ($alumnos as $value) {
                    if ($value->id_gvulnerable != '[]') {
                        $es_vulnerable = true;
                    }
                }
                $id_curso = $alumnos[0]->id_curso;
                $tipo = $alumnos[0]->tipo_curso;
                if($alumnos[0]->comprobante_pago)$comprobante = $this->path_files.$alumnos[0]->comprobante_pago;
                if ($alumnos[0]->turnado == 'VINCULACION' and isset($this->data['cct_unidad'])) $this->activar = true;
                else $this->activar = false;

                if ($alumnos) $curso = DB::table('cursos')->where('id', $id_curso)->first();
                $clave = DB::table('tbl_municipios')->where('id', $alumnos[0]->id_muni)->value('clave');
                $localidad = DB::table('tbl_localidades')->where('clave_municipio', '=', $clave)->pluck('localidad', 'clave');
                $cursos = DB::table('cursos')
                    ->where('tipo_curso', $tipo)
                    ->where('cursos.estado', true)
                    ->where('modalidad',$alumnos[0]->mod)
                    ->whereJsonContains('unidades_disponible', [$unidad])->orderby('cursos.nombre_curso')->pluck('nombre_curso', 'cursos.id');
            } else {
                $message = "No hay registro qwue mostrar para Grupo No." . $_SESSION['folio_grupo'];
                $_SESSION['folio_grupo'] = NULL;
                $this->activar = true;
            }
        } else {
            $_SESSION['folio_grupo'] = NULL;
            $this->activar = true;
        }

        $cerss = DB::table('cerss');
        if ($unidad) $cerss = $cerss->where('id_unidad', $this->id_unidad)->where('activo', true);
        $cerss = $cerss->orderby('nombre', 'ASC')->pluck('nombre', 'id');
        $folio_grupo =  $_SESSION['folio_grupo'];
        $activar = $this->activar;
        $municipio = DB::table('tbl_municipios')->where('id_estado', '7')->orderby('muni')->pluck('muni', 'id');
        $dependencia = DB::table('organismos_publicos')
            ->where('activo', true)
            ->orderby('organismo')
            ->pluck('organismo', 'organismo');
        $grupo_vulnerable = DB::table('grupos_vulnerables')->orderBy('grupo')->pluck('grupo','id');
        if (session('message')) $message = session('message');
        $tinscripcion = $this->tinscripcion();
        return view('preinscripcion.index', compact('cursos', 'alumnos', 'unidades', 'cerss', 'unidad', 'folio_grupo', 'curso', 'activar',
                'es_vulnerable', 'message', 'tinscripcion', 'municipio', 'dependencia', 'localidad','grupo_vulnerable','comprobante'));
    }


    public function cmbcursos(Request $request)
    {
        //$request->unidad = 'TUXTLA';
        if (isset($request->tipo) and isset($request->unidad) and isset($request->modalidad)) {
            $cursos = DB::table('cursos')->select('cursos.id', 'nombre_curso')
                ->where('tipo_curso', $request->tipo)
                ->where('modalidad','like',"%$request->modalidad%")
                ->where('cursos.estado', true)
                ->whereJsonContains('unidades_disponible', [$request->unidad])->orderby('cursos.nombre_curso')->get();
            $json = json_encode($cursos);
            //var_dump($json);exit;
        } else {
            $json = json_encode(["No hay registros que mostrar."]);
        }

        return $json;
    }

    public function save(Request $request)
    {
        $curp = $request->busqueda;    //dd($request->all());
        $matricula = $message = NULL;
        if ($curp) {
            $date = date('d-m-Y'); //dd($date);
            $alumno = DB::table('alumnos_pre')->select('id as id_pre', 'matricula', DB::raw("cast(EXTRACT(year from(age('$date', fecha_nacimiento))) as integer) as edad"))->where('curp', $curp)->where('activo', true)->first(); //dd($alumno);
            if ($alumno) {
                if ($alumno->edad >= 15) {
                    $cursos = DB::table(DB::raw("(select a.id_curso as curso from alumnos_registro as a
													inner join alumnos_pre as ap on a.id_pre = ap.id
                                                    where ap.curp = '$curp'
                                                   	and a.eliminado = false
													and extract(year from a.inicio) = extract(year from current_date)) as t"))
                        ->select(DB::raw("count(curso) as total"), DB::raw("count(case when curso = '$request->id_curso' then curso end) as igual"))
                        ->first(); //dd($cursos);
                    if ($cursos->igual < 2 && $cursos->total < 15) {
                        if($_SESSION['folio_grupo'] AND DB::table('alumnos_registro')->where('folio_grupo',$_SESSION['folio_grupo'])->where('turnado','<>','VINCULACION')->exists() == true) $_SESSION['folio_grupo'] = NULL;
                        if(!$_SESSION['folio_grupo'] AND $alumno) $_SESSION['folio_grupo'] =$this->genera_folio();
                        //EXTRAER MATRICULA Y GUARDAR
                        $matricula_sice = DB::table('registro_alumnos_sice')->where('eliminado', false)->where('curp', $curp)->value('no_control');

                        if ($matricula_sice) {
                            $matricula = $matricula_sice;
                            DB::table('registro_alumnos_sice')->where('curp', $curp)->update(['eliminado' => true]);
                        } elseif (isset($alumno->matricula)) $matricula  =  $alumno->matricula;
                        //FIN MATRICULA

                        $a_reg = DB::table('alumnos_registro')->where('folio_grupo', $_SESSION['folio_grupo'])->first();
                        if ($a_reg) {
                            $id_especialidad = $a_reg->id_especialidad;
                            $id_unidad = $a_reg->id_unidad;
                            $unidad = $a_reg->unidad;
                            $id_curso = $a_reg->id_curso;
                            $horario = $a_reg->horario;
                            $inicio = $a_reg->inicio;
                            $termino = $a_reg->termino;
                            $tipo = $a_reg->tipo_curso;
                            $id_cerss = $a_reg->id_cerss;
                            $id_muni = $a_reg->id_muni;
                            $clave_localidad = $a_reg->clave_localidad;
                            $organismo = $a_reg->organismo_publico;
                            $id_organismo = $a_reg->id_organismo;
                            $grupo_vulnerable = $a_reg->grupo_vulnerable;
                            $id_vulnerable = $a_reg->id_vulnerable;
                            $comprobante_pago = $a_reg->comprobante_pago;
                            $modalidad = $a_reg->mod;
                        } else {
                            $id_especialidad = DB::table('cursos')->where('estado', true)->where('id', $request->id_curso)->value('id_especialidad');
                            $id_unidad = DB::table('tbl_unidades')->select('id', 'plantel')->where('unidad', $request->unidad)->value('id');
                            $unidad = $request->unidad;
                            $id_curso = $request->id_curso;
                            $horario= $request->hini.' A '.$request->hfin;
                            $inicio = $request->inicio;
                            $termino = $request->termino;
                            $tipo = $request->tipo;
                            $id_cerss = $request->cerss;
                            $id_muni = $request->id_municipio;
                            $clave_localidad = $request->localidad;
                            $organismo = $request->dependencia;
                            $id_organismo = DB::table('organismos_publicos')->where('organismo',$request->dependencia)->value('id');
                            $grupo_vulnerable = DB::table('grupos_vulnerables')->where('id',$request->grupo_vulnerable)->value('grupo');
                            $id_vulnerable = $request->grupo_vulnerable;
                            $comprobante_pago = null;
                            $modalidad = $request->modalidad;
                        }
                        if ($id_cerss) $cerrs = true;
                        else $cerrs = NULL;
                        if ($_SESSION['folio_grupo']) {
                            $result = DB::table('alumnos_registro')->UpdateOrInsert(
                                ['id_pre' => $alumno->id_pre, 'folio_grupo' => $_SESSION['folio_grupo']],
                                [
                                    'id_unidad' =>  $id_unidad, 'id_curso' => $id_curso, 'id_especialidad' =>  $id_especialidad, 'organismo_publico' => $organismo, 'id_organismo'=>$id_organismo,
                                    'horario'=>$horario, 'inicio' => $inicio, 'termino' => $termino, 'unidad' => $unidad, 'tipo_curso' => $tipo, 'clave_localidad' => $clave_localidad,
                                    'cct' => $this->data['cct_unidad'], 'realizo' => str_replace('ñ','Ñ',strtoupper($this->realizo)), 'no_control' => $matricula, 'ejercicio' => $this->ejercicio, 'id_muni' => $id_muni,
                                    'folio_grupo' => $_SESSION['folio_grupo'], 'iduser_created' => $this->id_user, 'comprobante_pago' => $comprobante_pago,
                                    'created_at' => date('Y-m-d H:i:s'), 'fecha' => date('Y-m-d'), 'id_cerss' => $id_cerss, 'cerrs' => $cerrs, 'mod' => $modalidad,
                                    'grupo' => $_SESSION['folio_grupo'], 'eliminado' => false, 'grupo_vulnerable' => $grupo_vulnerable, 'id_vulnerable' => $id_vulnerable
                                ]
                            );
                            if ($result) $message = "Operación Exitosa!!";
                        } else $message = "Operación no permitida!";
                    } else {
                        $message = "El alumno excede con el limte de cursos que puede tomar";
                    }
                } else {
                    $message = "La edad del alumno no es valida";
                }
            } else {
                $message = "Alumno no registrado " . $curp . ".";
            }
        } else $message = "Ingrese la CURP";
        //dd($_SESSION['folio_grupo']);
        return redirect()->route('preinscripcion.grupo')->with(['message' => $message]);
    }

    public function update(Request $request)
    {
        // dd($request->all());
        if ($_SESSION['folio_grupo']) {
            $folio = $_SESSION['folio_grupo'];
            $file =  $request->customFile;
            $url_comprobante = DB::table('alumnos_registro')->select('comprobante_pago')->where('folio_grupo',$folio)->first();
            if ($file) {
                $url_comprobante = $this->uploaded_file($file, $folio, 'comprobante_pago');
            }elseif ($url_comprobante->comprobante_pago != null) {
                $url_comprobante = $url_comprobante->comprobante_pago;
            }else {
                $url_comprobante = null;
            }
            $id_especialidad = DB::table('cursos')->where('estado', true)->where('id', $request->id_curso)->value('id_especialidad');
            $costo_individual = DB::table('cursos')->where('estado', true)->where('id', $request->id_curso)->value('costo');
            $id_unidad = DB::table('tbl_unidades')->select('id', 'plantel')->where('unidad', $request->unidad)->value('id');
            foreach ($request->costo as $key => $pago) {
                $cursos = DB::table(DB::raw("(select a.id_curso as curso from alumnos_registro as a
													inner join alumnos_pre as ap on a.id_pre = ap.id
                                                    where a.id = '$key'
													and a.folio_grupo != '$folio'
                                                   	and a.eliminado = false
													and extract(year from a.inicio) = extract(year from current_date)) as t"))
                        ->select(DB::raw("count(curso) as total"), DB::raw("count(case when curso = '$request->id_curso' then curso end) as igual"))
                        ->first(); //dd($cursos);
                        $curp = DB::table('alumnos_registro')->select('alumnos_pre.curp')->join('alumnos_pre','alumnos_registro.id_pre','=','alumnos_pre.id')->where('alumnos_registro.id',$key)->value('alumnos_pre.curp');
                if ($cursos->igual > 2 && $cursos->total > 6) {
                    $message = "Alumno excede el limite de cursos " . $curp . ".";
                    return redirect()->route('preinscripcion.grupo')->with(['message' => $message]);
                }
                $diferencia = $costo_individual - $pago;
                if ($pago == 0) {
                    $tinscripcion = "EXONERACION";
                    $abrins = 'ET';
                } elseif ($diferencia > 0) {
                    $tinscripcion = "REDUCCION DE CUOTA";
                    $abrins = 'EP';
                } else {
                    $tinscripcion = "PAGO ORDINARIO";
                    $abrins = 'PI';
                }
                Alumno::where('id', $key)->update(['costo' => $pago, 'tinscripcion' => $tinscripcion, 'abrinscri' => $abrins]);
            }
            if ($request->cerss) $cerrs = true;
            else $cerrs = NULL;
            $horario= $request->hini.' A '.$request->hfin;
            $id_organismo = DB::table('organismos_publicos')->where('organismo',$request->dependencia)->value('id');
            $grupo_vulnerable = DB::table('grupos_vulnerables')->where('id',$request->grupo_vulnerable)->value('grupo');
            $result = DB::table('alumnos_registro')->where('folio_grupo', $_SESSION['folio_grupo'])->Update(
                [
                    'id_unidad' =>  $id_unidad, 'id_curso' => $request->id_curso, 'clave_localidad' => $request->localidad, 'organismo_publico' => $request->dependencia,
                    'id_especialidad' =>  $id_especialidad, 'horario'=>$horario, 'unidad' => $request->unidad, 'tipo_curso' => $request->tipo, 'mod'=>$request->modalidad,
                    'iduser_updated' => $this->id_user, 'updated_at' => date('Y-m-d H:i:s'), 'fecha' => date('Y-m-d'), 'id_muni' => $request->id_municipio,
                    'inicio' => $request->inicio, 'termino' => $request->termino, 'id_organismo'=>$id_organismo, 'id_vulnerable' => $request->grupo_vulnerable,
                    'id_cerss' => $request->cerss, 'cerrs' => $cerrs, 'id_muni' => $request->id_municipio, 'grupo_vulnerable' => $grupo_vulnerable, 'comprobante_pago'=>$url_comprobante
                ]
            );
            if ($result) $message = "Operación Exitosa!!";
            //Si hay cambios y esta registrado en tbl_cursos se elimina el instructor para validarlo nuevamente
            DB::table('tbl_cursos')->where('folio_grupo', $_SESSION['folio_grupo'])->where('clave', '0')->update(['nombre' => null, 'curp' => null, 'rfc' => null]);
        } else $message = "La acción no se ejecuto correctamente";
        return redirect()->route('preinscripcion.grupo')->with(['message' => $message]);
    }

    public function genera_folio()
    {
        $consec = DB::table('alumnos_registro')->where('ejercicio', $this->ejercicio)->where('cct', $this->data['cct_unidad'])->where('eliminado', false)->value(DB::RAW('max(cast(substring(folio_grupo,7,4) as int))')) + 1;
        $consec = str_pad($consec, 4, "0", STR_PAD_LEFT);
        $folio = $this->data['cct_unidad'] . "-" . $this->ejercicio . $consec;

        return $folio;
    }

    public function nuevo()
    {
        $_SESSION['folio_grupo'] = NULL;
        return redirect()->route('preinscripcion.grupo');
    }
    public function turnar()
    {
        if ($_SESSION['folio_grupo']) {
            $alumnos = DB::table('alumnos_registro')->where('folio_grupo',$_SESSION['folio_grupo'])->get();
            $comprobante = DB::table('alumnos_registro')->select('comprobante_pago')->where('folio_grupo', $_SESSION['folio_grupo'])->first();
            $costo = 0;
            $conteo = 0;
            foreach ($alumnos as $a) {
                $costo += $a->costo;
                if ($a->costo) {
                    $conteo += 1;
                }
            }
            if ($costo > 0) {
                if ($comprobante->comprobante_pago) {
                    //echo "pasa"; exit;
                   $result = DB::table('alumnos_registro')->where('folio_grupo', $_SESSION['folio_grupo'])->update(['turnado' => 'UNIDAD', 'fecha_turnado' => date('Y-m-d')]);
                    //$_SESSION['folio_grupo']=NULL;
                }else {
                    return redirect()->route('preinscripcion.grupo')->with(['message' => 'FAVOR DE CARGAR EL COMPROBANTE DE PAGO']);
                }
            } elseif ($conteo < count($alumnos)) {
                return redirect()->route('preinscripcion.grupo')->with(['message' => 'FAVOR DE AGREGAR LOS COSTOS A LOS ALUMNOS']);
            } else {
                $result = DB::table('alumnos_registro')->where('folio_grupo', $_SESSION['folio_grupo'])->update(['turnado' => 'UNIDAD', 'fecha_turnado' => date('Y-m-d'), 'comprobante_pago' => null]);
            }
        }
        return redirect()->route('preinscripcion.grupo');
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        if ($id) {
            //$result = DB::table('alumnos_registro')->where('folio_grupo', $_SESSION['folio_grupo'])->where('id',$id)->update(['eliminado'=>true,'iduser_updated'=>$this->id_user]);
            $result = DB::table('alumnos_registro')->where('folio_grupo', $_SESSION['folio_grupo'])->where('id', $id)->delete();
        } else $result = false;
        //echo $result; exit;
        return $result;
    }

    public function subir_comprobante(Request $request)
    {
        $file =  $request->customFile;  //dd($file);
        $id = $_SESSION['folio_grupo'];
        if ($file) {
            $url_comprobante = $this->uploaded_file($file, $id, 'comprobante_pago');
            $opss = DB::table('alumnos_registro')->where('folio_grupo', $id)->update(['comprobante_pago' => $url_comprobante]);
            $message = "Operación Exitosa!!";
        } else {
            $message = 'El documento no fue cargado correctamente';
        }
        return redirect()->route('preinscripcion.grupo')->with(['message' => $message]);
    }

    public function uploaded_file($file, $id, $name)
    {
        $tamanio = $file->getSize(); #obtener el tamaño del archivo del cliente
        $extensionFile = $file->getClientOriginalExtension(); // extension de la imagen
        # nuevo nombre del archivo
        $documentFile = trim($name . "_" . $id . "_" . date('YmdHis') . "." . $extensionFile);
        $path_pdf = "/UNIDAD/comprobantes_pagos/";
        $path = $path_pdf . $documentFile;
        Storage::disk('custom_folder_1')->put($path, file_get_contents($file)); // guardamos el archivo en la carpeta storage
        //$documentUrl = storage::url($path); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        $documentUrl = $path;
        return $documentUrl;
    }

    public function showlm(Request $request)
    {
        if ($request->ajax()) {
            $clave = DB::table('tbl_municipios')
                ->select('id_estado', 'clave')
                ->where('id', $request->estado_id)
                ->first();
            $localidadArray = DB::table('tbl_localidades')->select('localidad', 'clave')
                ->where('id_estado', $clave->id_estado)
                ->where('clave_municipio', '=', $clave->clave)
                ->orderBy('localidad')->get();
            return response()->json($localidadArray);
        }
    }
}
