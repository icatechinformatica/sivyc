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

class BuzonexpController extends Controller
{
    function __construct() {
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
        if($slug == 'titular-innovacion' || $slug == 'auxiliar-innovacion') $val_rol = 4;
        else if($slug == 'administrativo' || $slug == 'admin' || $slug == 'titular_unidad' || $slug == 'unidad_vinculacion'
                || $slug == 'pagos_contratos' || $slug == 'director_unidad') $val_rol = 3;
        //Esto linea es solo para hacer pruebas
        // $val_rol = 3;

        #DTA
        $this->rol = $val_rol;
        if($val_rol == 4){
            $status_dpto = ['PENDIENTE', 'RETORNADO', 'VALIDADO'];
            $unidades = Unidad::where('cct','like', '%07EI%')->orderby('unidad')->pluck('unidad');

            #Condiciones para las unidades y en caso de que vengan vacios o nulos
            if(empty($sel_eje)) $sel_eje = date('Y');
            if($sel_uni == null || $sel_uni == '') {
                $unidades_env = $unidades;
            }else{
                $unidades_env = [$sel_uni];
            }
            if($sel_status == '') $sel_status = 'PENDIENTE';

            $data_admin = $this->consulta_datos($sel_status, $sel_eje, $unidades_env, $txtbuscar);

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
        'sel_eje', 'sel_uni', 'sel_status', 'txtbuscar'));
    }

    public function consulta_datos($sel_status, $sel_eje, $unidades_env, $txtbuscar){
        try {
            if ($this->rol==4) $unidades_env = Unidad::wherein('ubicacion', $unidades_env)->orderby('unidad')->pluck('unidad');

            $data = tbl_curso::query()->join('tbl_cursos_expedientes as ex', 'ex.folio_grupo', '=', 'tbl_cursos.folio_grupo');            
            if (!empty($txtbuscar)) {
                $data = $data->whereRaw("CONCAT(tbl_cursos.clave, ' ', tbl_cursos.folio_grupo) LIKE ?", ['%' .$txtbuscar. '%']);                
            }elseif (!empty($sel_eje)){                
                if(!empty($sel_eje)) $data = $data->where(DB::raw("date_part('year' , tbl_cursos.inicio)"), '=', $sel_eje); //Anio
                if(!empty($unidades_env)) $data = $data->whereIn('tbl_cursos.unidad', $unidades_env); //Unidad(es)
                if (!empty($sel_status)) {
                    $data = $data->where(function ($query) use ($sel_status) {                    
                        $query->BusquedaExpediente($sel_status);
                    });  
                }


            } else return redirect()->route('buzon.expunico.index')->with('message', '¡ERROR AL REALIZAR LA BUSQUEDA!')->with('status', 'danger');
            
            $data = $data->select('tbl_cursos.unidad', 'tbl_cursos.folio_grupo', 'tbl_cursos.clave', 'tbl_cursos.curso', 'tbl_cursos.nombre',
            'tbl_cursos.inicio', 'tbl_cursos.termino', 'tbl_cursos.hini', 'tbl_cursos.hfin',
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
                    DB::raw("ex.administrativo->>'status_dpto' as st_admin"))
            ->orderByDesc('tbl_cursos.id')
            ->paginate(15, ['tbl_cursos.unidad', 'tbl_cursos.folio_grupo', 'tbl_cursos.clave', 'tbl_cursos.curso', 'tbl_cursos.nombre',
            'tbl_cursos.inicio', 'tbl_cursos.termino', 'tbl_cursos.hini', 'tbl_cursos.hfin']);
        } catch (\Throwable $th) {
            return redirect()->route('buzon.expunico.index')->with('message', '¡ERROR AL REALIZAR LA BUSQUEDA!')->with('status', 'danger');
        }

        return $data;

    }



}
