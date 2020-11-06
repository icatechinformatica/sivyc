<?php

namespace App\Http\Controllers\supervisionController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\calidad_encuestas;
use App\Models\calidad_respuestas;
use App\Models\calidad_respuestas_alumnos;
use App\Models\Inscripcion;
use App\Models\supervision\tokenEncuesta;
use App\Models\tbl_curso;
use App\Models\supervision\tokenTraitEncuesta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;

class EncuestaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function encuesta($urltoken)
    {
        $tokentrait = new tokenTraitEncuesta;
        $tokenCheck = $tokentrait->generateTmpToken($urltoken);

        $encuesta = calidad_encuestas::WHERE('activo', '=', 'true')->WHERE('idparent', '!=', '0')->WHERE('dirigido_a', '=', 'alumno')->GET();
        $titulo = calidad_encuestas::SELECT('nombre')->WHERE('activo', '=', 'true')->WHERE('idparent', '=', '0')->WHERE('dirigido_a', '=', 'alumno')->FIRST();
        return view('layouts.pages.frmencuesta', compact('encuesta','titulo', 'urltoken'));
    }

    public function encuesta_save(Request $request)
    {
        dd($request);
        $x = $request->get('optradio');
        $keys = array_keys($x);
        $token = tokenEncuesta::WHERE('url_token' , '=', $request->token)->FIRST();
        $id_curso = $token->id_curso;


        $RegisterExists = calidad_respuestas::WHERE('id_encuesta', '=', $request->id_encuesta)->FIRST();

        if($RegisterExists != NULL)
        {
            $array = $RegisterExists->respuestas;
            $pointerid = array_keys($array);
            foreach ($array as $data)
            {
                $keys = array_keys($data);
                foreach($keys as $item)
                {
                    if($item == current($x))
                    {
                        $array[current($pointerid)][current($x)] = $array[current($pointerid)][current($x)] + 1;
                    }
                }
                next($x);
                next($pointerid);
            }

            for ($i=1; $i<=5; $i++)
            {
                if($request->estrellas == $i)
                {
                    $array['calificacion'][$i] = $array['calificacion'][$i] + 1;
                }
            }

            $RegisterExists->respuestas = $array;
            $id_encuesta = $RegisterExists->id_encuesta;
            $RegisterExists->save();

            $token->cantidad_usuarios = $token->cantidad_usuarios - 1;
            if ($token->cantidad_usuarios == 0 )
            {
                $token = tokenEncuesta::WHERE('url_token' , '=', $request->token)->DELETE();
            }
            else
            {
                $token->save();
            }

        }
        else
        {
            $cursoValidado = tbl_curso::WHERE('id', '=', $token->id_curso);
            $encuesta = calidad_encuestas::SELECT('id','respuestas')->WHERE('activo', '=', 'true')->WHERE('idparent', '!=', '0')->WHERE('respuestas', '!=', NULL)->GET();

            $save_respuestas = new calidad_respuestas;
            $save_respuestas->id_encuesta = $request->id_encuesta;
            $save_respuestas->id_tbl_cursos = $token->id_curso;
            $save_respuestas->id_curso = $cursoValidado->id_curso;
            $save_respuestas->id_instructor = $cursoValidado->id_instructor;
            $save_respuestas->unidad = $cursoValidado->unidad;
            $save_respuestas->fecha_aplicacion = Carbon::now();

            foreach($encuesta as $item)
            {
                $key = $item->respuestas;
                foreach ($key as $data)
                {
                    if($data == current($x))
                    {
                        $array_respuestas[$item->id][$data] = '1';
                    }
                    else
                    {
                        $array_respuestas[$item->id][$data] = '0';
                    }
                }
                next($x);
            }
            for ($i=1; $i<=5; $i++)
            {
                if($request->estrellas == $i)
                {
                    $array_respuestas['calificacion'][$i] = '1';
                }
                else
                {
                    $array_respuestas['calificacion'][$i] = '0';
                }
            }

            $save_respuestas->respuestas = $array_respuestas;
            $id_encuesta = $encuesta->id;
            $save_respuestas->save();

            $token->cantidad_usuarios = $token->cantidad_usuarios - 1;
            if ($token->cantidad_usuarios == 0 )
            {
                $token = tokenEncuesta::WHERE('url_token' , '=', $request->token)->DELETE();
            }
            else
            {
                $token->save();
            }
        }

        $inscripcion = Inscripcion::WHERE('matricula', '=', $request->matricula)->WHERE('id_curso', '=', $token->id_curso)->FIRST();

        $respuesta_alumno = new calidad_respuestas_alumnos;
        $respuesta_alumno->id_inscripcion = $inscripcion->id;
        $respuesta_alumno->matricula = $inscripcion->matricula;
        $respuesta_alumno->nombre = $inscripcion->alumno;
        $respuesta_alumno->id_tbl_cursos = $id_curso;
        $respuesta_alumno->id_encuesta = $id_encuesta;
        $respuesta_alumno->respuestas = $x;
        $respuesta_alumno->comentario = $request->abierto;
        $respuesta_alumno->save();
    }

}
