<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\api\Instructor;

class InstructoresController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $instructores = Instructor::WHERE('instructores.status', '=', 'Validado')
        ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'instructores.id_especialidad')
        ->LEFTJOIN('tbl_unidades', 'tbl_unidades.cct', '=', 'instructores.clave_unidad')->GET([
            'instructores.id', 'instructores.numero_control', 'instructores.nombre', 'instructores.apellidoPaterno', 'instructores.apellidoMaterno',
            'instructores.rfc', 'instructores.curp', 'instructores.sexo', 'instructores.estado_civil', 'instructores.fecha_nacimiento', 'instructores.entidad', 'instructores.municipio',
            'instructores.asentamiento', 'instructores.domicilio', 'instructores.telefono', 'instructores.correo', 'instructores.banco', 'instructores.no_cuenta',
            'instructores.interbancaria', 'instructores.folio_ine','instructores.id_especialidad',
            'instructores.tipo_honorario', 'instructores.archivo_ine', 'instructores.archivo_domicilio', 'instructores.archivo_curp',
            'instructores.archivo_alta', 'instructores.archivo_bancario', 'instructores.archivo_fotografia', 'instructores.archivo_estudios',
            'instructores.archivo_otraid', 'instructores.status', 'instructores.rechazo', 'instructores.clave_unidad',
            'especialidades.nombre AS nombre_especialidad', 'tbl_unidades.unidad AS unidades', 'instructores.motivo'
        ]);
        return response()->json($instructores, 200);
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
        try {
            //instructores
            $instructor = new Instructor();
            $instructor->nombre = $request->nombre;
            $instructor->apellido_paterno = $request->apellido_paterno;
            $instructor->apellido_materno = $request->apellido_materno;
            $instructor->curp = $request->curp;
            $instructor->rfc = $request->rfc;
            $instructor->cv = $request->cv;

            $instructor->save();

            return response()->json(['success' => 'Se ha generado un instructor exitosamente'], 200);

        } catch (Exception $th) {
            return response()->json(['error' => $th->getMessage()], 501);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($nombre,$apaterno,$amaterno)
    {
        return Instructor::where([['nombre','=',$nombre],['apellido_paterno','=',$apaterno],['apellido_materno','=',$amaterno]])->get();
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
        // actualizar el registro del instructor
        try {
            //code...
            $instructor= new Instructor();
            $instructor->whereId($id)->update($request->all());
            return response()->json(['success' => 'Instructor se ha actualizado exitosamente'], 200);
        } catch (Exception $e) {
            //throw $th;
            return response()->json(['error' => $e->getMessage()], 501);
        }
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
}
