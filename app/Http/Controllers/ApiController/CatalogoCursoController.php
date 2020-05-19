<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\api\CatalogoCurso;

class CatalogoCursoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $catalogoCursos = new CatalogoCurso();
        $cursos_catalogo = $catalogoCursos->all();
        return response()->json($cursos_catalogo, 200);
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
            //code...
            $catalogo = new CatalogoCurso();
            $catalogo->especialidad = $request->especialidad;
            $catalogo->nombre_curso = $request->nombre_curso;
            $catalogo->modalidad = $request->modalidad;
            $catalogo->horas = $request->horas;
            $catalogo->clasificacion = $request->clasificacion;
            $catalogo->costo = $request->costo;
            $catalogo->duracion = $request->duracion;
            $catalogo->objetivo = $request->objetivo;
            $catalogo->perfil = $request->perfil;
            $catalogo->solicitud_autorizacion = $request->solicitud_autorizacion;
            $catalogo->fecha_validacion = $request->fecha_validacion;
            $catalogo->memo_validacion = $request->memo_validacion;
            $catalogo->memo_actualizacion = $request->memo_actualizacion;
            $catalogo->fecha_actualizacion = $request->fecha_actualizacion;
            $catalogo->unidad_amovil = $request->unidad_amovil;
            $catalogo->descripcion = $request->descripcion;
            $catalogo->no_convenio = $request->no_convenio;
            $catalogo->id_especialidad = $request->id_especialidad;
            $catalogo->save();

            return response()->json(['success' => 'El Catalogo de Cursos se cargo exitosamente!'], 200);
        } catch (Exception $th) {
            //throw $th;
            return response()->json(['error' => $th->getMessage()], 501);
        }
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
        return response()->json(['success' => 'listo'], 200);
        // actualizar
        try {
            //code...
            $catalogoCursos = new CatalogoCurso();
            $catalogoCursos->whereId($id)->update($request->all());
            return response()->json(['success' => 'Catalogo de cursos actualizado exitosamente'], 200);
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
