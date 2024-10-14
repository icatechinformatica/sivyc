<?php

namespace App\Http\Controllers\ExpeController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\ModelExpe\ExpeUnico;
use App\Models\Alumnopre;
use App\Models\Inscripcion;
use App\Models\Unidad;
use App\Models\tbl_curso;
use Maatwebsite\Excel\Facades\Excel;
use App\Excel\xlsConvenios;

class BuzonexpController extends Controller
{
    function __construct() {
        session_start();
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            $this->ubicacion = Unidad::where('id',$this->user->unidad)->value('ubicacion');
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $ubic_user = '';
        $status_dpto = [];
        $default_st = '';
        $unidades = '';
        $unidades_env = null;
        $ejercicio = [];
        $boton_excel = false;
        $_SESSION['consulta_todos'] = '';
        for ($i=2021; $i <= intval(date('Y')); $i++) {array_push($ejercicio, $i);}

        #OBTENERMOS VALORES DEL REQUEST
        $sel_eje = $request->input('sel_ejercicio');
        $sel_uni = $request->input('sel_unidad');
        $sel_status = $request->input('sel_status');
        $txtbuscar = $request->input('txtbuscar');

        #Rol
        $val_rol = null;
        $user = Auth::user();$roles = $user->roles();$resul = $roles->first();
        $slug = $resul->slug;
        if($slug == 'titular-innovacion' || $slug == 'auxiliar-innovacion' || $slug == 'dta') $val_rol = 4;
        else if($slug == 'administrativo' || $slug == 'admin' || $slug == 'titular_unidad' || $slug == 'unidad_vinculacion'
                || $slug == 'pagos_contratos' || $slug == 'director_unidad') $val_rol = 3;
        //Esto linea es solo para hacer pruebas
        // $val_rol = 3;

        #DTA
        $this->rol = $val_rol;
        if($val_rol == 4){
            $status_dpto = ['PENDIENTE', 'RETORNADO', 'VALIDADO','TODOS'];
            $unidades = Unidad::where('cct','like', '%07EI%')->orderby('unidad')->pluck('unidad');

            #Condiciones para las unidades y en caso de que vengan vacios o nulos
            if(empty($sel_eje)) $sel_eje = date('Y');
            if($sel_uni == null || $sel_uni == '') {
                $unidades_env = $unidades;
            }else{
                $unidades_env = [$sel_uni];
            }
            if($sel_status == '') $sel_status = 'PENDIENTE';

            if($sel_status == 'TODOS'){
                $data_admin = $this->consulta_todos($sel_eje, $unidades_env, $txtbuscar);
                //Hacer una consulta general y meterlo en un arreglo global para genera un excel en un futuro
                if(!empty($data_admin)){
                    $_SESSION['consulta_todos'] = [$sel_eje, $unidades_env, $txtbuscar];
                    $boton_excel = true;
                }

            }else{
                $_SESSION['consulta_todos'] = '';
                $data_admin = $this->consulta_datos($sel_status, $sel_eje, $unidades_env, $txtbuscar);
            }

        }

        #DELEGACION ADMINISTRATIVA
        if($val_rol == 3){
            $status_dpto = ['EN CAPTURA', 'PENDIENTE POR ENVIAR', 'ENVIADO A DTA', 'RETORNADO', 'VALIDADO'];
            // $default_st = 'PENDIENTE POR ENVIAR';
            $ubic_user = $this->ubicacion;
            $unidades = Unidad::where('ubicacion', $ubic_user)->orderby('unidad')->pluck('unidad');

            #Condiciones para las unidades y en caso de que vengan vacios o nulos
            if(empty($sel_eje)) $sel_eje = date('Y');
            if($sel_uni == null || $sel_uni == '') {
                $unidades_env = $unidades;
            }else{
                $unidades_env = [$sel_uni];
            }
            if($sel_status == '') $sel_status = 'PENDIENTE POR ENVIAR';

            //Consulta
            $data_admin = $this->consulta_datos($sel_status, $sel_eje, $unidades_env, $txtbuscar);
        }


        return view('vistas_expe.buzon_expe', compact('ejercicio', 'unidades', 'status_dpto', 'val_rol', 'data_admin',
        'sel_eje', 'sel_uni', 'sel_status', 'txtbuscar', 'boton_excel'));
    }

