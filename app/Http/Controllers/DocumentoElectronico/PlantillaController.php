<?php

namespace App\Http\Controllers\DocumentoElectronico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\DocumentoService;
use App\Services\EFirmaService;

class PlantillaController extends Controller
{
    private $servicioPlantilla;

    public function __construct(DocumentoService $servicioPlantilla)
    {
        $this->servicioPlantilla = $servicioPlantilla;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        //
        $plantillas = $this->servicioPlantilla->obtenerPlantillas();
        return response()->json([
            'success' => true,
            'data' => $allLayouts
        ], 200);
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
        $documento = $this->servicioPlantilla->getPlantilla($id);
        // como llamarias al método de inyección de variables en un servicio dedicado
                //Prueba de inyeccion html
        $uuid = 'prueba uuid'; $cadena_sello = 'prueba de cadena de sellado'; $fecha_sello = '22/04/2025';

        $selloDigital = "Sello Digital: | GUID: $uuid | Sello: $cadena_sello | Fecha: $fecha_sello<br>
                Este documento ha sido Firmado Electrónicamente, teniendo el mismo valor que la firma autógrafa
                de acuerdo a los Artículos 1, 3, 8 y 11 de la Ley de Firma Electrónica Avanzada del Estado de Chiapas";

        $variables = [
            'sello_digital' => $selloDigital,
            'no_contrato' => 'TU/DA/800/0272/2024.',
            'titular_uc' => 'LIC. ILIANA MICHELLE RAMIREZ MOLINA',
            'cargo_titular_uc' => 'TITULAR DE LA DIRECCIÓN DE LA UNIDAD DE CAPACITACION TUXTLA',
            'instructor' => 'ROGELIO MOISES MUELA LOPEZ',
            'director_general' => 'Mtra. Fabiola Lizbeth Astudillo Reyes',
            'cargo_dg' => 'Titular de la Dirección General del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas',
            'gobernador' => 'Dr. Rutilio Escandón Cadenas',
            'fecha_nom_dg' => '16 de enero de 2019',
            'espe_instructor' => 'ADMINISTRACIÓN',
            'regimen_instructor' => 'SUELDOS Y SALARIOS E INGRESOS ASIMILADOS A SALARIOS',
            'clave_grupo' => '2B-24-ADMI-CAE-0138',
            'tipo_identif_instructor' => 'Credencial Para Votar',
            'folio_identif_instructor' => '1625073024802',
            'rfc_instructor' => 'MULR870429QN2',
            'domicilio_instructor' => 'C SABINO NORTE MZA 139 LT 9, COL PATRIA NUEVA DE SABINES, C.P. 29045, TUXTLA GUTIERREZ, CHIS.',
            'importe_monto' => '12,800.00',
            'importe_monto_letra' => 'DOCE MIL OCHOCIENTOS PESOS 00/100 M.N.',
            'municipio' => 'TUXTLA GUTIERREZ',
            // otras variables...
        ];
        $contenidoProcesado = $this->servicioPlantilla->procesarPlantilla($documento->cuerpo, $variables);
        $newDocument = (new EFirmaService())->setBody($contenidoProcesado);
        return $newDocument;
        //
        // return response()->json([
        //     'success' => true,
        //     'data' => $contenidoProcesado
        // ], 200);
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
