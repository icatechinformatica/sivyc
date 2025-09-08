<?php
namespace App\Http\Controllers\DocumentoElectronico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Ejemplo: firmasTotales puede variar, firmasFirmadas y sellado pueden cambiar por documento
        $documentos = [
            [
                'id' => 1,
                'nombre' => 'Documento de Prueba 1',
                'folio' => 'FOL-001',
                'fecha' => '01/09/2025',
                'autor' => 'Juan Pérez',
                'archivo' => 'dummy1.pdf',
                'firmasTotales' => 3,
                'firmasFirmadas' => 3,
                'sellado' => true,
            ],
            [
                'id' => 2,
                'nombre' => 'Documento de Prueba 2',
                'folio' => 'FOL-002',
                'fecha' => '15/08/2025',
                'autor' => 'María López',
                'archivo' => 'dummy2.pdf',
                'firmasTotales' => 4,
                'firmasFirmadas' => 2,
                'sellado' => false,
            ],
            [
                'id' => 3,
                'nombre' => 'Documento de Prueba 3',
                'folio' => 'FOL-003',
                'fecha' => '20/07/2025',
                'autor' => 'Carlos Ramírez',
                'archivo' => 'dummy3.pdf',
                'firmasTotales' => 2,
                'firmasFirmadas' => 0,
                'sellado' => false,
            ],
            [
                'id' => 4,
                'nombre' => 'Documento de Prueba 4',
                'folio' => 'FOL-004',
                'fecha' => '10/06/2025',
                'autor' => 'Ana Torres',
                'archivo' => 'dummy4.pdf',
                'firmasTotales' => 5,
                'firmasFirmadas' => 3,
                'sellado' => false,
            ],
            [
                'id' => 5,
                'nombre' => 'Documento de Prueba 5',
                'folio' => 'FOL-005',
                'fecha' => '05/05/2025',
                'autor' => 'Luis Gómez',
                'archivo' => 'dummy5.pdf',
                'firmasTotales' => 2,
                'firmasFirmadas' => 2,
                'sellado' => true,
            ],
            [
                'id' => 6,
                'nombre' => 'Documento de Prueba 6',
                'folio' => 'FOL-006',
                'fecha' => '22/04/2025',
                'autor' => 'Patricia Ruiz',
                'archivo' => 'dummy6.pdf',
                'firmasTotales' => 4,
                'firmasFirmadas' => 2,
                'sellado' => false,
            ],
            [
                'id' => 7,
                'nombre' => 'Documento de Prueba 7',
                'folio' => 'FOL-007',
                'fecha' => '30/03/2025',
                'autor' => 'Miguel Ángel',
                'archivo' => 'dummy7.pdf',
                'firmasTotales' => 3,
                'firmasFirmadas' => 1,
                'sellado' => false,
            ],
        ];

        // Búsqueda por folio si se recibe parámetro
        $busqueda = $request->input('busqueda');
        if ($busqueda) {
            $documentos = array_filter($documentos, function($doc) use ($busqueda) {
                return stripos($doc['folio'], $busqueda) !== false;
            });
            $documentos = array_values($documentos);
        }

        // Búsqueda por folio si se recibe parámetro
        $busqueda = $request->input('busqueda');
        if ($busqueda) {
            $documentos = array_filter($documentos, function($doc) use ($busqueda) {
                return stripos($doc['folio'], $busqueda) !== false;
            });
            $documentos = array_values($documentos);
        }

        // Calcular proceso y semáforo para cada documento
        foreach ($documentos as &$doc) {
            $doc['proceso'] = $this->calcularAvance($doc['firmasFirmadas'], $doc['firmasTotales'], $doc['sellado']);
            // Lógica de semáforo:
            // Rojo: sin firmas o cancelado (aquí solo sin firmas, ya que no hay campo cancelado)
            // Amarillo: faltan firmas o falta sello
            // Verde: todas las firmas y sellado
            if (isset($doc['cancelado']) && $doc['cancelado']) {
                $doc['semaforo'] = '#dc3545'; // rojo por cancelado
            } elseif ($doc['firmasFirmadas'] == 0) {
                $doc['semaforo'] = '#dc3545'; // rojo por sin firmas
            } elseif ($doc['firmasFirmadas'] < $doc['firmasTotales'] || !$doc['sellado']) {
                $doc['semaforo'] = '#ffc107'; // amarillo
            } elseif ($doc['firmasFirmadas'] == $doc['firmasTotales'] && $doc['sellado']) {
                $doc['semaforo'] = '#28a745'; // verde
            } else {
                $doc['semaforo'] = '#dc3545'; // fallback rojo
            }
        }
        unset($doc);
        return view('documentos.index', compact('documentos'));
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

    /**
     * Calcula el porcentaje de avance basado en firmas y sellado.
     * @param int $firmasFirmadas
     * @param int $firmasTotales
     * @param bool $sellado
     * @return int
     */
    private function calcularAvance($firmasFirmadas, $firmasTotales, $sellado = false)
    {
        if ($firmasTotales <= 0) return $sellado ? 10 : 0;
        $porcentajeFirmas = ($firmasFirmadas / $firmasTotales) * 90;
        $porcentajeSellado = $sellado ? 10 : 0;
        return (int) round($porcentajeFirmas + $porcentajeSellado);
    }
}
