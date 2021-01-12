<?php

namespace App\Http\Controllers\webController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use App\Models\cerss;
use App\Models\Municipio;
use App\Models\Unidad;

class CerssController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $i = 0;
        $data = cerss::WHERE('id', '!=', '0')->GET();

        foreach ($data as $cadwell)
        {
            $muni[$i] = Municipio::SELECT('muni')->WHERE('id', '=', $cadwell->id_municipio)->FIRST();
            $i++;
        }
        return view('layouts.pages.vstainiciocerss', compact('data','muni'));
    }

    public function create()
    {
        $muni = Municipio::SELECT('id','muni')->WHERE('id_estado', '=', '7')->GET();
        $unidad = Unidad::SELECT('id','unidad')->WHERE('id', '!=', '0')->GET();

        return view('layouts.pages.frmcerss', compact('muni','unidad'));
    }

    public function save(Request $request)
    {
        $cerss = new cerss();

        $cerss->nombre = $request->nombre;
        $cerss->direccion = $request->direccion;
        $cerss->id_municipio = $request->municipio;
        $cerss->titular = $request->titular;
        $cerss->telefono = $request->telefono;
        $cerss->telefono2 = $request->telefono2;
        $cerss->id_unidad = $request->unidad;
        $cerss->activo = true;
        $cerss->iduser_create = auth()->user()->id;
        $cerss->save();

        return redirect()->route('cerss.inicio')
                ->with('success','CERSS agregado');
    }

    public function update($id)
    {
        $data = cerss::WHERE('id', '=', $id)->FIRST();
        $munisel = Municipio::SELECT('id','muni')->WHERE('id', '=', $data->id_municipio)->FIRST();
        $muni = Municipio::SELECT('id','muni')->WHERE('id', '!=', $data->id_municipio)->WHERE('id_estado', '=', '7')
                            ->GET();

        $unidadsel = Unidad::SELECT('id','unidad')->WHERE('id', '=', $data->id_unidad)->FIRST();
        $unidad = Unidad::SELECT('id','unidad')->WHERE('id', '!=', $data->id_unidad)->GET();

        return view('layouts.pages.frmcerssupdate', compact('data','munisel','muni','unidadsel','unidad'));
    }

    public function update_save(Request $request)
    {
        $mod = cerss::find($request->idcerss);
        if($request->status != NULL)
        {
            $mod->activo = true;
        }
        else
        {
            $mod->activo = false;

        }

        $mod->nombre = $request->nombre;
        $mod->direccion = $request->direccion;
        $mod->id_municipio = $request->municipio;
        $mod->titular = $request->titular;
        $mod->telefono = $request->telefono;
        $mod->telefono2 = $request->telefono2;
        $mod->id_unidad = $request->unidad;
        $mod->iduser_update = auth()->user()->id;
        $mod->save();

        return redirect()->route('cerss.inicio')
                ->with('success','CERSS Modificado');
    }
}
