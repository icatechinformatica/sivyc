<?php
// Creado Por Orlando Chavez
namespace App\Http\Controllers\webController;

use App\Models\instructor;
use App\Models\supre;
use App\Models\folio;
use App\Models\tbl_curso;
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
use App\Models\tablaFolio;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

use function PHPSTORM_META\type;

class supreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function solicitud_supre_inicio() {
        $supre = new supre();
        $data = $supre::where('id', '!=', '0')->latest()->get();
        return view('layouts.pages.vstasolicitudsupre', compact('data'));
    }

    public function frm_formulario() {
        return view('layouts.pages.delegacionadmin');
    }

    public function store(Request $request) {
        $supre = new supre();
        $curso_validado = new tbl_curso();

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
        $supre->puesto_ccp2 = $request->puesto_ccp2;
        $supre->status = 'En Proceso';
        $supre->save();

       $id = $supre->SELECT('id')->WHERE('no_memo', '=', $request->memorandum)->FIRST();

        //Guarda Folios
        foreach ($request->addmore as $key => $value){
            $folio = new folio();
            $folio->folio_validacion = $value['folio'];
            $folio->numero_presupuesto = $value['numeropresupuesto'];
            $folio->iva = $value['iva'];
            $clave = $value['clavecurso'];
            $hora = $curso_validado->SELECT('tbl_cursos.horas','tbl_cursos.id')
                    ->WHERE('tbl_cursos.clave', '=', $clave)
                    ->FIRST();
            $importe = $value['importe'];
            $importe_hora = $importe / $hora->horas;
            $folio->importe_hora = $importe_hora;
            $folio->importe_total = $value['importe'];
            $folio->id_supre = $id->id;
            $folio->id_cursos = $hora->id;
            $folio->save();
        }

        return redirect()->route('supre-inicio')
                        ->with('success','Solicitud de Suficiencia Presupuestal agregado');
    }

    public function solicitud_modificar($id)
    {
        $supre = new supre();
        $folio = new folio();

        $getsupre = $supre::WHERE('id', '=', $id)->FIRST();
        $getfolios = $folio::WHERE('id_supre','=', $getsupre->id)->GET();
        return view('layouts.pages.modsupre',compact('getsupre','getfolios'));
    }

    public function solicitud_mod_guardar(Request $request)
    {
        $supre = new supre();
        $curso_validado = new tbl_curso();


        $validData = $request->validate([
            'unidad_capacitacion' => 'required',
            'no_memo' => 'required',
            'fecha' => 'required',
            'nombre_para' => 'required',
            'puesto_para' => 'required',
            'nombre_remitente' => 'required',
            'puesto_remitente' => 'required',
            'nombre_valida' => 'required',
            'puesto_valida' => 'required',
            'nombre_elabora' => 'required',
            'puesto_elabora' => 'required',
            'nombre_ccp1' => 'required',
            'puesto_ccp1' => 'required',
            'nombre_ccp2' => 'required',
            'puesto_ccp2' => 'required',
        ]);


        foreach ($request->addmore as $key => $value){
            $foliomod = new folio();
            $curso = new tbl_curso();
            dd($value);
            $clave = $curso::SELECT('clave')->WHERE('id', '=', $value['id_cursos']);
            folio::DELETE('id_cursos','=',$value['id_cursos']);
            $foliomod->folio_validacion = $value['folio'];
            $foliomod->numero_presupuesto = $value['numeropresupuesto'];
            $foliomod->iva = $value['iva'];
            $clave = $clave->clave;
            $hora = $curso_validado->SELECT('tbl_cursos.horas','tbl_cursos.id')
                    ->WHERE('tbl_cursos.clave', '=', $clave)
                    ->FIRST();
            $importe = $value['importe'];
            $importe_hora = $importe / $hora->horas;
            $foliomod->importe_hora = $importe_hora;
            $foliomod->importe_total = $value['importe'];
            $foliomod->id_supre = $request->id_supre;
            $foliomod->id_cursos = $hora->id;
            $foliomod->save();
        }

        return redirect()->route('supre-inicio')
                        ->with('success','Solicitud de Suficiencia Presupuestal agregado');
    }

    public function validacion_supre_inicio(){
        return view('layouts.pages.initvalsupre');
    }

    public function validacion(){
        return view('layouts.pages.valsupre');
    }
}
