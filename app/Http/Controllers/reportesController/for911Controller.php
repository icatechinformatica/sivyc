<?php

namespace App\Http\Controllers\reportesController;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PDF;
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
        $instruc_h = '0';
        $instruc_m = '0';
        $vulnerav_h = '0';
        $vulnerav_m = '0';


//dd($b);
        // $sql= DB::table('tbl_cursos as tc')
        // ->join('cursos as c','tc.id_curso','=','c.id')
        // ->join('especialidades as e','tc.espe','=','e.nombre')
        // ->select(DB::raw('count(e.id)'), 'e.clave','e.nombre as especialidad')
        // //->select('tc.clave')

        // ->where('tc.termino','>=',$fecha_inicio)
        // ->where('tc.termino','<=',$fecha_termino)
        // ->where('tc.unidad','=',$unidades)
        // //->where('tc.hini','>=',$a)
        // //->where('tc.hini','<=',$b)
        // //->where('tc.status_curso', '!=', 'CANCELADO')
        // ->where('tc.status', '=', 'REPORTADO')
        // ->groupBy('e.id','e.clave','e.nombre')
        // //->groupByRaw('e.clave, e.nombre')
        // ->orderBy('e.nombre');
        // //->orderBy('tc.clave')
        // //->get();

        // $sql= DB::table('tbl_cursos as tc')
        // ->join('cursos as c','tc.id_curso','=','c.id')
        // ->leftjoin('especialidades as e','tc.espe','=','e.nombre')
        // ->select(DB::raw('count(tc.espe)'), 'e.clave','tc.espe as especialidad')
        // //->select('tc.clave')
        // ->where('tc.termino','>=',$fecha_inicio)
        // ->where('tc.termino','<=',$fecha_termino)
        // ->where('tc.unidad','=',$unidades)
        // //->where('tc.hini','>=',$a)
        // //->where('tc.hini','<=',$b)
        // //->where('tc.status_curso', '!=', 'CANCELADO')
        // ->where('tc.status', '=', 'REPORTADO')
        // ->groupBy('tc.espe','e.clave')
        // //->groupByRaw('e.clave, e.nombre')
        // ->orderBy('tc.espe');

        //NEW VERSION
        $sql = DB::table('tbl_cursos as tc')
        ->join('cursos as c', 'tc.id_curso', '=', 'c.id')
        ->leftjoin('especialidades as e', 'tc.espe', '=', 'e.nombre')
        ->select(DB::raw('count(tc.espe)'), 'e.clave', 'tc.espe as especialidad')
        ->where('tc.termino', '>=', $fecha_inicio)
        ->where('tc.termino', '<=', $fecha_termino)
        ->where('tc.unidad', '=', $unidades)
        ->whereIn('tc.status', ['REPORTADO', 'TURNADO_PLANEACION'])
        ->groupBy('tc.espe', 'e.clave')
        ->orderBy('tc.espe');



        $consulta=DB::table('tbl_cursos as tc')
        ->leftjoin('tbl_inscripcion as i','tc.id','=','i.id_curso')
        ->select(DB::raw("count(extract(year from (age(tc.inicio,i.fecha_nacimiento)))) as total_inscritos"), 'tc.espe as especialidad',
        DB::raw("sum(case when extract(year from (age(tc.inicio,i.fecha_nacimiento))) < '15' then 1 else 0 end) as total_inscritos1"),
        DB::raw("sum(case when extract(year from (age(tc.inicio,i.fecha_nacimiento))) between '15' and '19' then 1 else 0 end) as total_inscritos2"),
        DB::raw("sum(case when extract(year from (age(tc.inicio,i.fecha_nacimiento))) between '20' and '24' then 1 else 0 end) as total_inscritos3"),
        DB::raw("sum(case when extract(year from (age(tc.inicio,i.fecha_nacimiento))) between '25' and '34' then 1 else 0 end) as total_inscritos4"),
        DB::raw("sum(case when extract(year from (age(tc.inicio,i.fecha_nacimiento))) between '35' and '44' then 1 else 0 end) as total_inscritos5"),
        DB::raw("sum(case when extract(year from (age(tc.inicio,i.fecha_nacimiento))) between '45' and '54' then 1 else 0 end) as total_inscritos6"),
        DB::raw("sum(case when extract(year from (age(tc.inicio,i.fecha_nacimiento))) between '55' and '64' then 1 else 0 end) as total_inscritos7"),
        DB::raw("sum(case when extract(year from (age(tc.inicio,i.fecha_nacimiento))) > '64' then 1 else 0 end) as total_inscritos8"),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) < '15' and i.sexo='H' then 1 else 0 end) as insh1 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) between '15' and '19' and i.sexo='H' then 1 else 0 end) as insh2 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) between '20' and '24' and i.sexo='H' then 1 else 0 end) as insh3 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) between '25' and '34' and i.sexo='H' then 1 else 0 end) as insh4 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) between '35' and '44' and i.sexo='H' then 1 else 0 end) as insh5 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) between '45' and '54' and i.sexo='H' then 1 else 0 end) as insh6 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) between '55' and '64' and i.sexo='H' then 1 else 0 end) as insh7 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) > '64' and i.sexo='H' then 1 else 0 end) as insh8 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) > '0' and i.sexo='H' then 1 else 0 end) as insh9 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) < '15' and i.sexo='M' then 1 else 0 end) as insm1 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) between '15' and '19' and i.sexo='M' then 1 else 0 end) as insm2 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) between '20' and '24' and i.sexo='M' then 1 else 0 end) as insm3 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) between '25' and '34' and i.sexo='M' then 1 else 0 end) as insm4 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) between '35' and '44' and i.sexo='M' then 1 else 0 end) as insm5 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) between '45' and '54' and i.sexo='M' then 1 else 0 end) as insm6 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) between '55' and '64' and i.sexo='M' then 1 else 0 end) as insm7 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) > '64' and i.sexo='M' then 1 else 0 end) as insm8 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) > '0' and i.sexo='M' then 1 else 0 end) as insm9 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) < '15' and i.sexo='H' and i.calificacion !='NP' then 1 else 0 end) as iacreh1 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) between '15' and '19' and i.sexo='H' and i.calificacion !='NP' then 1 else 0 end) as iacreh2 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) between '20' and '24' and i.sexo='H' and i.calificacion !='NP' then 1 else 0 end) as iacreh3 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) between '25' and '34' and i.sexo='H' and i.calificacion !='NP' then 1 else 0 end) as iacreh4 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) between '35' and '44' and i.sexo='H' and i.calificacion !='NP' then 1 else 0 end) as iacreh5 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) between '45' and '54' and i.sexo='H' and i.calificacion !='NP' then 1 else 0 end) as iacreh6 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) between '55' and '64' and i.sexo='H' and i.calificacion !='NP' then 1 else 0 end) as iacreh7 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) > '64' and i.sexo='H' and i.calificacion !='NP' then 1 else 0 end) as iacreh8 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) > '0' and i.sexo='H' and i.calificacion !='NP' then 1 else 0 end) as iacreh9 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) < '15' and i.sexo='M' and i.calificacion !='NP' then 1 else 0 end) as iacrem1 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) between '15' and '19' and i.sexo='M' and i.calificacion !='NP' then 1 else 0 end) as iacrem2 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) between '20' and '24' and i.sexo='M' and i.calificacion !='NP' then 1 else 0 end) as iacrem3 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) between '25' and '34' and i.sexo='M' and i.calificacion !='NP' then 1 else 0 end) as iacrem4 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) between '35' and '44' and i.sexo='M' and i.calificacion !='NP' then 1 else 0 end) as iacrem5 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) between '45' and '54' and i.sexo='M' and i.calificacion !='NP' then 1 else 0 end) as iacrem6 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) between '55' and '64' and i.sexo='M' and i.calificacion !='NP' then 1 else 0 end) as iacrem7 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) > '64' and i.sexo='M' and i.calificacion !='NP' then 1 else 0 end) as iacrem8 "),
        DB::raw("sum(case when extract(year from(age(tc.inicio, i.fecha_nacimiento))) > '0' and i.sexo='M' and i.calificacion !='NP' then 1 else 0 end) as iacrem9 ")
        )
        ->where('tc.termino','>=',$fecha_inicio)
        ->where('tc.termino','<=',$fecha_termino)
        ->where('tc.unidad','=',$unidades)
        // ->where('tc.status_curso', '!=', 'CANCELADO')
        // ->where('tc.status', '=', 'REPORTADO')
        ->whereIn('tc.status', ['REPORTADO', 'TURNADO_PLANEACION'])
        ->where('i.status','=','INSCRITO')
        ->groupBy('tc.espe')
        ->orderBy('tc.espe');





        if($turno=='MATUTINO'){


            $consulta_inscritos= $consulta->whereRaw("cast(replace(hini, '.', '') as time) < '14:00:00' and hini !=''")->get();
            $encabezado= $sql->whereRaw("cast(replace(hini, '.', '') as time) < '14:00:00' and hini !=''")->get();
            // $instructores= $results->whereRaw("cast(replace(hini, '.', '') as time) < '14:00:00' and hini !=''")->get();

            #OBTENEMOS EL PERSONAL DOCENTE QUE IMPARTE EL CURSO
            $instruc_h = $this->personalDocente($unidades, $fecha_inicio, $fecha_termino, 'MASCULINO', true);
            $instruc_m = $this->personalDocente($unidades, $fecha_inicio, $fecha_termino, 'FEMENINO', true);


            #OBETEMOS LA CONSULTA DE DISCAPACIDADES DE LOS ALUMNOS
            $vulnerav_h = $this->alumnoVulnerable($unidades, $fecha_inicio, $fecha_termino, 'H', 'DIAS');
            $vulnerav_m = $this->alumnoVulnerable($unidades, $fecha_inicio, $fecha_termino, 'M', 'DIAS');
            $encabezado= $sql->whereRaw("cast(replace(hini, '.', '') as time) < '14:00:00' and hini !=''")->get();
            // $instructores= $results->whereRaw("cast(replace(hini, '.', '') as time) < '14:00:00' and hini !=''")->get();

            #OBETEMOS LA CONSULTA DE DISCAPACIDADES DE LOS ALUMNOS
            $vulnerav_h = $this->alumnoVulnerable($unidades, $fecha_inicio, $fecha_termino, 'H', 'DIAS');
            $vulnerav_m = $this->alumnoVulnerable($unidades, $fecha_inicio, $fecha_termino, 'M', 'DIAS');

        }elseif($turno=='VESPERTINO'){

            $consulta_inscritos=$consulta->whereRaw("cast(replace(hini, '.', '') as time) >= '14:00:00' and hini !=''")->get();
            $encabezado=$sql->whereRaw("cast(replace(hini, '.', '') as time) >= '14:00:00' and hini !=''")->get();

            #OBTENEMOS EL PERSONAL DOCENTE QUE IMPARTE EL CURSO
            $instruc_h = $this->personalDocente($unidades, $fecha_inicio, $fecha_termino, 'MASCULINO', false);
            $instruc_m = $this->personalDocente($unidades, $fecha_inicio, $fecha_termino, 'FEMENINO', false);

            #OBETEMOS LA CONSULTA DE DISCAPACIDADES DE LOS ALUMNOS
            $vulnerav_h = $this->alumnoVulnerable($unidades, $fecha_inicio, $fecha_termino, 'H', 'TARDES');
            $vulnerav_m = $this->alumnoVulnerable($unidades, $fecha_inicio, $fecha_termino, 'M', 'TARDES');

        }

        if(count($encabezado)==0){return redirect()->route('reportes.911.showForm')->with('success', 'No existen registros');}
        $pdf = PDF::loadView('reportes.911.forna', compact('encabezado','consulta_inscritos','turno','unidades','fecha_inicio',
        'fecha_termino', 'instruc_h', 'instruc_m', 'vulnerav_h', 'vulnerav_m'));
        $pdf->setPaper('A4', 'landscape');
    	return $pdf-> stream('forna.pdf');

    }

    private function personalDocente($unidades, $fecha_inicio, $fecha_termino, $sexo, $isMorning)
    {

        #consults made by Jose Luis Moreno Arcos
        // $consulta = DB::table(DB::raw("(
        //     SELECT
        //         tc.id_instructor,
        //         cr.perfil_profesional,
        //         ROW_NUMBER() OVER (PARTITION BY tc.id_instructor ORDER BY tc.termino DESC) AS rn
        //     FROM tbl_cursos AS tc
        //     JOIN criterio_pago AS cr ON tc.cp = cr.id
        //     WHERE tc.unidad = '$unidades'
        //         AND tc.termino >= '$fecha_inicio'
        //         AND tc.termino <= '$fecha_termino'
        //         AND tc.status_curso != 'CANCELADO'
        //         AND tc.status = 'REPORTADO'
        //         AND tc.instructor_sexo = '$sexo'
        //         " . ($isMorning ? "
        //             AND (tc.hini LIKE '%a.m.' OR tc.hini = '12:00 p.m.' OR tc.hini = '1:00 p.m.' OR tc.hini = '12:30 p.m.' OR tc.hini = '1:30 p.m.')
        //         " : "
        //             AND ((tc.hini LIKE '%p.m.' AND tc.hini NOT IN ('12:00 p.m.', '12:30 p.m.', '1:00 p.m.', '1:30 p.m.')))
        //         ") . "
        // ) ranked_cursos"))
        //     ->select('perfil_profesional', DB::raw('COUNT(*) AS cantidad'))
        //     ->where('rn', 1)
        //     ->groupBy('perfil_profesional')
        //     ->get();

        // $consulta = DB::table(DB::raw("(
        //     SELECT
        //         tc.id_instructor,
        //         cr.perfil_profesional,
        //         cr.id,
        //         ROW_NUMBER() OVER (PARTITION BY tc.id_instructor ORDER BY tc.termino DESC) AS rn
        //     FROM tbl_cursos AS tc
        //     JOIN criterio_pago AS cr ON tc.cp = cr.id
        //     WHERE tc.unidad = '$unidades'
        //         AND tc.termino >= '$fecha_inicio'
        //         AND tc.termino <= '$fecha_termino'
        //         AND tc.instructor_sexo = '$sexo'
        //         " . ($isMorning ? "
        //             AND (tc.hini LIKE '%a.m.' OR tc.hini = '12:00 p.m.' OR tc.hini = '01:00 p.m.' OR tc.hini = '12:30 p.m.' OR tc.hini = '01:30 p.m.')
        //         " : "
        //             AND ((tc.hini LIKE '%p.m.' AND tc.hini NOT IN ('12:00 p.m.', '12:30 p.m.', '01:00 p.m.', '01:30 p.m.')))
        //         ") . "
        //         AND tc.status IN ('REPORTADO', 'TURNADO_PLANEACION')
        // ) ranked_cursos"))
        //     ->select('perfil_profesional', 'id', DB::raw('COUNT(*) AS cantidad'))
        //     ->where('rn', 1)
        //     ->groupBy('perfil_profesional', 'id')
        //     ->get();

        $consulta = DB::select("
            SELECT cp, COUNT(*) AS cantidad
            FROM (
                SELECT
                    curp,
                    cp,
                    ROW_NUMBER() OVER (PARTITION BY curp ORDER BY termino DESC) AS rn
                FROM tbl_cursos
                WHERE unidad = '$unidades'
                AND termino >= '$fecha_inicio'
                AND termino <= '$fecha_termino'
                AND status IN ('REPORTADO', 'TURNADO_PLANEACION')
                AND instructor_sexo = '$sexo'
                " . ($isMorning ? "
                    AND (hini LIKE '%a.m.' OR hini = '12:00 p.m.' OR hini = '01:00 p.m.' OR hini = '12:30 p.m.' OR hini = '01:30 p.m.')
                " : "
                    AND ((hini LIKE '%p.m.' AND hini NOT IN ('12:00 p.m.', '12:30 p.m.', '01:00 p.m.', '01:30 p.m.')))
                ") . "
            ) AS ranked_cursos
            WHERE rn = 1
            GROUP BY cp
        ");

        #PROCESAMOS LA CONSULTA PARA MANDARLA A LA VISTA
        $prim_i = $prim = $secu = $bach = $profesional = $maestria = $doctorado = $subtotal = 0;
        $prof_trunc = $prof_pasante = $maestria_pasante = $docto_pasante = $prof_cert_comp = 0;

        foreach ($consulta as  $instruc) {
            $subtotal += $instruc->cantidad;
            if ($instruc->cp == 1) $prim_i = $instruc->cantidad;
            if ($instruc->cp == 2) $prim = $instruc->cantidad;
            if ($instruc->cp == 3) $secu = $instruc->cantidad;
            if ($instruc->cp == 4) $bach = $instruc->cantidad;
            if ($instruc->cp == 7) $profesional = $instruc->cantidad;
            if ($instruc->cp == 9) $maestria = $instruc->cantidad;
            if ($instruc->cp == 11) $doctorado = $instruc->cantidad;
            #otros
            if ($instruc->cp == 5) $prof_trunc = $instruc->cantidad;
            if ($instruc->cp == 6) $prof_pasante = $instruc->cantidad;
            if ($instruc->cp == 8) $maestria_pasante = $instruc->cantidad;
            if ($instruc->cp == 10) $docto_pasante = $instruc->cantidad;
            if ($instruc->cp == 12) $prof_cert_comp = $instruc->cantidad;
        }

        $array_instruc = array("primaria_inc" => $prim_i, "primaria" => $prim, "secundaria" => $secu,
                            "bachiller" => $bach, "licenciatura" => $profesional, "maestria" => $maestria,
                            "doctorado" => $doctorado, "prof_trunco" => $prof_trunc, "prof_pasante" => $prof_pasante,
                            "maestria_pasante" => $maestria_pasante, 'doctorado_pasante' => $docto_pasante,
                            'prof_cert_comp' => $prof_cert_comp,  "subtotal" => $subtotal);

        return $array_instruc;

    }

    private function alumnoVulnerable($unidades, $fecha_inicio, $fecha_termino, $sexo, $turno){
        #consults made by Jose Luis Moreno Arcos
        $discap = DB::table('tbl_cursos as tc')
        ->join('tbl_inscripcion as i', 'tc.id', '=', 'i.id_curso')
        ->select(
            DB::raw('COUNT(CASE WHEN (i.id_gvulnerable->>0)::text = \'18\' THEN 1 ELSE NULL END) AS dis_ver'),
            DB::raw('COUNT(CASE WHEN (i.id_gvulnerable->>0)::text = \'19\' THEN 1 ELSE NULL END) AS dis_oir'),
            DB::raw('COUNT(CASE WHEN (i.id_gvulnerable->>0)::text = \'21\' THEN 1 ELSE NULL END) AS dis_motriz'),
            DB::raw('COUNT(CASE WHEN (i.id_gvulnerable->>0)::text = \'22\' THEN 1 ELSE NULL END) AS dis_mental'),
            'tc.hini'
        )
        ->where('tc.unidad', $unidades)
        ->where('i.sexo', $sexo)
        ->whereBetween('tc.termino', [$fecha_inicio, $fecha_termino])
        ->where('tc.status_curso', '!=', 'CANCELADO')
        ->where('tc.status', 'REPORTADO')
        ->groupBy('tc.hini')
        ->get();

        if ($turno == 'DIAS') {
            $resulfilter = $discap->filter(function ($item) {
                $hini = $item->hini;
                return !empty($hini) && strtotime(str_replace('.', '', $hini)) < strtotime('14:00:00');
            })->values();

        } else if ($turno == 'TARDES'){
            $resulfilter = $discap->filter(function ($item) {
                $hini = $item->hini;
                return !empty($hini) && strtotime(str_replace('.', '', $hini)) >= strtotime('14:00:00');
            })->values();
        }

        #HACEMOS EL CONTEO PARA ENVIARLO DIRECTO A LA VISTA
        $sumatodos = $discver = $discoir = $discmot = $discment = 0;
        foreach ($resulfilter as $valor){
            $discver += $valor->dis_ver;
            $discoir += $valor->dis_oir;
            $discmot += $valor->dis_motriz;
            $discment += $valor->dis_mental;
            $sumatodos = $discver + $discoir + $discmot + $discment;
        }
        $array_disc = array("disc_ver" => $discver, "disc_oir" => $discoir, "disc_motriz" => $discmot,
                            "disc_mental" => $discment, "totalreg" => $sumatodos);


        return $array_disc;


    }

}
