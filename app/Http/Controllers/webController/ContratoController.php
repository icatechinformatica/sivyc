<?php
//Creado por Orlando Chavez
namespace App\Http\Controllers\WebController;

use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\contratos;
use App\Models\InstructorPerfil;
use App\Models\supre;
use App\Models\folio;
use App\Models\pago;
use App\Models\especialidad_instructor;
use App\Models\directorio;
use App\Models\tbl_curso;
use App\Models\contrato_directorio;
use App\Models\especialidad;
use App\Models\instructor;
use PDF;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\tbl_unidades;
use Illuminate\Pagination\Paginator;

class ContratoController extends Controller
{
    public function prueba()
    {
        $hola = instructor::SELECT('instructores.numero_control','instructores.nombre','instructores.apellidoPaterno',
                           'instructores.apellido Materno', 'especialidades.nombre', 'especialidades.id')
                           ->LEFTJOIN('instructor_perfil', 'instructor_perfil.numero_control', '=','instructores.id')
                           ->LEFTJOIN('especialidad_instructores', 'especialidad_instructores.perfilprof_id','=','instructor_perfil.id' )
                           ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'especialidad_instructores.especialidad_id')->GET();
                           dd($hola);
    }
    public function index(Request $request)
    {
        /**
         * parametros para iniciar la busqueda
         */
        $tipoContrato = $request->get('tipo_contrato');
        $busqueda_contrato = $request->get('busquedaPorContrato');
        $tipoStatus = $request->get('tipo_status');
        // obtener el usuario y su unidad
        $usuarioUnidad = Auth::user()->unidad;
        // obtener el id
        $userId = Auth::user()->id;

        $roles = DB::table('role_user')
            ->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')
            ->SELECT('roles.slug AS role_name')
            ->WHERE('role_user.user_id', '=', $userId)
            ->GET();
            //hola

        $contratos = new contratos();

        //dd($roles[0]->role_name);

        switch ($roles[0]->role_name) {
            case 'admin':
                # code...
                $querySupre = $contratos::busquedaporcontrato($tipoContrato, $busqueda_contrato, $tipoStatus)
                                ->WHERE('folios.status', '!=', 'En_Proceso')
                                ->WHERE('folios.status', '!=', 'Finalizado')
                                ->WHERE('folios.status', '!=', 'Pago_Verificado')
                                ->WHERE('folios.status', '!=', 'Rechazado')
                                ->RIGHTJOIN('folios', 'contratos.id_folios', '=', 'folios.id_folios')
                                ->RIGHTJOIN('tbl_cursos', 'folios.id_cursos', '=', 'tbl_cursos.id')
                                ->RIGHTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                                ->RIGHTJOIN('tabla_supre', 'tabla_supre.id', '=', 'folios.id_supre')
                                ->orderBy('tabla_supre.fecha', 'desc')
                                ->PAGINATE(25, [
                                    'tabla_supre.id','tabla_supre.no_memo',
                                    'tabla_supre.unidad_capacitacion', 'tabla_supre.fecha','folios.status',
                                    'folios.id_folios', 'folios.folio_validacion', 'tbl_unidades.ubicacion',
                                    'contratos.docs','contratos.id_contrato','contratos.fecha_status',
                                    'tbl_cursos.termino AS fecha_termino',
                                    'tbl_cursos.inicio AS fecha_inicio',
                                    DB::raw("(DATE_PART('day', CURRENT_DATE::timestamp - termino::timestamp)) fecha_dif")
                                ]);
            break;
            case 'unidad.ejecutiva':
                # code...
                $querySupre = $contratos::busquedaporcontrato($tipoContrato, $busqueda_contrato)
                                ->WHERE('folios.status', '!=', 'En_Proceso')
                                ->WHERE('folios.status', '!=', 'Finalizado')
                                ->WHERE('folios.status', '!=', 'Pago_Verificado')
                                ->WHERE('folios.status', '!=', 'Rechazado')
                                ->RIGHTJOIN('folios', 'contratos.id_folios', '=', 'folios.id_folios')
                                ->RIGHTJOIN('tbl_cursos', 'folios.id_cursos', '=', 'tbl_cursos.id')
                                ->RIGHTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                                ->RIGHTJOIN('tabla_supre', 'tabla_supre.id', '=', 'folios.id_supre')
                                ->orderBy('tabla_supre.fecha', 'desc')
                                ->PAGINATE(25, [
                                    'tabla_supre.id','tabla_supre.no_memo',
                                    'tabla_supre.unidad_capacitacion', 'tabla_supre.fecha','folios.status',
                                    'folios.id_folios', 'folios.folio_validacion', 'tbl_unidades.ubicacion',
                                    'contratos.docs','contratos.id_contrato','contratos.fecha_status',
                                    'tbl_cursos.termino AS fecha_termino',
                                    'tbl_cursos.inicio AS fecha_inicio',
                                    DB::raw("(DATE_PART('day', CURRENT_DATE::timestamp - termino::timestamp)) fecha_dif")
                                ]);
            break;
            case 'direccion.general':
                # code...
                $querySupre = $contratos::busquedaporcontrato($tipoContrato, $busqueda_contrato, $tipoStatus)
                                 ->WHERE('folios.status', '!=', 'En_Proceso')
                                 ->WHERE('folios.status', '!=', 'Finalizado')
                                 ->WHERE('folios.status', '!=', 'Pago_Verificado')
                                 ->WHERE('folios.status', '!=', 'Rechazado')
                                ->RIGHTJOIN('folios', 'contratos.id_folios', '=', 'folios.id_folios')
                                ->RIGHTJOIN('tbl_cursos', 'folios.id_cursos', '=', 'tbl_cursos.id')
                                ->RIGHTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                                ->RIGHTJOIN('tabla_supre', 'tabla_supre.id', '=', 'folios.id_supre')
                                ->orderBy('tabla_supre.fecha', 'desc')
                                ->PAGINATE(25, [
                                    'tabla_supre.id','tabla_supre.no_memo',
                                    'tabla_supre.unidad_capacitacion', 'tabla_supre.fecha','folios.status',
                                    'folios.id_folios', 'folios.folio_validacion', 'tbl_unidades.ubicacion',
                                    'contratos.docs','contratos.id_contrato','contratos.fecha_status',
                                    'tbl_cursos.termino AS fecha_termino',
                                    'tbl_cursos.inicio AS fecha_inicio',
                                    DB::raw("(DATE_PART('day', CURRENT_DATE::timestamp - termino::timestamp)) fecha_dif")
                                ]);
            break;
            case 'planeacion':
                # code...
                $querySupre = $contratos::busquedaporcontrato($tipoContrato, $busqueda_contrato, $tipoStatus)
                                ->WHERE('folios.status', '!=', 'En_Proceso')
                                ->WHERE('folios.status', '!=', 'Finalizado')
                                ->WHERE('folios.status', '!=', 'Pago_Verificado')
                                ->WHERE('folios.status', '!=', 'Rechazado')
                                ->RIGHTJOIN('folios', 'contratos.id_folios', '=', 'folios.id_folios')
                                ->RIGHTJOIN('tbl_cursos', 'folios.id_cursos', '=', 'tbl_cursos.id')
                                ->RIGHTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                                ->RIGHTJOIN('tabla_supre', 'tabla_supre.id', '=', 'folios.id_supre')
                                ->orderBy('tabla_supre.fecha', 'desc')
                                ->PAGINATE(25, [
                                    'tabla_supre.id','tabla_supre.no_memo',
                                    'tabla_supre.unidad_capacitacion', 'tabla_supre.fecha','folios.status',
                                    'folios.id_folios', 'folios.folio_validacion', 'tbl_unidades.ubicacion',
                                    'contratos.docs','contratos.id_contrato','contratos.fecha_status',
                                    'tbl_cursos.termino AS fecha_termino',
                                    'tbl_cursos.inicio AS fecha_inicio',
                                    DB::raw("(DATE_PART('day', CURRENT_DATE::timestamp - termino::timestamp)) fecha_dif")
                                ]);
            break;
            case 'financiero_verificador':
                # code...
                $querySupre = $contratos::busquedaporcontrato($tipoContrato, $busqueda_contrato, $tipoStatus)
                                ->WHERE('folios.status', '!=', 'En_Proceso')
                                ->WHERE('folios.status', '!=', 'Finalizado')
                                ->WHERE('folios.status', '!=', 'Pago_Verificado')
                                ->WHERE('folios.status', '!=', 'Rechazado')
                                ->RIGHTJOIN('folios', 'contratos.id_folios', '=', 'folios.id_folios')
                                ->RIGHTJOIN('tbl_cursos', 'folios.id_cursos', '=', 'tbl_cursos.id')
                                ->RIGHTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                                ->RIGHTJOIN('tabla_supre', 'tabla_supre.id', '=', 'folios.id_supre')
                                ->orderBy('tabla_supre.fecha', 'desc')
                                ->PAGINATE(25, [
                                    'tabla_supre.id','tabla_supre.no_memo',
                                    'tabla_supre.unidad_capacitacion', 'tabla_supre.fecha','folios.status',
                                    'folios.id_folios', 'folios.folio_validacion', 'tbl_unidades.ubicacion',
                                    'contratos.docs','contratos.id_contrato','contratos.fecha_status',
                                    'tbl_cursos.termino AS fecha_termino',
                                    'tbl_cursos.inicio AS fecha_inicio',
                                    DB::raw("(DATE_PART('day', CURRENT_DATE::timestamp - termino::timestamp)) fecha_dif")
                                ]);
            break;
            case 'financiero_pago':
                # code...
                $querySupre = $contratos::busquedaporcontrato($tipoContrato, $busqueda_contrato, $tipoStatus)
                                ->WHERE('folios.status', '!=', 'En_Proceso')
                                ->WHERE('folios.status', '!=', 'Finalizado')
                                ->WHERE('folios.status', '!=', 'Pago_Verificado')
                                ->WHERE('folios.status', '!=', 'Rechazado')
                                ->RIGHTJOIN('folios', 'contratos.id_folios', '=', 'folios.id_folios')
                                ->RIGHTJOIN('tbl_cursos', 'folios.id_cursos', '=', 'tbl_cursos.id')
                                ->RIGHTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                                ->RIGHTJOIN('tabla_supre', 'tabla_supre.id', '=', 'folios.id_supre')
                                ->orderBy('tabla_supre.fecha', 'desc')
                                ->PAGINATE(25, [
                                    'tabla_supre.id','tabla_supre.no_memo',
                                    'tabla_supre.unidad_capacitacion', 'tabla_supre.fecha','folios.status',
                                    'folios.id_folios', 'folios.folio_validacion', 'tbl_unidades.ubicacion',
                                    'contratos.docs','contratos.id_contrato','contratos.fecha_status',
                                    'tbl_cursos.termino AS fecha_termino',
                                    'tbl_cursos.inicio AS fecha_inicio',
                                    DB::raw("(DATE_PART('day', CURRENT_DATE::timestamp - termino::timestamp)) fecha_dif")
                                ]);
            break;
            default:
                # code...
                // obtener unidades
                $unidadUsuario = DB::table('tbl_unidades')->WHERE('id', $usuarioUnidad)->FIRST();
                /**
                 * contratos - contratos
                 */
                $contratos = new contratos();

                $querySupre = $contratos::busquedaporcontrato($tipoContrato, $busqueda_contrato, $tipoStatus)
                                ->WHERE('tbl_unidades.ubicacion', '=', $unidadUsuario->ubicacion)
                                ->WHERE('folios.status', '!=', 'En_Proceso')
                                ->WHERE('folios.status', '!=', 'Finalizado')
                                ->WHERE('folios.status', '!=', 'Pago_Verificado')
                                ->WHERE('folios.status', '!=', 'Rechazado')
                                ->RIGHTJOIN('folios', 'contratos.id_folios', '=', 'folios.id_folios')
                                ->RIGHTJOIN('tbl_cursos', 'folios.id_cursos', '=', 'tbl_cursos.id')
                                ->RIGHTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                                ->RIGHTJOIN('tabla_supre', 'tabla_supre.id', '=', 'folios.id_supre')
                                ->orderBy('tabla_supre.fecha', 'desc')
                                ->PAGINATE(25, [
                                    'tabla_supre.id','tabla_supre.no_memo',
                                    'tabla_supre.unidad_capacitacion', 'tabla_supre.fecha','folios.status',
                                    'folios.id_folios', 'folios.folio_validacion', 'tbl_unidades.ubicacion',
                                    'contratos.docs','contratos.id_contrato','contratos.fecha_status',
                                    'tbl_cursos.termino AS fecha_termino',
                                    'tbl_cursos.inicio AS fecha_inicio',
                                    DB::raw("(DATE_PART('day', CURRENT_DATE::timestamp - termino::timestamp)) fecha_dif")
                                ]);
            break;
        }

        return view('layouts.pages.vstacontratoini', compact('querySupre'));
    }

    /**
     * Show the form for creating a new resourc.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $folio = new folio();
        $perfil = new InstructorPerfil();
        $data = $folio::SELECT('folios.id_folios','folios.importe_total','folios.iva','tbl_cursos.clave','tbl_cursos.termino','tbl_cursos.curso','instructores.nombre AS insnom','instructores.apellidoPaterno',
                               'instructores.apellidoMaterno','instructores.id')
                        ->WHERE('id_folios', '=', $id)
                        ->LEFTJOIN('tbl_cursos','tbl_cursos.id', '=', 'folios.id_cursos')
                        ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                        ->FIRST();

        $perfil_prof = $perfil::SELECT('especialidades.nombre AS nombre_especialidad', 'especialidad_instructores.id AS id_espins')
                                ->WHERE('instructor_perfil.numero_control', '=', $data->id)
                                ->LEFTJOIN('especialidad_instructores','especialidad_instructores.perfilprof_id', '=', 'instructor_perfil.id')
                                ->LEFTJOIN('especialidades','especialidades.id','=','especialidad_instructores.especialidad_id')->GET();

        $nombrecompleto = $data->insnom . ' ' . $data->apellidoPaterno . ' ' . $data->apellidoMaterno;
        $pago = round($data->importe_total-$data->iva, 2);

        $date = strtotime($data->termino);
        $dacarbon = strtotime(Carbon::now());

        if($dacarbon > $date)
        {
            $term = TRUE;
        }
        else
        {
            $term = FALSE;
        }

        return view('layouts.pages.frmcontrato', compact('data','nombrecompleto','perfil_prof','pago','term'));
    }

    public function contrato_save(Request $request)
    {
        $contrato = new contratos();
        $contrato->numero_contrato = $request->numero_contrato;
        $contrato->instructor_perfilid = $request->perfil_instructor;
        $contrato->cantidad_letras1 = $request->cantidad_letras;
        $contrato->cantidad_numero = $request->cantidad_numero;
        $contrato->municipio = $request->lugar_expedicion;
        $contrato->fecha_firma = $request->fecha_firma;
        $contrato->unidad_capacitacion = $request->unidad_capacitacion;
        $contrato->id_folios = $request->id_folio;
        $contrato->fecha_status = carbon::now();
        $file = $request->file('factura'); # obtenemos el archivo
        if ($file != NULL)
        {
            $urldocs = $this->pdf_upload($file, $request->id_contrato,'factura');
            $contrato->arch_factura = $urldocs;
        }
        $contrato->save();

        $id_contrato = contratos::SELECT('id_contrato')->WHERE('numero_contrato', '=', $request->numero_contrato)->FIRST();
        $directorio = new contrato_directorio();
        $directorio->contrato_iddirector = $request->id_director;
        $directorio->contrato_idtestigo1 = $request->id_testigo1;
        $directorio->contrato_idtestigo2 = $request->id_testigo2;
        $directorio->contrato_idtestigo3 = $request->id_testigo3;
        $directorio->id_contrato = $id_contrato->id_contrato;
        $directorio->save();

        folio::where('id_folios', '=', $request->id_folio)
        ->update(['status' => 'Validando_Contrato']);

        $idc = $id_contrato->id_contrato;

        return view('layouts.pages.contratocheck', compact('idc'));
    }

    public function modificar($id)
    {
        $folio = new folio();
        $especialidad = new especialidad();
        $perfil = new InstructorPerfil();

        $datacon = contratos::WHERE('id_contrato', '=', $id)->FIRST();
        $data = $folio::SELECT('folios.id_folios','folios.iva','tbl_cursos.clave','tbl_cursos.nombre','instructores.nombre AS insnom','instructores.apellidoPaterno',
                               'instructores.apellidoMaterno','instructores.id', 'tbl_cursos.curso')
                        ->WHERE('id_folios', '=', $datacon->id_folios)
                        ->LEFTJOIN('tbl_cursos','tbl_cursos.id', '=', 'folios.id_cursos')
                        ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                        ->FIRST();

        $perfil_sel = contratos::SELECT('especialidades.nombre AS nombre_especialidad', 'contratos.instructor_perfilid AS id_espins')
                                ->WHERE('id_contrato', '=', $id)
                                ->LEFTJOIN('especialidad_instructores','especialidad_instructores.id','=','contratos.instructor_perfilid')
                                ->LEFTJOIN('especialidades','especialidades.id','=','especialidad_instructores.especialidad_id')
                                ->FIRST();


        $insPer = especialidad_instructor::SELECT('perfilprof_id')->WHERE('id', '=', $datacon->instructor_perfilid)->FIRST();

        $perfil_prof = $perfil::SELECT('especialidades.nombre AS nombre_especialidad', 'especialidad_instructores.id AS id_espins')
                                ->WHERE('instructor_perfil.numero_control', '=', $data->id)
                                ->WHERE('especialidad_instructores.id', '!=', $perfil_sel->id_espins)
                                ->LEFTJOIN('especialidad_instructores','especialidad_instructores.perfilprof_id', '=', 'instructor_perfil.id')
                                ->LEFTJOIN('especialidades','especialidades.id','=','especialidad_instructores.especialidad_id')->GET();

        $data_directorio = contrato_directorio::WHERE('id_contrato', '=', $id)->FIRST();
        $director = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','id')->WHERE('id', '=', $data_directorio->contrato_iddirector)->FIRST();
        $testigo1 = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $data_directorio->contrato_idtestigo1)->FIRST();
        $testigo2 = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $data_directorio->contrato_idtestigo2)->FIRST();
        $testigo3 = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $data_directorio->contrato_idtestigo3)->FIRST();

        $nombrecompleto = $data->insnom . ' ' . $data->apellidoPaterno . ' ' . $data->apellidoMaterno;
        return view('layouts.pages.modcontrato', compact('data','nombrecompleto','perfil_prof','perfil_sel','datacon','director','testigo1','testigo2','testigo3','data_directorio'));
    }

    public function save_mod(Request $request){
        $contrato = contratos::find($request->id_contrato);
        $contrato->numero_contrato = $request->numero_contrato;
        if($request->perfilinstructor != NULL)
        {
            $contrato->instructor_perfilid = $request->perfilinstructor;
        }
        $contrato->cantidad_numero = $request->cantidad_numero;
        $contrato->cantidad_letras1 = $request->cantidad_letras;
        $contrato->municipio = $request->lugar_expedicion;
        $contrato->fecha_firma = $request->fecha_firma;
        $contrato->unidad_capacitacion = $request->unidad_capacitacion;
        $contrato->fecha_status = carbon::now();

        if($request->factura != NULL)
        {
            $file = $request->file('factura'); # obtenemos el archivo
            $urldocs = $this->pdf_upload($file, $request->id_contrato,'factura');
            $contrato->arch_factura = $urldocs;
        }

        $contrato->save();

        $folio = folio::find($request->id_folio);
        $folio->status = 'Validando_Contrato';
        $folio->save();


        $directorio = contrato_directorio::find($request->id_directorio);
        $directorio->contrato_iddirector = $request->id_director;
        $directorio->contrato_idtestigo1 = $request->id_testigo1;
        $directorio->contrato_idtestigo2 = $request->id_testigo2;
        $directorio->contrato_idtestigo3 = $request->id_testigo3;
        $directorio->save();

        $idc = $request->id_contrato;
        return view('layouts.pages.contratocheck', compact('idc'));
    }

    public function validar_contrato($id){
        $data = contratos::SELECT('contratos.id_contrato','contratos.numero_contrato','contratos.cantidad_letras1','contratos.fecha_firma',
                                 'contratos.municipio','contratos.arch_factura','contratos.id_folios','contratos.instructor_perfilid','contratos.unidad_capacitacion',
                                 'contratos.cantidad_numero','contratos.arch_factura','folios.iva','folios.id_cursos','folios.id_supre','tabla_supre.doc_validado',
                                 'tbl_cursos.clave','tbl_cursos.curso','tbl_cursos.id_curso','tbl_cursos.mod','tbl_cursos.pdf_curso',
                                 'instructores.nombre AS insnom','instructores.apellidoPaterno','instructores.tipo_honorario','tbl_cursos.dura',
                                 'tbl_cursos.hombre','tbl_cursos.mujer','tbl_cursos.inicio','tbl_cursos.termino','tbl_cursos.efisico','tbl_cursos.dia',
                                 'tbl_cursos.hini','tbl_cursos.hfin','instructores.apellidoMaterno','instructores.id','especialidad_instructores.especialidad_id',
                                 'instructores.archivo_ine','instructores.archivo_domicilio','instructores.archivo_alta','instructores.archivo_bancario',
                                 'instructores.archivo_fotografia','instructores.archivo_estudios','instructores.archivo_otraid','instructores.archivo_rfc','especialidad_instructores.memorandum_validacion',
                                 'especialidades.nombre AS especialidad','tbl_inscripcion.costo','cursos.perfil')
                            ->WHERE('id_contrato', '=', $id)
                            ->LEFTJOIN('folios', 'folios.id_folios', '=', 'contratos.id_folios')
                            ->LEFTJOIN('tabla_supre', 'tabla_supre.id', '=', 'folios.id_supre')
                            ->LEFTJOIN('tbl_cursos','tbl_cursos.id', '=', 'folios.id_cursos')
                            ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                            ->LEFTJOIN('especialidad_instructores', 'especialidad_instructores.id', '=', 'contratos.instructor_perfilid')
                            ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'especialidad_instructores.especialidad_id')
                            ->LEFTJOIN('tbl_inscripcion', 'tbl_inscripcion.id_curso', '=', 'tbl_cursos.id')
                            ->LEFTJOIN('cursos','cursos.id', '=', 'tbl_cursos.id_curso')
                            ->FIRST();

        $cupo = $data->hombre + $data->mujer;

        $data_directorio = contrato_directorio::WHERE('id_contrato', '=', $id)->FIRST();
        $director = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','id')->WHERE('id', '=', $data_directorio->contrato_iddirector)->FIRST();
        $testigo1 = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $data_directorio->contrato_idtestigo1)->FIRST();
        $testigo2 = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $data_directorio->contrato_idtestigo2)->FIRST();
        $testigo3 = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $data_directorio->contrato_idtestigo3)->FIRST();

        return view('layouts.pages.vstvalidarcontrato', compact('data','director','testigo1','testigo2','testigo3','cupo'));
    }

    public function rechazar_contrato(Request $request){
        $contrato = contratos::find($request->idContrato);
        $contrato->observacion = $request->observaciones;
        $contrato->fecha_status = carbon::now();
        $contrato->save();

        $folio = folio::find($request->idfolios);
        $folio->status = 'Contrato_Rechazado';
        $folio->save();

        return redirect()->route('contrato-inicio')
                        ->with('success','Contrato Rechazado Exitosamente');
    }

    public function valcontrato($id){
        contratos::where('id_folios', '=', $id)
        ->update(['fecha_status' => carbon::now()]);

        $folio = folio::find($id);
        $folio->status = "Contratado";
        $folio->save();
        return redirect()->route('contrato-inicio')
                        ->with('success','Contrato Validado Exitosamente');
    }

    public function solicitud_pago($id){
        $X = new contratos();
        $folio = new folio();
        $dataf = $folio::where('id_folios', '=', $id)->first();
        $datac = $X::where('id_folios', '=', $id)->first();
        $bancario = tbl_curso::SELECT('instructores.archivo_bancario','instructores.id AS idins','instructores.banco',
                                      'instructores.no_cuenta','instructores.interbancaria')
                                ->WHERE('tbl_cursos.id', '=', $dataf->id_cursos)
                                ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')->FIRST();
        return view('layouts.pages.vstasolicitudpago', compact('datac','dataf','bancario'));
    }

    public function save_doc(Request $request){
        $pago = new pago();

        $pago->no_memo = $request->no_memo;
        $pago->id_contrato = $request->id_contrato;
        $pago->liquido = $request->liquido;

        $file = $request->file('arch_asistencia'); # obtenemos el archivo
        $urldocs = $this->pago_upload($file, $request->id_contrato, 'asistencia'); #invocamos el método
        // guardamos en la base de datos
        $pago->arch_asistencia = trim($urldocs);

        $file = $request->file('arch_evidencia'); # obtenemos el archivo
        $urldocs = $this->pdf_upload($file, $request->id_contrato, 'evidencia'); #invocamos el método
        // guardamos en la base de datos
        $pago->arch_evidencia = trim($urldocs);
        $pago->fecha_status = carbon::now();
        $pago->save();

        contrato_directorio::where('id_contrato', '=', $request->id_contrato)
        ->update(['solpa_elaboro' => $request->id_elabora,
                  'solpa_para' => $request->id_destino,
                  'solpa_ccp1' => $request->id_ccp1,
                  'solpa_ccp2' => $request->id_ccp2,
                  'solpa_ccp3' => $request->id_ccp3]);
        if($request->arch_factura != NULL)
        {
            $file = $request->file('arch_factura'); # obtenemos el archivo
            $urldocs = $this->pdf_upload($file, $request->id_contrato, 'factura'); #invocamos el método
            // guardamos en la base de datos
            $contrato = contratos::find($request->id_contrato);
            $contrato->arch_factura = trim($urldocs);
            $contrato->save();
        }

        if ($request->file('arch_bancario') != null)
        {
            $banco = $request->file('arch_bancario'); # obtenemos el archivo
            $urlbanco = $this->pdf_upload($banco, $request->id_instructor, 'banco'); # invocamos el método
            $instructor = instructor::find($request->id_instructor);
            $instructor->archivo_bancario = trim($urlbanco);
            $instructor->banco = $request->nombre_banco;
            $instructor->no_cuenta = $request->numero_cuenta;
            $instructor->interbancaria = $request->clabe;
            $instructor->save();
        }

        folio::where('id_folios', '=', $request->id_folio)
        ->update(['status' => 'Verificando_Pago']);

        return redirect()->route('contrato-inicio')
                        ->with('success','Solicitud de Pago Agregado');

    }

    public function mod_solicitud_pago($id){
        $X = new contratos();
        $folio = new folio();
        $dataf = $folio::where('id_folios', '=', $id)->first();
        $datac = $X::where('id_folios', '=', $id)->first();
        $bancario = tbl_curso::SELECT('instructores.archivo_bancario','instructores.id AS idins')
                                ->WHERE('tbl_cursos.id', '=', $dataf->id_cursos)
                                ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')->FIRST();

        $datap = pago::WHERE('id_contrato', '=', $datac->id_contrato)->FIRST();
        $directorio = contrato_directorio::where('id_contrato', '=', $datac->id_contrato)->FIRST();
        $elaboro = directorio::WHERE('id', '=', $directorio->solpa_elaboro)->FIRST();
        $director = directorio::WHERE('id', '=', $directorio->contrato_iddirector)->FIRST();
        $para = directorio::WHERE('id', '=', $directorio->solpa_para)->FIRST();
        $ccp1 = directorio::WHERE('id', '=', $directorio->solpa_ccp1)->FIRST();
        $ccp2 = directorio::WHERE('id', '=', $directorio->solpa_ccp2)->FIRST();
        $ccp3 = directorio::WHERE('id', '=', $directorio->solpa_ccp3)->FIRST();

        return view('layouts.pages.vstamodsolicitudpago', compact('datac','dataf','datap','bancario','directorio','elaboro','para','ccp1','ccp2','ccp3'));
    }

    public function save_mod_solpa(Request $request){

        $pago = pago::find($request->id_pago);
        $pago->no_memo = $request->no_memo;
        $pago->id_contrato = $request->id_contrato;
        $pago->liquido = $request->liquido;
        $pago->fecha_status = carbon::now();

        if($request->arch_asistencia != NULL)
        {
            $file = $request->file('arch_asistencia'); # obtenemos el archivo
            $urldocs = $this->pago_upload($file, $request->id_contrato, 'asistencia'); #invocamos el método
            // guardamos en la base de datos
            $pago->arch_asistencia = trim($urldocs);
        }

        if($request->arch_evidencia != NULL)
        {
            $file = $request->file('arch_evidencia'); # obtenemos el archivo
            $urldocs = $this->pdf_upload($file, $request->id_contrato, 'evidencia'); #invocamos el método
            // guardamos en la base de datos
            $pago->arch_evidencia = trim($urldocs);
        }

        $pago->save();

        contrato_directorio::where('id_contrato', '=', $request->id_contrato)
        ->update(['solpa_elaboro' => $request->id_elabora,
                  'solpa_para' => $request->id_destino,
                  'solpa_ccp1' => $request->id_ccp1,
                  'solpa_ccp2' => $request->id_ccp2,
                  'solpa_ccp3' => $request->id_ccp3]);

        if($request->arch_factura != NULL)
        {
            $file = $request->file('arch_factura'); # obtenemos el archivo
            $urldocs = $this->pdf_upload($file, $request->id_contrato, 'factura'); #invocamos el método
            // guardamos en la base de datos
            $contrato = contratos::find($request->id_contrato);
            $contrato->arch_factura = trim($urldocs);
            $contrato->save();
        }

        if ($request->file('arch_bancario') != null)
        {
            $banco = $request->file('arch_bancario'); # obtenemos el archivo
            $urlbanco = $this->pdf_upload($banco, $request->id_instructor, 'banco'); # invocamos el método
            $instructor = instructor::find($request->id_instructor);
            $instructor->archivo_bancario = trim($urlbanco);
            $instructor->banco = $request->nombre_banco;
            $instructor->no_cuenta = $request->numero_cuenta;
            $instructor->interbancaria = $request->clabe;
            $instructor->save();
        }

        folio::where('id_folios', '=', $request->id_folio)
        ->update(['status' => 'Verificando_Pago']);

        return redirect()->route('contrato-inicio')
                        ->with('success','Solicitud de Pago Modificado');
    }

    public function historial_validado($id){
        $data = contratos::SELECT('contratos.id_contrato','contratos.numero_contrato','contratos.cantidad_letras1','contratos.fecha_firma',
                                 'contratos.municipio','contratos.arch_factura','contratos.id_folios','contratos.instructor_perfilid','contratos.unidad_capacitacion',
                                 'contratos.cantidad_numero','contratos.arch_factura','contratos.observacion','folios.iva','folios.id_cursos','folios.id_supre','folios.status',
                                 'tabla_supre.doc_validado','tbl_cursos.clave','tbl_cursos.curso','tbl_cursos.id_curso','tbl_cursos.mod',
                                 'instructores.nombre AS insnom','instructores.apellidoPaterno','instructores.tipo_honorario','tbl_cursos.dura',
                                 'tbl_cursos.hombre','tbl_cursos.mujer','tbl_cursos.inicio','tbl_cursos.termino','tbl_cursos.efisico','tbl_cursos.dia',
                                 'tbl_cursos.hini','tbl_cursos.hfin','instructores.apellidoMaterno','instructores.id','especialidad_instructores.especialidad_id',
                                 'instructores.archivo_ine','instructores.archivo_domicilio','instructores.archivo_alta','instructores.archivo_bancario',
                                 'instructores.archivo_fotografia','instructores.archivo_estudios','instructores.archivo_otraid','instructores.archivo_rfc','especialidad_instructores.memorandum_validacion',
                                 'especialidades.nombre AS especialidad','tbl_inscripcion.costo','cursos.perfil')
                            ->WHERE('id_contrato', '=', $id)
                            ->LEFTJOIN('folios', 'folios.id_folios', '=', 'contratos.id_folios')
                            ->LEFTJOIN('tabla_supre', 'tabla_supre.id', '=', 'folios.id_supre')
                            ->LEFTJOIN('tbl_cursos','tbl_cursos.id', '=', 'folios.id_cursos')
                            ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                            ->LEFTJOIN('especialidad_instructores', 'especialidad_instructores.id', '=', 'contratos.instructor_perfilid')
                            ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'especialidad_instructores.especialidad_id')
                            ->LEFTJOIN('tbl_inscripcion', 'tbl_inscripcion.id_curso', '=', 'tbl_cursos.id')
                            ->LEFTJOIN('cursos','cursos.id', '=', 'tbl_cursos.id_curso')
                            ->FIRST();

        $cupo = $data->hombre + $data->mujer;

        $data_directorio = contrato_directorio::WHERE('id_contrato', '=', $id)->FIRST();
        $director = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','id')->WHERE('id', '=', $data_directorio->contrato_iddirector)->FIRST();
        $testigo1 = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $data_directorio->contrato_idtestigo1)->FIRST();
        $testigo2 = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $data_directorio->contrato_idtestigo2)->FIRST();
        $testigo3 = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $data_directorio->contrato_idtestigo3)->FIRST();

        return view('layouts.pages.vsthistorialvalidarcontrato', compact('data','director','testigo1','testigo2','testigo3','cupo'));
    }

    public function delete($id)
    {
        $id_supre = folio::SELECT('id_supre')->WHERE('id_folios', '=', $id)->FIRST();
        $list = folio::SELECT('id_folios')->WHERE('id_supre', '=', $id_supre->id_supre)->GET();
        foreach($list as $item)
        {
            $idcontrato = contratos::SELECT('id_contrato')->WHERE('id_folios', '=', $item->id_folios)->FIRST();
            if($idcontrato != NULL)
            {
                contrato_directorio::WHERE('id_contrato', '=', $idcontrato->id_contrato)->DELETE();
                contratos::where('id_folios', '=', $item->id_folios)->DELETE();
            }
            $affecttbl_inscripcion = DB::table("folios")->WHERE('id_folios', $item->id_folios)->update(['status' => 'eliminado']);
        }
        DB::table('tabla_supre')->WHERE('id', $id_supre->id_supre)->UPDATE(['status' => 'Rechazado']);

        return redirect()->route('contrato-inicio')
                        ->with('success','Solicitud de Contrato Eliminado');
    }

    public function contractRestart($id)
    {
        $affecttbl_inscripcion = DB::table("folios")->WHERE('id_folios', $id)->update(['status' => 'Contrato_Rechazado']);

        return redirect()->route('contrato-inicio')
                        ->with('success','Solicitud de Contrato Reiniciado');
    }

    public function get_directorio(Request $request){

        $search = $request->search;

        if (isset($search)) {
            # si la variable está inicializada
            if($search == ''){
                $directorio = directorio::orderby('nombre','asc')->select('id','nombre','apellidoPaterno','apellidoMaterno','puesto')->limit(5)->get();
            }else{
                $directorio = directorio::orderby('nombre','asc')->select('id','nombre','apellidoPaterno','apellidoMaterno','puesto')->where('nombre', 'like', '%' .$search . '%')->limit(5)->get();
            }

            $response = array();
            foreach($directorio as $dir){
                $response[] = array("value"=>$dir->id,"label"=>$dir->nombre . " " .$dir->apellidoPaterno . " " . $dir->apellidoMaterno, "charge"=>$dir->puesto);
            }

            echo json_encode($response);
            exit;
        }
    }

    public function pre_contratoPDF($id)
    {

        $contrato = new contratos();

        $data_contrato = contratos::WHERE('id_contrato', '=', $id)->FIRST();

        $data_directorio = contrato_directorio::WHERE('id_contrato', '=', $id)->FIRST();
        $director = directorio::WHERE('id', '=', $data_directorio->contrato_iddirector)->FIRST();
        $testigo1 = directorio::WHERE('id', '=', $data_directorio->contrato_idtestigo1)->FIRST();
        $testigo2 = directorio::WHERE('id', '=', $data_directorio->contrato_idtestigo2)->FIRST();
        $testigo3 = directorio::WHERE('id', '=', $data_directorio->contrato_idtestigo3)->FIRST();

        $data = $contrato::SELECT('folios.id_folios','folios.importe_total','tbl_cursos.id','tbl_cursos.horas','instructores.nombre','instructores.apellidoPaterno',
                                  'instructores.apellidoMaterno','instructores.folio_ine','instructores.rfc','instructores.curp',
                                  'instructores.domicilio')
                          ->WHERE('folios.id_folios', '=', $data_contrato->id_folios)
                          ->LEFTJOIN('folios', 'folios.id_folios', '=', 'contratos.id_folios')
                          ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                          ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                          ->FIRST();
                          //nomes especialidad
        $especialidad = especialidad_instructor::SELECT('especialidades.nombre')
                                                ->WHERE('especialidad_instructores.id', '=', $data_contrato->instructor_perfilid)
                                                ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'especialidad_instructores.especialidad_id')
                                                ->FIRST();
        $nomins = $data->nombre . ' ' . $data->apellidoPaterno . ' ' . $data->apellidoMaterno;
        $date = strtotime($data_contrato->fecha_firma);
        $D = date('d', $date);
        $M = $this->toMonth(date('m', $date));
        $Y = date("Y", $date);

        $cantidad = $this->numberFormat($data_contrato->cantidad_numero);
        $monto = explode(".",strval($data_contrato->cantidad_numero));

        $pdf = PDF::loadView('layouts.pdfpages.precontratohonorarios', compact('director','testigo1','testigo2','testigo3','data_contrato','data','nomins','D','M','Y','monto','especialidad','cantidad'));

        return $pdf->stream('Contrato Instructor.pdf');
    }

    public function contrato_pdf($id)
    {

        $contrato = new contratos();

        $data_contrato = contratos::WHERE('id_contrato', '=', $id)->FIRST();

        $data_directorio = contrato_directorio::WHERE('id_contrato', '=', $id)->FIRST();
        $director = directorio::WHERE('id', '=', $data_directorio->contrato_iddirector)->FIRST();
        $testigo1 = directorio::WHERE('id', '=', $data_directorio->contrato_idtestigo1)->FIRST();
        $testigo2 = directorio::WHERE('id', '=', $data_directorio->contrato_idtestigo2)->FIRST();
        $testigo3 = directorio::WHERE('id', '=', $data_directorio->contrato_idtestigo3)->FIRST();

        $data = $contrato::SELECT('folios.id_folios','folios.importe_total','tbl_cursos.id','tbl_cursos.horas','instructores.nombre','instructores.apellidoPaterno',
                                  'instructores.apellidoMaterno','instructores.folio_ine','instructores.rfc','instructores.curp',
                                  'instructores.domicilio')
                          ->WHERE('folios.id_folios', '=', $data_contrato->id_folios)
                          ->LEFTJOIN('folios', 'folios.id_folios', '=', 'contratos.id_folios')
                          ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                          ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                          ->FIRST();
                          //nomes especialidad
        $especialidad = especialidad_instructor::SELECT('especialidades.nombre')
                                                ->WHERE('especialidad_instructores.id', '=', $data_contrato->instructor_perfilid)
                                                ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'especialidad_instructores.especialidad_id')
                                                ->FIRST();
        $nomins = $data->nombre . ' ' . $data->apellidoPaterno . ' ' . $data->apellidoMaterno;
        $date = strtotime($data_contrato->fecha_firma);
        $D = date('d', $date);
        $M = $this->toMonth(date('m', $date));
        $Y = date("Y", $date);

        $cantidad = $this->numberFormat($data_contrato->cantidad_numero);
        $monto = explode(".",strval($data_contrato->cantidad_numero));

        $pdf = PDF::loadView('layouts.pdfpages.contratohonorarios', compact('director','testigo1','testigo2','testigo3','data_contrato','data','nomins','D','M','Y','monto','especialidad','cantidad'));

        return $pdf->stream('Contrato Instructor.pdf');
    }

    public function solicitudpago_pdf($id){

        $data = folio::SELECT('tbl_cursos.curso','tbl_cursos.clave','tbl_cursos.espe','tbl_cursos.mod','tbl_cursos.inicio',
                              'tbl_cursos.termino','tbl_cursos.hini','tbl_cursos.hfin','tbl_cursos.id AS id_curso','instructores.nombre',
                              'instructores.apellidoPaterno','instructores.apellidoMaterno', 'especialidad_instructores.memorandum_validacion',
                              'instructores.rfc','instructores.id AS id_instructor','instructores.banco','instructores.no_cuenta',
                              'instructores.interbancaria','folios.importe_total','folios.id_folios','contratos.unidad_capacitacion',
                              'contratos.id_contrato','pagos.created_at','pagos.no_memo','pagos.liquido')
                        ->WHERE('folios.id_folios', '=', $id)
                        ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                        ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                        ->LEFTJOIN('contratos', 'contratos.id_folios', '=', 'folios.id_folios')
                        ->LEFTJOIN('pagos', 'pagos.id_contrato', '=', 'contratos.id_contrato')
                        ->LEFTJOIN('especialidad_instructores', 'especialidad_instructores.id', '=', 'contratos.instructor_perfilid')
                        ->FIRST();

        $date = strtotime($data->created_at);
        $D = date('d', $date);
        $M = $this->toMonth(date('m',$date));
        $Y = date("Y",$date);

        $data_directorio = contrato_directorio::WHERE('id_contrato', '=', $data->id_contrato)->FIRST();
        $elaboro = directorio::WHERE('id', '=', $data_directorio->solpa_elaboro)->FIRST();
        $director = directorio::WHERE('id', '=', $data_directorio->contrato_iddirector)->FIRST();
        $para = directorio::WHERE('id', '=', $data_directorio->solpa_para)->FIRST();
        $ccp1 = directorio::WHERE('id', '=', $data_directorio->solpa_ccp1)->FIRST();
        $ccp2 = directorio::WHERE('id', '=', $data_directorio->solpa_ccp2)->FIRST();
        $ccp3 = directorio::WHERE('id', '=', $data_directorio->solpa_ccp3)->FIRST();
        //dd($data);
        $pdf = PDF::loadView('layouts.pdfpages.procesodepago', compact('data','D','M','Y','elaboro','para','ccp1','ccp2','ccp3','director'));

        return $pdf->stream('solicitud de pago.pdf');

    }

    public function docs($docs){
        return response()->download($docs);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    protected function pago_upload($pdf, $id)
    {
        $tamanio = $pdf->getSize(); #obtener el tamaño del archivo del cliente
        $extensionPdf = $pdf->getClientOriginalExtension(); // extension de la imagen
        # nuevo nombre del archivo
        $pdfFile = trim("docs"."_".date('YmdHis')."_".$id.".".$extensionPdf);
        $pdf->storeAs('/uploadContrato/contrato/'.$id, $pdfFile); // guardamos el archivo en la carpeta storage
        $pdfUrl = Storage::url('/uploadContrato/contrato/'.$id."/".$pdfFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $pdfUrl;
    }
    protected function pdf_upload($pdf, $id, $nom)
    {
        # nuevo nombre del archivo
        $pdfFile = trim($nom."_".date('YmdHis')."_".$id.".pdf");
        $pdf->storeAs('/uploadFiles/instructor/'.$id, $pdfFile); // guardamos el archivo en la carpeta storage
        $pdfUrl = Storage::url('/uploadFiles/instructor/'.$id."/".$pdfFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $pdfUrl;
    }

    protected function toMonth($m)
    {
        switch ($m) {
            case 1:
                return "Enero";
            break;
            case 2:
                return "Febrero";
            break;
            case 3:
                return "Marzo";
            break;
            case 4:
                return "Abril";
            break;
            case 5:
                return "Mayo";
            break;
            case 6:
                return "Junio";
            break;
            case 7:
                return "Julio";
            break;
            case 8:
                return "Agosto";
            break;
            case 9:
                return "Septiembre";
            break;
            case 10:
                return "Octubre";
            break;
            case 11:
                return "Noviembre";
            break;
            case 12:
                return "Diciembre";
            break;


        }
    }

    protected function numberFormat($numero)
    {
        $part = explode(".", $numero);
        $part[0] = number_format($part['0']);
        $cadwell = implode(".", $part);
        return ($cadwell);
    }
}
