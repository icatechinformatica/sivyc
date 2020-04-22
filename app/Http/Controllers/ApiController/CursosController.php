<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\api\Curso;
use Illuminate\Http\Response;

class CursosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $Curso= new Curso();
        $cursos = $Curso->all();
        return response()->json($cursos, 200);
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
            //code
            /*$validador = Validator::make($request->all(), [

            ]);*/

                # enviar o generar codigo que si funciona
                $Curso= new Curso();
                $Curso->id = $request->id;
                $Curso->cct = $request->cct;
                $Curso->unidad = $request->unidad;
                $Curso->nombre = $request->nombre;
                $Curso->curp = $request->curp;
                $Curso->rfc = $request->rfc;
                $Curso->clave = $request->clave;
                $Curso->grupo = $request->grupo;
                $Curso->mvalida = $request->mvalida;
                $Curso->mod = $request->mod;
                $Curso->turno = $request->turno;
                $Curso->area = $request->area;
                $Curso->espe = $request->espe;
                $Curso->curso = $request->curso;
                $Curso->inicio = $request->inicio;
                $Curso->termino = $request->termino;
                $Curso->dia = $request->dia;
                $Curso->pini = $request->pini;
                $Curso->pfin = $request->pfin;
                $Curso->dura = $request->dura;
                $Curso->hini = $request->hini;
                $Curso->hfin = $request->hfin;
                $Curso->horas = $request->horas;
                $Curso->ciclo = $request->ciclo;
                $Curso->plantel = $request->plantel;
                $Curso->depen = $request->depen;
                $Curso->muni = $request->muni;
                $Curso->sector = $request->sector;
                $Curso->programa = $request->programa;
                $Curso->nota = $request->nota;
                $Curso->munidad = $request->munidad;
                $Curso->efisico = $request->efisico;
                $Curso->cespecifico = $request->cespecifico;
                $Curso->mpaqueteria = $request->mpaqueteria;
                $Curso->mexoneracion = $request->mexoneracion;
                $Curso->hombre = $request->hombre;
                $Curso->mujer = $request->mujer;
                $Curso->tipo = $request->tipo;
                $Curso->fcespe = $request->fcespe;
                $Curso->cgeneral = $request->cgeneral;
                $Curso->fcgen = $request->fcgen;
                $Curso->opcion = $request->opcion;
                $Curso->motivo = $request->motivo;
                $Curso->cp = $request->cp;
                $Curso->ze = $request->ze;
                $Curso->id_curso = $request->id_curso;
                $Curso->id_instructor = $request->id_instructor;
                $Curso->save();

                return response()->json(['success' => 'Curso se cargo exitosamente en la base de datos'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 501);
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
        // nueva edicion en el controlador del api
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
        // actualizando
        try {
            $Curso= new Curso();
            $Curso->whereId($id)->update($request);
            return response()->json(['success' => 'Curso actualizado exitosamente'], 200);
        } catch(Exception $e) {
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
