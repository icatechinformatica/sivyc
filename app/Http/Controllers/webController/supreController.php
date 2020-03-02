<?php
// Creado Por Orlando Chavez
namespace App\Http\Controllers\webController;

use App\Models\instructor;
use App\Models\supre;
use App\Models\folio;
use App\Models\curso;
use App\ProductoStock;
use App\Models\cursoValidado;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Redirect,Response;
use App\Models\InstructorPerfil;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class supreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function solicitud_supre_inicio() {
        $supre = new supre();
        $data = $supre::where('id_supre', '!=', '0')->latest()->get();
        return view('layouts.pages.vstasolicitudsupre', compact('data'));
    }

    public function solicitud_formulario() {
        return view('layouts.pages.delegacionadmin');
    }

    public function solicitud_guardar(Request $request) {
        $supre = new supre();
        $folio = new folio();
        $curso_validado = new cursoValidado();

        //Guarda Solicitud
        $supre->unidad_capacitacion = $request->unidad;
        $supre->no_memo = $request->memorandum;
        $supre->fecha = $request->fecha;
        $supre->nombre_para = $request->destino;
        $supre->puesto_para = $request->destino_puesto;
        $supre->nombre_remitente = $request->remitente;
        $supre->puesto_remitente = $request->remitente_puesto;
        $supre->nombre_valida = $request->nombre_valida;
        $supre->puesto_valida = $request->puesto_valida;
        $supre->nombre_elabora = $request->nombre_elabora;
        $supre->puesto_elabora = $request->puesto_elabora;
        $supre->nombre_ccp1 = $request->nombre_ccp1;
        $supre->puesto_ccp1 = $request->puesto_ccp1;
        $supre->nombre_ccp2 = $request->nombre_ccp2;
        $supre->nombre_ccp2 = $request->puesto_ccp2;
        $supre->save();

        $id = $supre->SELECT('id_supre')->WHERE('no_memo', '=', $request->memorandum)->GET();
        //Guarda Folios
        foreach ($request->addmore as $key => $value){
            $folio->folio_validacion = $value['folio'];
            $folio->numero_presupuesto = $value['numeropresupuesto'];
            $folio->iva = $value['iva'];
            $clave = $value['clavecurso'];
            $hora = $curso_validado->SELECT('cursos.horas AS horas')
                    ->WHERE('curso_validado.clave_curso', '=', $clave)
                    ->LEFTJOIN('cursos', 'cursos.id', '=', 'curso_validado.id_curso')
                    ->GET();
            $importe_hora = $value['importe']/$hora;
            $folio->importe_hora = $importe_hora;
            $folio->importa_total = $value['importe'];
            $folio->idsupre = $id;
            $folio->save();
        }

        return redirect()->route('/supre/solicitud/inicio')
                        ->with('success','Solicitud de Suficiencia Presupuestal agregado');
    }

    public function validacion_supre_inicio(){
        return view('layouts.pages.initvalsupre');
    }

    public function validacion(){
        return view('layouts.pages.valsupre');
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
        // almacenamiento de datos
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
}
