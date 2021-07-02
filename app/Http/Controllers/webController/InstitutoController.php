<?php

namespace App\Http\Controllers\webController;

use App\Http\Controllers\Controller;
use App\Models\Instituto;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InstitutoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $instituto = DB::table('tbl_instituto')
            ->leftJoin('users', 'tbl_instituto.iduser_created', '=', 'users.id')
            ->leftJoin('users as usuarios', 'tbl_instituto.iduser_updated', '=', 'usuarios.id')
            ->select('tbl_instituto.*', 'users.name AS nameCreated', 'usuarios.name AS nameUpdated')
            ->first();
        // dd($instiuto);
        // dd($instituto);

        return view('layouts.pages.vstainicioinstituto', compact('instituto'));
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
        $instituto = Instituto::find($request->idarea);
        $date = new DateTime();

        if ($instituto == null) {
            $instituto = new Instituto();
            $instituto->iduser_created = Auth::user()->id;
            $instituto->created_at = $date;
        }

        $instituto->name = $request->nombre;
        $instituto->direccion = $request->direccion;
        $instituto->telefono = $request->telefono;
        $instituto->url = $request->url;
        $instituto->correo = $request->email;
        $instituto->distintivo = $request->distintivo;
        $instituto->updated_at = $date;
        $instituto->iduser_updated = Auth::user()->id;
        $instituto->titular = $request->titular;
        $instituto->cargo = $request->cargo;
        $instituto->correo_titular = $request->email_titular;

        $instituto->save();
        return redirect()->route('instituto.inicio');
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
