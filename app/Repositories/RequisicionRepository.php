<?php

namespace App\Repositories;

use App\Interfaces\RequisicionRepositoryInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class RequisicionRepository implements RequisicionRepositoryInterface
{
    public function searchPartidaPresupuestal(Request $request): array
    {
        $resultados = DB::Connection('mysql')->Table('partida')->Select(
            'partida.descripcion',
            'partida.clave_partida',
            'partida.id'
        )
            ->where('partida.clave_partida', "LIKE", "%{$request->term}%")
            ->orWhere("partida.descripcion", "LIKE", "%{$request->term}%")
            ->OrderBy('partida.id')->get();

        $partidaResultado = [];

        foreach ($resultados as $key) {
            $partidaResultado[] = $key->clave_partida . "-----" . $key->descripcion;
        }

        return $partidaResultado;
    }

    public function searchMateriales(Request $req): array
    {
        $res = DB::connection('mysql')->table('catalogo_vienes')->select(
            'catalogo_vienes.descripcion',
            'catalogo_vienes.activo',
            'catalogo_vienes.id'
        )
            ->where('catalogo_vienes.descripcion', "LIKE", "%{$req->term}%")
            ->OrderBy('catalogo_vienes.id')->get();

        $materialesResultado = [];

        foreach ($res as $key) {
            $materialesResultado[] = $key->id . "---" . $key->descripcion;
        }

        return $materialesResultado;
    }

    public function createRequisition(Request $req)
    {
        try {
            //queries
            $data = $req->correo;
            // store requition
            $check = DB::connection('mysql')->table('requisicion')->select('requisicion.usuario', 'requisicion.id')
                ->whereJsonContains('requisicion.usuario->email', $data)->get();

            if (count($check) > 0) {
                // if it contains data proceed to update
                $requisicionId = $check[0]->id;

                $idBien = explode('---', $req->articulo)[0];

                $checkItems = DB::connection('mysql')->table('requisicion_unidad')->select('requisicion_unidad.cantidad')->where('requisicion_unidad.id_bien', '=', $idBien)->first();

                if ($checkItems) {
                    # it's data from query
                    $sumCantidad = $checkItems->cantidad + $req->cantidad;
                    $qry = [
                        'cantidad' => $sumCantidad,
                        'updated_at' => Carbon::now()
                    ];
                    DB::connection('mysql')->table('requisicion_unidad')->where('requisicion_unidad.id_bien', '=', $idBien)->update($qry); // update query
                } else {
                    $qry = [
                        'requisicion_id' => $requisicionId,
                        'id_bien' => $idBien, //separar el id get id
                        'cantidad' => $req->cantidad,
                        'unidad' => $req->unidad,
                        'created_at' => Carbon::now()
                    ];
                    DB::connection('mysql')->table('requisicion_unidad')->insert($qry);
                }

                $respuesta['response'] = true;
                $respuesta['message'] = 'AGREGAR ELEMENTO A REQUISICIÓN';

                return $respuesta;
            } else {
                // instead to proceed to insert data
                $requisionId = DB::connection('mysql')->table('requisicion')->insertGetId([
                    'solicita' => $req->articulo,
                    'id_area' => $req->id_area,
                    'usuario' => $req->usuario,
                    'fechaRequisicion' => $req->fecha,
                    'autoriza' => 'LIC. CRISTINA RIOS MIJANGOS',
                    'id_estado' => 6
                ]);

                //insertar registro

                DB::connection('mysql')->table('requisicion_unidad')->insert([
                    'requisicion_id' => $requisionId,
                    'id_bien' => explode('---', $req->articulo)[0], //separar el id get id
                    'cantidad' => $req->cantidad,
                    'unidad' => $req->unidad,
                    'created_at' => Carbon::now()
                ]);

                $respuesta['response'] = true;
                $respuesta['message'] = 'AGREGAR ELEMENTO A REQUISICIÓN';

                return $respuesta;
            }
        } catch (QueryException $th) {
            return $th->getMessage();
        }
    }

    public function loadRequisicion()
    {
        return DB::connection('mysql')->table('catalogo_vienes')->select(
            'partida.clave_partida',
            'partida.descripcion',
            'partida.id'
        )
            ->join('partida', 'catalogo_vienes.id_partida', '=', 'partida.id')
            ->join('requisicion_unidad', 'requisicion_unidad.id_bien', '=', 'catalogo_vienes.id')
            ->groupBy('partida.clave_partida', 'partida.descripcion', 'partida.id')
            ->get();
        // SELECT PAT.clave_partida, PAT.descripcion, CAT.descripcion FROM catalogo_vienes CAT JOIN partida PAT ON CAT.id_partida = PAT.Id GROUP BY PAT.clave_partida;

    }

    public function loadItems($idReq)
    {
        return DB::connection('mysql')->table('requisicion_unidad')
            ->select('requisicion_unidad.cantidad as cantidadReq', 'cat_unidades.unidad as UnidadReq', 'catalogo_vienes.descripcion as descripcionCat', 'catalogo_vienes.id_partida as idPartidaCat', 'requisicion_unidad.id as requisicionId', 'requisicion_unidad.unidad as unidadId', 'requisicion_unidad.id')
            ->leftJoin('catalogo_vienes', 'requisicion_unidad.id_bien', '=', 'catalogo_vienes.id')
            ->leftJoin('partida', 'catalogo_vienes.id_partida', '=', 'partida.id')
            ->leftJoin('cat_unidades', 'requisicion_unidad.unidad', '=', 'cat_unidades.id')
            ->get();
    }

    public function checkRequisicion($email): object
    {
        return DB::connection('mysql')->table('requisicion')->select('requisicion.usuario', 'requisicion.id', 'requisicion.solicita', 'requisicion.fechaRequisicion', 'requisicion.autoriza', 'requisicion.id', 'requisicion.id_estado', 'seguimiento_status.estado')
            ->leftJoin('seguimiento_status', 'requisicion.id_estado', '=', 'seguimiento_status.id')
            ->whereJsonContains('requisicion.usuario->email', $email)->get();
    }

    public function addJustification($req)
    {
        try {
            $idRequisicion = base64_decode($req->requisicionId);
            $qry = [
                'justificacion' => $req->justificacion
            ];

            DB::connection('mysql')->table('requisicion')->where('requisicion.id', $idRequisicion)->update($qry);
            //code...
            $respuesta['response'] = true;
            $respuesta['message'] = 'AGREGAR ELEMENTO A REQUISICIÓN';

            return $respuesta;
        } catch (QueryException $th) {
            return $th->getMessage();
        }
    }

    public function getJustificacion($id)
    {
        return  DB::connection('mysql')->table('requisicion')->select('requisicion.justificacion')
            ->where('requisicion.id', $id)->first();
    }

    public function destroyReq($id)
    {
        DB::connection('mysql')->table('requisicion_unidad')->where('id', '=', $id)->delete();

        $respuesta['response'] = true;
        $respuesta['message'] = 'ELEMENTO ELIMINADO';

        return $respuesta;
    }

    public function getCatUnidades()
    {
        return DB::connection('mysql')->table('cat_unidades')->pluck('unidad', 'id');
    }

    public function updateReq($request, $id)
    {
        $query = [
            'unidad' => $request->unidad,
            'cantidad' => $request->cantidad,
        ];

        DB::connection('mysql')->table('requisicion_unidad')->where('requisicion_unidad.id', $id)->update($query);

        $respuesta['response'] = true;
        $respuesta['message'] = 'REQUISICIÓN ACTUALIZADA';

        return $respuesta;
    }

    public function uploadFile($request)
    {
        $anio = Carbon::now()->year;
        $idReq = base64_decode($request->idRequisicion);

        $request->validate([
            'file' => 'required|mimes:pdf|max:10240', // PDF file, maximum size 10MB
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $name_file = $idReq."_".date('ymdHis')."_".auth()->id();
            $extensionFile = $file->getClientOriginalExtension(); // extension de la imagen
            #nombreArchivo
            $pdfFile = trim($name_file.".".$extensionFile);
            $directorio = '/'. $anio . "/" .'requisiciones/' . $idReq;

            $path = $request->file('file')->storeAs($directorio, $pdfFile); // guardamos el archivo en la carpeta storage
        }

        $data = [
            'archivo' => $path,
        ];

        DB::connection('mysql')->table('requisicion')->where('requisicion.id', $idReq)->update($data);

        $respuesta['response'] = true;
        $respuesta['message'] = 'ARCHIVO CARGADO CON ÉXITO';

        return $respuesta;

    }
}