    public function consulta_datos($sel_status, $sel_eje, $unidades_env, $txtbuscar){

        try {
            if ($this->rol == 4 && !empty($unidades_env)) {
                $unidades_env = Unidad::whereIn('ubicacion', $unidades_env)
                    ->orderBy('unidad')
                    ->pluck('unidad');
            }

            $data = tbl_curso::query()
                ->join('tbl_cursos_expedientes as ex', 'ex.folio_grupo', '=', 'tbl_cursos.folio_grupo')
                ->join('pagos as pa', 'pa.id_curso', '=', 'tbl_cursos.id');

            if (!empty($txtbuscar)) {
                $data = $data->whereRaw("CONCAT(tbl_cursos.clave, ' ', tbl_cursos.folio_grupo) LIKE ?", ['%' . $txtbuscar . '%']);

            } elseif (!empty($sel_eje)) {
                $data = $data->where(DB::raw("date_part('year', tbl_cursos.inicio)"), '=', $sel_eje);

                if (!empty($unidades_env)) {
                    $data = $data->whereIn('tbl_cursos.unidad', $unidades_env);
                }

                if (!empty($sel_status)) {
                    $data = $data->where(function ($query) use ($sel_status) {
                        $query->BusquedaExpediente($sel_status);
                    });
                }
            } else {
                return redirect()->route('buzon.expunico.index')
                    ->with('message', '¡ERROR AL REALIZAR LA BÚSQUEDA!')
                    ->with('status', 'danger');
            }

            // Filtrado de pagado por financieros y proceso terminado por parte de planeación.
            $data = $data->where('tbl_cursos.proceso_terminado', true)->where('pa.status_transferencia', 'PAGADO');

            // Selección de campos
            $data = $data->select(
                'tbl_cursos.unidad',
                'tbl_cursos.folio_grupo',
                'tbl_cursos.clave',
                'tbl_cursos.curso',
                'tbl_cursos.nombre',
                'tbl_cursos.inicio',
                'tbl_cursos.termino',
                'tbl_cursos.hini',
                'tbl_cursos.hfin',
                DB::raw("ex.administrativo->>'fecha_envio_dta' as fec_envio"),
                DB::raw("ex.administrativo->>'fecha_retornado' as fec_return"),
                DB::raw("ex.administrativo->>'fecha_validado' as fec_valid"),
                DB::raw("ex.vinculacion->>'fecha_guardado' as fecg_vin"),
                DB::raw("ex.academico->>'fecha_guardado' as fecg_aca"),
                DB::raw("ex.administrativo->>'fecha_guardado' as fecg_admi"),
                DB::raw("ex.vinculacion->>'status_save' as sav_vinc"),
                DB::raw("ex.academico->>'status_save' as sav_acad"),
                DB::raw("ex.administrativo->>'status_save' as sav_admin"),
                DB::raw("ex.vinculacion->>'status_dpto' as st_vinc"),
                DB::raw("ex.academico->>'status_dpto' as st_aca"),
                DB::raw("ex.administrativo->>'status_dpto' as st_admin")
            )->orderByDesc('tbl_cursos.id')->paginate(15);

        } catch (\Exception $e) {
            return redirect()->route('buzon.expunico.index')
                ->with('message', '¡ERROR AL REALIZAR LA BÚSQUEDA!')
                ->with('status', 'danger');
        }

        return $data;

    }

    private function consulta_todos($sel_eje, $unidades_env, $txtbuscar) {
        try {

            $unidades_env = Unidad::whereIn('ubicacion', $unidades_env)->orderBy('unidad')->pluck('unidad');

            $data = tbl_curso::query()
                ->join('tbl_cursos_expedientes as ex', 'ex.folio_grupo', '=', 'tbl_cursos.folio_grupo')
                ->join('pagos as pa', 'pa.id_curso', '=', 'tbl_cursos.id');

            if (!empty($txtbuscar)) {
                $data = $data->whereRaw("CONCAT(tbl_cursos.clave, ' ', tbl_cursos.folio_grupo) LIKE ?", ['%' . $txtbuscar . '%']);

            } elseif (!empty($sel_eje)) {
                $data = $data->where(DB::raw("date_part('year', tbl_cursos.inicio)"), '=', $sel_eje);

                if (!empty($unidades_env)) {
                    $data = $data->whereIn('tbl_cursos.unidad', $unidades_env);
                }

                $data = $data->whereIn(DB::raw("ex.administrativo->>'status_dpto'"), ['ENVIADO', 'RETORNADO', 'VALIDADO']);
            } else {
                return redirect()->route('buzon.expunico.index')
                    ->with('message', '¡ERROR AL REALIZAR LA BÚSQUEDA!')
                    ->with('status', 'danger');
            }

            // Filtrado de pagado por financieros y proceso terminado por parte de planeación.
            $data = $data->where('tbl_cursos.proceso_terminado', true)->where('pa.status_transferencia', 'PAGADO');

            // Selección de campos
            $data = $data->select(
                'tbl_cursos.unidad',
                'tbl_cursos.folio_grupo',
                'tbl_cursos.clave',
                'tbl_cursos.curso',
                'tbl_cursos.nombre',
                'tbl_cursos.inicio',
                'tbl_cursos.termino',
                'tbl_cursos.hini',
                'tbl_cursos.hfin',
                DB::raw("ex.administrativo->>'fecha_envio_dta' as fec_envio"),
                DB::raw("ex.administrativo->>'fecha_retornado' as fec_return"),
                DB::raw("ex.administrativo->>'fecha_validado' as fec_valid"),
                DB::raw("ex.vinculacion->>'fecha_guardado' as fecg_vin"),
                DB::raw("ex.academico->>'fecha_guardado' as fecg_aca"),
                DB::raw("ex.administrativo->>'fecha_guardado' as fecg_admi"),
                DB::raw("ex.vinculacion->>'status_save' as sav_vinc"),
                DB::raw("ex.academico->>'status_save' as sav_acad"),
                DB::raw("ex.administrativo->>'status_save' as sav_admin"),
                DB::raw("ex.vinculacion->>'status_dpto' as st_vinc"),
                DB::raw("ex.academico->>'status_dpto' as st_aca"),
                DB::raw("ex.administrativo->>'status_dpto' as st_admin")
            )->orderByDesc('tbl_cursos.id')->paginate(15);

        } catch (\Exception $e) {
            return redirect()->route('buzon.expunico.index')
                ->with('message', '¡ERROR AL REALIZAR LA BÚSQUEDA!')
                ->with('status', 'danger');
        }

        return $data;
    }

