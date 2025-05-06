<?php

namespace App\Http\Controllers\Reporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ValidacionServicio;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ValidarServicioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $folio_grupo = '6Y-250020';
        $grupo = DB::table('tbl_cursos')->select('inicio', 'id_especialidad', 'termino')->where('folio_grupo', $folio_grupo)->first();
        $instructores = $this->data_instructores($grupo);
        $servicio = (new ValidacionServicio($instructores));


        // return response()->json([
        //     'resp' => $dataCurso
        //  ], Response::HTTP_CREATED);
        dd($servicio);
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

    public function data_instructores($data){
        $internos = DB::table('instructores as i')->select('i.id')->join('tbl_cursos as c','c.id_instructor','i.id')
        ->where('i.tipo_instructor', 'INTERNO')->where('curso_extra',false)
        ->where(DB::raw("EXTRACT(YEAR FROM c.inicio)"), date('Y', strtotime($data->inicio)))
        ->where(DB::raw("EXTRACT(MONTH FROM c.inicio)"), date('m', strtotime($data->inicio)))
        ->havingRaw('count(*) >= 2')
        ->groupby('i.id');

        $instructores = DB::table(DB::raw('(select id_instructor, id_curso from agenda group by id_instructor, id_curso) as t'))
        ->select(DB::raw('CONCAT("apellidoPaterno", '."' '".' ,"apellidoMaterno",'."' '".',instructores.nombre) as instructor'),'instructores.id', DB::raw('count(id_curso) as total'))
        ->rightJoin('instructores','t.id_instructor','=','instructores.id')
        ->JOIN('instructor_perfil', 'instructor_perfil.numero_control', '=', 'instructores.id')
        ->JOIN('tbl_unidades', 'tbl_unidades.cct', '=', 'instructores.clave_unidad')
        ->JOIN('especialidad_instructores', 'especialidad_instructores.perfilprof_id', '=', 'instructor_perfil.id')
        ->WHERE('estado',true)
        ->WHERE('instructores.status', '=', 'VALIDADO')->where('instructores.nombre','!=','')
        ->WHERE('especialidad_instructores.especialidad_id',$data->id_especialidad)
        //->where('especialidad_instructor_curso.activo', true)
        ->WHERE('fecha_validacion','<',$data->inicio)
        ->WHERE(DB::raw("(fecha_validacion + INTERVAL'1 year')::timestamp::date"),'>=',$data->termino)
        ->whereNotIn('instructores.id', $internos)
        ->groupBy('t.id_instructor','instructores.id')
        ->orderBy('instructor')
        ->get();
        return $instructores;
    }
}
