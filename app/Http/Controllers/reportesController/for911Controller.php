<?php

namespace App\Http\Controllers\reportesController;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\faCADE as PDF;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class for911Controller extends Controller
{
    public function showForm(){
        $id_user = Auth::user()->id;//dd($id_user);
        $id_unidad= Auth::user()->unidad;
        //$id_unidad= "7";//rollos

        $unidades = $unidad = NULL;
        $rol = DB::table('role_user')->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')
        ->WHERE('role_user.user_id', '=', $id_user)->WHERE('roles.slug', 'like', '%unidad%')
            ->value('roles.slug');//dd($rol);
        if ($rol) {
            //dd('si trae unidad');
            $uni = DB::table('tbl_unidades')->select(DB::raw('SUBSTR(cct,1,5) as clave'),DB::raw('SUBSTR(cct,6,10) as cct'),'ubicacion','unidad')->where('id',$id_unidad)->first();
            //dd($uni);
            if($uni->clave=='07EIC'){
                $unidades = DB::table('tbl_unidades')->where('ubicacion',$uni->unidad)->pluck('unidad');
                $tipo= gettype($unidades);
                //dd($unidades);
                //var_dump($unidades);
            }
            else{
                //var_dump($uni->unidad);
                $unidades=$uni->unidad;
                $tipo= gettype($unidades);
                //dd($tipo);
            }

        } else {
            //dd('no trae unidad');
            $unidades = DB::table('tbl_unidades')->groupBy('unidad')->orderBy('unidad')->pluck('unidad');//dd($unidades);
            $tipo= gettype($unidades);//dd($tipo);
            //dd($unidades);
            //var_dump($unidades);
        }
        return view('reportes.911.911formu', compact('unidades','tipo'));
    }

