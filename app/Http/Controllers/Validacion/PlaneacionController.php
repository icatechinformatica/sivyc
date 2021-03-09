<?php

namespace App\Http\Controllers\Validacion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use PDF;

class PlaneacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // unidad a buscar
        $unidades = $request->get('busqueda_unidad');
        // cursos unidades por planeacion
        $cursos_unidades_planeacion = DB::table('tbl_cursos as c')
        ->select('c.id AS id_tbl_cursos', 'c.unidad','c.plantel','c.espe','c.curso','c.clave','c.mod','c.dura',DB::raw("case when extract(hour from to_timestamp(c.hini,'HH24:MI a.m.')::time)<14 then 'MATUTINO' else 'VESPERTINO' end as turno"),
        DB::raw('extract(day from c.inicio) as diai'),DB::raw('extract(month from c.inicio) as mesi'),DB::raw('extract(day from c.termino) as diat'),DB::raw('extract(month from c.termino) as mest'),DB::raw("case when EXTRACT( Month FROM c.termino) between '7' and '9' then '1' when EXTRACT( Month FROM c.termino) between '10' and '12' then '2' when EXTRACT( Month FROM c.termino) between '1' and '3' then '3' else '4' end as pfin"),
        'c.horas','c.dia',DB::raw("concat(c.hini,' ', 'A', ' ',c.hfin) as horario"),DB::raw('count(distinct(ca.id)) as tinscritos'),DB::raw("SUM(CASE WHEN ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as imujer"),DB::raw("SUM(CASE WHEN ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ihombre"),DB::raw("SUM(CASE WHEN ca.acreditado= 'X' THEN 1 ELSE 0 END) as egresado"),
        DB::raw("SUM(CASE WHEN ca.acreditado='X' and ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as emujer"),DB::raw("SUM(CASE WHEN ca.acreditado='X' and ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ehombre"),DB::raw("SUM(CASE WHEN ca.noacreditado='X' THEN 1 ELSE 0 END) as desertado"),
        'ins.costo',DB::raw("SUM(ins.costo) as ctotal"),DB::raw("sum(case when ins.abrinscri='ET' and ap.sexo='FEMENINO' then 1 else 0 end) as etmujer"),DB::raw("sum(case when ins.abrinscri='ET' and ap.sexo='MASCULINO' then 1 else 0 end) as ethombre"),DB::raw("sum(case when ins.abrinscri='EP' and ap.sexo='FEMENINO' then 1 else 0 end) as epmujer"),
        DB::raw("sum(case when ins.abrinscri='EP' and ap.sexo='MASCULINO' then 1 else 0 end) as ephombre"),'c.cespecifico','c.mvalida','c.efisico','c.nombre','ip.grado_profesional','ip.estatus','i.sexo','ei.memorandum_validacion','c.mexoneracion',
        DB::raw("sum(case when ap.empresa_trabaja<>'DESEMPLEADO' then 1 else 0 end) as empleado"),DB::raw("sum(case when ap.empresa_trabaja='DESEMPLEADO' then 1 else 0 end) as desempleado"),
        DB::raw("sum(case when ap.discapacidad<> 'NINGUNA' then 1 else 0 end) as discapacidad"),DB::raw("sum(case when ar.migrante='true' then 1 else 0 end) as migrante"),DB::raw("sum(case when ar.indigena='true' then 1 else 0 end) as indigena"),DB::raw("sum(case when ar.etnia<> NULL then 1 else 0 end) as etnia"),
        'c.programa','c.muni','c.depen','c.cgeneral','c.sector','c.mpaqueteria',DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) < '15' and ap.sexo='FEMENINO' then 1 else 0 end) as iem1"),
        DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) < '15' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh1"),DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '15' and '19' and ap.sexo='FEMENINO' then 1 else 0 end) as iem2"),
        DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '15' and '19' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh2"),DB::raw("sum(Case When EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '20' and '24' and ap.sexo='FEMENINO' then 1 else 0 end) as iem3"),
        DB::raw("sum(Case When EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '20' and '24' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh3"),DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '25' and '34' and ap.sexo='FEMENINO' then 1 else 0 end) as iem4"),
        db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '25' and '34' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh4"),db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '35' and '44' and ap.sexo='FEMENINO' then 1 else 0 end) as iem5"),
        DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '35' and '44' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh5"),db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '45' and '54' and ap.sexo='FEMENINO' then 1 else 0 end) as iem6"),
        db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '45' and '54' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh6"),db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '55' and '64' and ap.sexo='FEMENINO' then 1 else 0 end) as iem7"),
        db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '55' and '64' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh7"),db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento)))>= '65' and ap.sexo='FEMENINO' then 1 else 0 end) as iem8"),
        db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento)))>= '65' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh8"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm1"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh1"),
        db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm2"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh2"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm3"),
        db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh3"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm4"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh4"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MUJER' then 1 else 0 end) as iesm5"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh5"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm6"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh6"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm7"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh7"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm8"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh8"),db::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm9"),
        db::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh9"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm1"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh1"),
        db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm2"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh2"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm3"),
        db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh3"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm4"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh4"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm5"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh5"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm6"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh6"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm7"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh7"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm8"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh8"),
        db::raw("sum(case when ap.ultimo_grado_estudios='POSTRADO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm9"),db::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh9"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm1"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh1"),
        db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as naesm2"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh2"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm3"),
        db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as naesh3"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm4"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh4"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm5"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh5"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm6"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh6"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm7"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh7"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm8"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh8"),
        db::raw("sum(case when ap.ultimo_grado_estudios='POSTRADO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm9"),db::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh9"),
        DB::raw("case when arc='01' then nota else observaciones end as tnota"),
        DB::raw("c.observaciones_formato_t->'OBSERVACION_UNIDAD_DTA'->>'OBSERVACION_UNIDAD' AS observaciones_unidad")
        )
        ->JOIN('tbl_calificaciones as ca','c.id', '=', 'ca.idcurso')
        ->JOIN('instructores as i','c.id_instructor', '=', 'i.id')
        ->JOIN('instructor_perfil as ip','i.id', '=', 'ip.numero_control')
        ->JOIN('especialidad_instructores as ei','ip.id', '=', 'ei.perfilprof_id')                
        ->JOIN('especialidades as e', function($join)
            {
                $join->on('ei.especialidad_id', '=', 'e.id');                
                $join->on('c.espe', '=', 'e.nombre');
            })
        ->JOIN('alumnos_registro as ar',function($join)
        {
            $join->on('ca.matricula', '=', 'ar.no_control');                
            $join->on('c.id_curso','=','ar.id_curso');
        }) 
        ->JOIN('alumnos_pre as ap', 'ar.id_pre', '=', 'ap.id')
        ->JOIN('tbl_inscripcion as ins', function($join)
        {
            $join->on('ca.idcurso', '=', 'ins.id_curso');                
            $join->on('ca.matricula','=','ins.matricula');
        })
        ->JOIN('tbl_unidades as u', 'u.unidad', '=', 'c.unidad')
        ->WHERE('u.ubicacion', '=', $unidades)
        ->WHERE('c.status', '=', 'TURNADO_PLANEACION')                
        ->WHERE(DB::raw("extract(year from c.termino)"), '=', '2021')
        ->WHERE('c.turnado', '=', 'PLANEACION')
        ->groupby('c.unidad','c.nombre','c.clave','c.mod','c.espe','c.curso','c.inicio','c.termino','c.dia','c.dura','c.hini','c.hfin','c.horas','c.plantel','c.programa','c.muni','c.depen','c.cgeneral','c.mvalida','c.efisico','c.cespecifico','c.sector','c.mpaqueteria','c.mexoneracion','c.nota','i.sexo','ei.memorandum_validacion','ip.grado_profesional','ip.estatus','ins.costo','c.observaciones'
                 ,'ins.abrinscri','c.arc', 'c.id')
        ->distinct()->get();
        // las unidades
        $unidadesIndex = DB::table('tbl_unidades')->select('unidad', 'ubicacion')->get();
        //indice de datos
        return view('reportes.vista_planeacion_indice', compact('unidadesIndex', 'cursos_unidades_planeacion', 'unidades'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendtodta(Request $request)
    {
        $numero_memo = $request->get('numero_memo'); // número de memo
        $cursoschk = $request->get('checkCursos');
        /**
         * vamos al cargar el archivo que se sube
         */
        if ($request->hasFile('memorandumNegativoFile')) {
            // obtenemos el valor del archivo memo

            $validator = Validator::make($request->all(), [
                'memorandumNegativoFile' => 'mimes:pdf|max:2048'
            ]);

            if ($validator->fails()) {
                # mandar un mensaje de error
                return json_encode($validator);
            } else {
                $memo = str_replace('/', '_', $numero_memo);
                /**
                 * aquí vamos a verificar que el archivo no se encuentre guardado
                 * previamente en el sistema de archivos del sistema de ser así se 
                 * remplazará el archivo porel que se subirá a continuación
                 */
                // construcción del archivo
                $archivo_memo_regreso_dta = 'uploadFiles/memoRegresoDTA/'.$memo.'/'.$memo.'.pdf';
                if (Storage::exists($archivo_memo_regreso_dta)) {
                    #checamos si hay algún documento, de ser así, procedemos a eliminarlo
                    Storage::delete($archivo_memo_regreso_dta);
                }

                $archivo_memo_planeacion_regreso_dta = $request->file('memorandumNegativoFile'); # obtenemos el archivo
                $url_archivo_memo_planeacion_regreso_dta = $this->uploaded_memo_dta_to_planeacion_file($archivo_memo_planeacion_regreso_dta, $memo, 'memoRegresoDTA'); #invocamos el método
            }
        } else {
            $url_archivo_memo_planeacion_regreso_dta = null;
        }

        if (!empty($cursoschk)) {
            # vamos a checar sólo a los checkbox checados como propiedad
            if (!empty($cursoschk)) {
                $fecha_ahora = Carbon::now();
                $date = $fecha_ahora->format('Y-m-d'); // fecha
                $numero_memo = $request->get('numero_memo'); // número de memo

                $turnado_revision_dta = [
                    'FECHA' => $date,
                    'NUMERO' => $numero_memo,
                    'MEMORANDUM' => $url_archivo_memo_planeacion_regreso_dta
                ];
                /**
                 * TURNADO_DTA:[“NUMERO”:”XXXXXX”,”FECHA”:” XXXX-XX-XX”]
                 */
                # sólo obtenemos a los que han sido chequeados para poder continuar con la actualización
                $data = explode(",", $cursoschk);
                $comentario = explode(",", $_POST['comentarios_planeacion']);
                foreach(array_combine($data, $comentario) as $key => $comentarios){
                    $observaciones_revision_dta = [
                        'OBSERVACION_RETORNO' =>  $comentarios
                    ];
                    \DB::table('tbl_cursos')
                        ->where('id', $key)
                        ->update(['memos' => DB::raw("jsonb_set(memos, '{TURNADO_REVISION_DTA}','".json_encode($turnado_revision_dta)."'::jsonb)"), 
                        'status' => 'REVISION_DTA', 
                        'turnado' => 'REVISION_DTA',
                        'observaciones_formato_t' => DB::raw("jsonb_set(observaciones_formato_t, '{OBSERVACION_CERRADO_PLANEACION}', '".json_encode($observaciones_revision_dta)."'::jsonb)")]);
                }

            }

            $json = json_encode('DONE');
            return $json;
        } else {
            $json = json_encode('EMPTYCURSOS');
            return $json;
        }
    }

    protected function finishPlaneacion(Request $request)
    {
        $numero_memo = $request->get('numero_memo'); // número de memo
        $cursoschk = $request->get('checkCursos');
        /**
         * vamos al cargar el archivo que se sube
         */
        if ($request->hasFile('memorandumPositivoFile')) {
            // obtenemos el valor del archivo memo

            $validator = Validator::make($request->all(), [
                'memorandumPositivoFile' => 'mimes:pdf|max:2048'
            ]);

            if ($validator->fails()) {
                # mandar un mensaje de error
                return json_encode($validator);
            } else {
                $memo = str_replace('/', '_', $numero_memo);
                /**
                 * aquí vamos a verificar que el archivo no se encuentre guardado
                 * previamente en el sistema de archivos del sistema de ser así se 
                 * remplazará el archivo porel que se subirá a continuación
                 */
                // construcción del archivo
                $archivo_memo = 'uploadFiles/memoterminado/'.$memo.'/memorandum_planeacion_termino.pdf';
                if (Storage::exists($archivo_memo)) {
                    #checamos si hay algún documento, de ser así, procedemos a eliminarlo
                    Storage::delete($archivo_memo);
                }

                $archivo_memo_planeacion_terminado = $request->file('memorandumPositivoFile'); # obtenemos el archivo
                $url_archivo_memo_planeacion_terminado = $this->uploaded_memo_dta_to_planeacion_file($archivo_memo_planeacion_terminado, $memo, 'memoterminado'); #invocamos el método
            }
        } else {
            $url_archivo_memo_planeacion_terminado = null;
        }

        if (!empty($cursoschk)) {
            # vamos a checar sólo a los checkbox checados como propiedad
            if (!empty($cursoschk)) {
                $fecha_ahora = Carbon::now();
                $date = $fecha_ahora->format('Y-m-d'); // fecha
                $numero_memo = $request->get('numero_memo'); // número de memo

                $cerrado_planeacion = [
                    'FECHA' => $date,
                    'NUMERO' => $numero_memo,
                    'MEMORANDUM' => $url_archivo_memo_planeacion_terminado
                ];
                /**
                 * TURNADO_DTA:[“NUMERO”:”XXXXXX”,”FECHA”:” XXXX-XX-XX”]
                 */
                # sólo obtenemos a los que han sido chequeados para poder continuar con la actualización
                $data = explode(",", $cursoschk);
                $comentario = explode(",", $_POST['comentarios_planeacion']);
                foreach(array_combine($data, $comentario) as $key => $comentarios){
                    $comentarios_regreso_unidad = [
                        'OBSERVACION_RETORNO' =>  $comentarios
                    ];
                    \DB::table('tbl_cursos')
                        ->where('id', $key)
                        ->update(['memos' => DB::raw("jsonb_set(memos, '{CERRADO_PLANEACION}','".json_encode($cerrado_planeacion)."'::jsonb)"), 
                        'status' => 'CERRADO', 
                        'turnado' => 'PLANEACION',
                        'observaciones_formato_t' => DB::raw("jsonb_set(observaciones_formato_t, '{OBSERVACION_CERRADO_PLANEACION}', '".json_encode($comentarios_regreso_unidad)."'::jsonb)")]);
                }

            }

            $json = json_encode('DONE');
            return $json;
        }  else {
            $json = json_encode('EMPTYCURSOS');
            return $json;
        }
    }

    protected function generarMemorandum(Request $request)
    {
        // variable pivote
        $generarMemo = $request->get('memorandumGenerado');
        if (isset($generarMemo)) {
            # hacemos un switch...
            switch ($generarMemo) {
                case 'memorandumPositivo':
                    # generamos un switch
                        $value = 'JEFE DE DEPARTAMENTO DE PROGRAMACION Y PRESUPUESTO';
                        $jefdepto = 'JEFE DE DEPARTAMENTO DE CERTIFICACION Y CONTROL';
                        $unidadB = $request->get('unidad_busqueda');
                        $num_memo_planeacion = $request->get('num_memo');
                        // fecha actual
                        $fecha_ahora = Carbon::now();
                        $fecha = $fecha_ahora->format('Y-m-d'); // fecha
                        // arreglo de meses
                        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                        $fechaFormato = Carbon::parse($fecha);
                        $mes = $meses[($fechaFormato->format('n')) - 1];
                        $fecha_ahora_espaniol = $fechaFormato->format('d') . ' de ' . $mes . ' de ' . $fechaFormato->format('Y');
                        // registro de las unidades
                        $reg_unidad = DB::table('tbl_unidades')->select('unidad','dunidad','academico','vinculacion','dacademico','pdacademico','pdunidad','pacademico',
                                        'pvinculacion','jcyc','pjcyc', 'dgeneral', 'pdgeneral')->where('unidad', $unidadB)->first();
                        $directorio = DB::table('directorio')->select('nombre', 'apellidoPaterno', 'apellidoMaterno', 'puesto')->where('puesto', 'LIKE', "%{$value}%")->first();
                        $jefeDepto = DB::table('directorio')->select('nombre', 'apellidoPaterno', 'apellidoMaterno', 'puesto')->where('puesto', 'LIKE', "%{$jefdepto}%")->first();
                        $directorPlaneacion = DB::table('directorio')->select('nombre', 'apellidoPaterno', 'apellidoMaterno', 'puesto')->where('id', 14)->first();
                        $pdf = PDF::loadView('layouts.pdfpages.memorandum_termino_satisfactorio_planeacion', compact('fecha_ahora_espaniol', 'reg_unidad', 'num_memo_planeacion', 'directorio', 'jefeDepto', 'directorPlaneacion'));
                        return $pdf->stream('Memorandum_respuesta_negativa_dta.pdf');
                    break;
                case 'memorandumNegativo':
                    # generamos un switch
                        $value = 'JEFE DE DEPARTAMENTO DE PROGRAMACION Y PRESUPUESTO';
                        $jefdepto = 'JEFE DE DEPARTAMENTO DE CERTIFICACION Y CONTROL';
                        $unidadB = $request->get('unidad_busqueda');
                        $num_memo_planeacion = $request->get('num_memo');
                        // fecha actual
                        $fecha_ahora = Carbon::now();
                        $fecha = $fecha_ahora->format('Y-m-d'); // fecha
                        // arreglo de meses
                        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                        $fechaFormato = Carbon::parse($fecha);
                        $mes = $meses[($fechaFormato->format('n')) - 1];
                        $fecha_ahora_espaniol = $fechaFormato->format('d') . ' de ' . $mes . ' de ' . $fechaFormato->format('Y');
                        // registro de las unidades
                        $reg_unidad = DB::table('tbl_unidades')->select('unidad','dunidad','academico','vinculacion','dacademico','pdacademico','pdunidad','pacademico',
                                        'pvinculacion','jcyc','pjcyc', 'dgeneral', 'pdgeneral')->where('unidad', $unidadB)->first();
                        $directorio = DB::table('directorio')->select('nombre', 'apellidoPaterno', 'apellidoMaterno', 'puesto')->where('puesto', 'LIKE', "%{$value}%")->first();
                        $jefeDepto = DB::table('directorio')->select('nombre', 'apellidoPaterno', 'apellidoMaterno', 'puesto')->where('puesto', 'LIKE', "%{$jefdepto}%")->first();
                        $directorPlaneacion = DB::table('directorio')->select('nombre', 'apellidoPaterno', 'apellidoMaterno', 'puesto')->where('id', 14)->first();
                        $pdf = PDF::loadView('layouts.pdfpages.memorandum_termino_satisfactorio_planeacion', compact('fecha_ahora_espaniol', 'reg_unidad', 'num_memo_planeacion', 'directorio', 'jefeDepto', 'directorPlaneacion'));
                        return $pdf->stream('Memorandum_termino_satisfactorio_planeacion.pdf');
                    break;
                default:
                    # code...
                    break;
            }
        }

    }

    protected function uploaded_memo_dta_to_planeacion_file($file, $memo, $sub)
    {
        $tamanio = $file->getSize(); #obtener el tamaño del archivo del cliente
        $extensionFile = $file->getClientOriginalExtension(); // extension de la imagen
        # nuevo nombre del archivo
        $documentFile = trim("memorandum_respuesta.".$extensionFile);
        $path = '/'.$sub.'/'.$memo.'/'.$documentFile;
        Storage::disk('custom_folder_1')->put($path, file_get_contents($file));
        $documentUrl = Storage::disk('custom_folder_1')->url('/uploadFiles/'.$sub."/".$memo."/".$documentFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $documentUrl;
    }
}