    public function generar_excel(Request $request) {
        $consulta = $_SESSION['consulta_todos'];
        $sel_eje = $consulta[0];
        $unidades_env = $consulta[1];
        $txtbuscar = $consulta[2];

        // dd($consulta[0], $consulta[1], $consulta[2]);
        //Generacion de excel
        try {
            $unidades_env = Unidad::whereIn('ubicacion', $unidades_env)->orderBy('unidad')->pluck('unidad');

            $data = tbl_curso::query()
                ->join('tbl_cursos_expedientes as ex', 'ex.folio_grupo', '=', 'tbl_cursos.folio_grupo')
                ->join('pagos as pa', 'pa.id_curso', '=', 'tbl_cursos.id');

            if (!empty($txtbuscar)) {
                $data = $data->whereRaw("CONCAT(tbl_cursos.clave, ' ', tbl_cursos.folio_grupo) LIKE ?", ['%' . $txtbuscar . '%']);

            } elseif (!empty($sel_eje)) {
                $data = $data->where(DB::raw("date_part('year', tbl_cursos.inicio)"), '=', $sel_eje);

                if (!empty($unidades_env)) {
                    $data = $data->whereIn('tbl_cursos.unidad', $unidades_env);
                }

                $data = $data->whereIn(DB::raw("ex.administrativo->>'status_dpto'"), ['ENVIADO', 'RETORNADO', 'VALIDADO']);
            } else {
                return redirect()->route('buzon.expunico.index')
                    ->with('message', '¡ERROR AL REALIZAR LA BÚSQUEDA!')
                    ->with('status', 'danger');
            }

            // Filtrado de pagado por financieros y proceso terminado por parte de planeación.
            $data = $data->where('tbl_cursos.proceso_terminado', true)->where('pa.status_transferencia', 'PAGADO');

            // Selección de campos
            $data = $data->select(
                'tbl_cursos.unidad',
                'tbl_cursos.folio_grupo',
                'tbl_cursos.clave',
                'tbl_cursos.curso',
                'tbl_cursos.nombre',
                'tbl_cursos.inicio',
                'tbl_cursos.termino',
                'tbl_cursos.hini',
                'tbl_cursos.hfin',
                DB::raw("ex.administrativo->>'fecha_envio_dta' as fec_envio"),
                DB::raw("ex.administrativo->>'fecha_retornado' as fec_return"),
                DB::raw("ex.administrativo->>'fecha_validado' as fec_valid"),
                DB::raw("ex.administrativo->>'status_dpto' as st_admin")
            )->orderByDesc('tbl_cursos.id')->get();

        } catch (\Exception $e) {
            return redirect()->route('buzon.expunico.index')
                ->with('message', '¡ERROR AL REALIZAR LA BÚSQUEDA!')
                ->with('status', 'danger');
        }

        ##Excel
        $head = ['UNIDAD', 'FOLIO DE GRUPO', 'CLAVE', 'CURSO', 'INSTRUCTOR',
                'INICIO', 'TERMINO', 'HORA DE INICIO', 'HORA DE TERMINO', 'FECH ENVIO',
                'FECH RETORNO', 'FECH VALIDACION', 'ESTATUS'];

        $title = "LISTA DE CURSOS EXPE UNICO";
        $name = $title."_".date('Ymd').".xlsx";
        $view = 'layouts.pages.reportes.excel_lista_expeunico';
        $datos_vista = [
            'data' => $data,
            'anio' => $sel_eje,
        ];

        if(count($data)>0)return Excel::download(new xlsConvenios($datos_vista, $head, $title, $view), $name);

        // dd($data[0]->unidad);
    }

}