    public function store(Request $request){
        $request->validate([
            'unidades'=>'required',
            'turno'=>'required',
            'fecha_inicio'=>'required',
            'fecha_termino'=>'required'
        ]);
        $unidades= $request->unidades; //dd($unidades);
        $turno= $request->turno; //dd($turno);
        $fecha_inicio=$request->fecha_inicio;
        $fecha_termino=$request->fecha_termino;

        $encabezado='0';
        $consulta_inscritos='0';


//dd($b);
        $sql= DB::table('tbl_cursos as tc')
        ->join('cursos as c','tc.id_curso','=','c.id')
        ->join('especialidades as e','tc.espe','=','e.nombre')
        ->select(DB::raw('count(e.id)'), 'e.clave','e.nombre as especialidad')
        //->select('tc.clave')
        ->where('tc.termino','>=',$fecha_inicio)
        ->where('tc.termino','<=',$fecha_termino)
        ->where('tc.unidad','=',$unidades)
        //->where('tc.hini','>=',$a)
        //->where('tc.hini','<=',$b)
        //->where('tc.status_curso', '!=', 'CANCELADO')
        ->where('tc.status', '=', 'REPORTADO')
        ->groupBy('e.id','e.clave','e.nombre')
        //->groupByRaw('e.clave, e.nombre')
        ->orderBy('e.nombre');
        //->orderBy('tc.clave')
        //->get();
        //dd($sql);

        $temptblinner = DB::raw("(SELECT id_pre, no_control, id_curso, migrante, indigena, etnia FROM alumnos_registro GROUP BY id_pre, no_control, id_curso, migrante, indigena, etnia) as ar");
//dd($temptblinner);
        $consulta=DB::table('tbl_cursos as tc')
        ->leftjoin('tbl_inscripcion as i','tc.id','=','i.id_curso')
        ->leftjoin($temptblinner, function($join)
                    {
                        $join->on('tc.id_curso','=','ar.id_curso');
                        $join->on('i.matricula','=','ar.no_control');
                    })
        ->select(DB::raw("count(extract(year from (age(tc.termino,i.fecha_nacimiento)))) as total_inscritos"), 'tc.espe as especialidad',
        DB::raw("sum(case when extract(year from (age(tc.termino,i.fecha_nacimiento))) < '15' then 1 else 0 end) as total_inscritos1"),
        DB::raw("sum(case when extract(year from (age(tc.termino,i.fecha_nacimiento))) between '15' and '19' then 1 else 0 end) as total_inscritos2"),
        DB::raw("sum(case when extract(year from (age(tc.termino,i.fecha_nacimiento))) between '20' and '24' then 1 else 0 end) as total_inscritos3"),
        DB::raw("sum(case when extract(year from (age(tc.termino,i.fecha_nacimiento))) between '25' and '34' then 1 else 0 end) as total_inscritos4"),
        DB::raw("sum(case when extract(year from (age(tc.termino,i.fecha_nacimiento))) between '35' and '44' then 1 else 0 end) as total_inscritos5"),
        DB::raw("sum(case when extract(year from (age(tc.termino,i.fecha_nacimiento))) between '45' and '54' then 1 else 0 end) as total_inscritos6"),
        DB::raw("sum(case when extract(year from (age(tc.termino,i.fecha_nacimiento))) between '55' and '64' then 1 else 0 end) as total_inscritos7"),
        DB::raw("sum(case when extract(year from (age(tc.termino,i.fecha_nacimiento))) > '64' then 1 else 0 end) as total_inscritos8"),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) < '15' and i.sexo='H' then 1 else 0 end) as insh1 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) between '15' and '19' and i.sexo='H' then 1 else 0 end) as insh2 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) between '20' and '24' and i.sexo='H' then 1 else 0 end) as insh3 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) between '25' and '34' and i.sexo='H' then 1 else 0 end) as insh4 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) between '35' and '44' and i.sexo='H' then 1 else 0 end) as insh5 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) between '45' and '54' and i.sexo='H' then 1 else 0 end) as insh6 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) between '55' and '64' and i.sexo='H' then 1 else 0 end) as insh7 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) > '64' and i.sexo='H' then 1 else 0 end) as insh8 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) > '0' and i.sexo='H' then 1 else 0 end) as insh9 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) < '15' and i.sexo='M' then 1 else 0 end) as insm1 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) between '15' and '19' and i.sexo='M' then 1 else 0 end) as insm2 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) between '20' and '24' and i.sexo='M' then 1 else 0 end) as insm3 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) between '25' and '34' and i.sexo='M' then 1 else 0 end) as insm4 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) between '35' and '44' and i.sexo='M' then 1 else 0 end) as insm5 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) between '45' and '54' and i.sexo='M' then 1 else 0 end) as insm6 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) between '55' and '64' and i.sexo='M' then 1 else 0 end) as insm7 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) > '64' and i.sexo='M' then 1 else 0 end) as insm8 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) > '0' and i.sexo='M' then 1 else 0 end) as insm9 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) < '15' and i.sexo='H' and i.calificacion !='NP' then 1 else 0 end) as iacreh1 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) between '15' and '19' and i.sexo='H' and i.calificacion !='NP' then 1 else 0 end) as iacreh2 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) between '20' and '24' and i.sexo='H' and i.calificacion !='NP' then 1 else 0 end) as iacreh3 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) between '25' and '34' and i.sexo='H' and i.calificacion !='NP' then 1 else 0 end) as iacreh4 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) between '35' and '44' and i.sexo='H' and i.calificacion !='NP' then 1 else 0 end) as iacreh5 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) between '45' and '54' and i.sexo='H' and i.calificacion !='NP' then 1 else 0 end) as iacreh6 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) between '55' and '64' and i.sexo='H' and i.calificacion !='NP' then 1 else 0 end) as iacreh7 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) > '64' and i.sexo='H' and i.calificacion !='NP' then 1 else 0 end) as iacreh8 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) > '0' and i.sexo='H' and i.calificacion !='NP' then 1 else 0 end) as iacreh9 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) < '15' and i.sexo='M' and i.calificacion !='NP' then 1 else 0 end) as iacrem1 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) between '15' and '19' and i.sexo='M' and i.calificacion !='NP' then 1 else 0 end) as iacrem2 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) between '20' and '24' and i.sexo='M' and i.calificacion !='NP' then 1 else 0 end) as iacrem3 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) between '25' and '34' and i.sexo='M' and i.calificacion !='NP' then 1 else 0 end) as iacrem4 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) between '35' and '44' and i.sexo='M' and i.calificacion !='NP' then 1 else 0 end) as iacrem5 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) between '45' and '54' and i.sexo='M' and i.calificacion !='NP' then 1 else 0 end) as iacrem6 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) between '55' and '64' and i.sexo='M' and i.calificacion !='NP' then 1 else 0 end) as iacrem7 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) > '64' and i.sexo='M' and i.calificacion !='NP' then 1 else 0 end) as iacrem8 "),
        DB::raw("sum(case when extract(year from(age(tc.termino, i.fecha_nacimiento))) > '0' and i.sexo='M' and i.calificacion !='NP' then 1 else 0 end) as iacrem9 ")
        )
        ->where('tc.termino','>=',$fecha_inicio)
        ->where('tc.termino','<=',$fecha_termino)
        ->where('tc.unidad','=',$unidades)
        //->where('tc.hini','>=',$a)
        //->where('tc.hini','<=',$b)
        ->where('tc.status_curso', '!=', 'CANCELADO')
        ->where('tc.status', '=', 'REPORTADO')
        ->where('i.status','=','INSCRITO')
        ->groupBy('tc.espe')
        ->orderBy('tc.espe');
        //->get();
        //dd($consulta);

        if($turno=='MATUTINO'){
            /*$encabezado=$sql->where(function ($query) {
                $query->where('tc.hini', 'like', '%a.m.%')
                      ->orWhere('tc.hini', 'like', '%01:00 p.m.%')
                      ->orWhere('tc.hini', 'like', '%01:30 p.m.%')
                      ->orWhere('tc.hini', 'like', '%12:30 p.m.%')
                      ->orWhere('tc.hini', 'like', '%12:00 p.m.%');})->get();
            $consulta_inscritos=$consulta->where(function ($query) {
                $query->where('tc.hini', 'like', '%a.m.%')
                      ->orWhere('tc.hini', 'like', '%01:00 p.m.%')
                      ->orWhere('tc.hini', 'like', '%01:30 p.m.%')
                      ->orWhere('tc.hini', 'like', '%12:30 p.m.%')
                      ->orWhere('tc.hini', 'like', '%12:00 p.m.%');})->get();*/
            $consulta_inscritos= $consulta->whereRaw("cast(replace(hini, '.', '') as time) < '14:00:00' and hini !=''")->get();
            $encabezado= $sql->whereRaw("cast(replace(hini, '.', '') as time) < '14:00:00' and hini !=''")->get();          

        }elseif($turno=='VESPERTINO'){
            /*$encabezado=$sql->where(function ($query) {
                $query->where('tc.hini', 'like', '%p.m.%')
                      ->Where('tc.hini', 'not like', '%01:00 p.m.%')
                      ->where('tc.hini', 'not like', '%12:00 p.m.%')
                      ->where('tc.hini', 'not like', '%12:30 p.m.%')
                      ->where('tc.hini', 'not like', '%01:30 p.m.%')
                      ;})
                      ->get();
            $consulta_inscritos=$consulta->where(function ($query) {
                $query->where('tc.hini', 'like', '%p.m.%')
                      ->Where('tc.hini', 'not like', '%01:00 p.m.%')
                      ->where('tc.hini', 'not like', '%12:00 p.m.%')
                      ->where('tc.hini', 'not like', '%12:00 p.m.%')
                      ->where('tc.hini', 'not like', '%01:30 p.m.%')
                      ;})
                      ->get();*/
            $consulta_inscritos=$consulta->whereRaw("cast(replace(hini, '.', '') as time) >= '14:00:00' and hini !=''")->get();
            $encabezado=$sql->whereRaw("cast(replace(hini, '.', '') as time) >= '14:00:00' and hini !=''")->get();

        }
         //dd($consulta_inscritos);
        if(count($encabezado)==0){return redirect()->route('reportes.911.showForm')->with('success', 'No existen registros');}
    //    dd($encabezado);


        $pdf = PDF::loadView('reportes.911.forna', compact('encabezado','consulta_inscritos','turno','unidades','fecha_inicio','fecha_termino'));
        $pdf->setPaper('A4', 'landscape');
    	//portrait
        //return view('reportes.911.forna', compact('encabezado','consulta_inscritos','turno','unidades','fecha_inicio','fecha_termino'));
    	return $pdf-> stream('forna.pdf');

    }

}