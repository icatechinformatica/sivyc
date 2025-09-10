<?php
use Illuminate\Support\Facades\Route;
use App\Services\EFirmaService;
use App\Http\Controllers\DocumentoElectronico\PlantillaController;

// Route::get('/servicio/archivo/electronico', function () {

//     $movimiento = json_decode('[
//         {
//             "descripcion": null,
//             "folio": "CA-4001",
//             "curso": "PRINCIPIOS DE ELECTRICIDAD",
//             "concepto": "CURSO DE CAPACITACIÓN O CERTIFICACIÓN",
//             "documento": "2024/expedientes/1/CA-4001_240213130228_350.pdf",
//             "importe": "2300.00",
//             "importe_letra": "DOS MIL TRESCIENTOS PESOS 00/100 MN ",
//             "depositos": "[{\"fecha\": \"2024-02-13\", \"folio\": \"6802208333\", \"importe\": \"2300.00\"}]"
//         },
//         {
//             "descripcion": null,
//             "folio": "CA-4002",
//             "curso": "TENDENCIAS Y EFECTOS EN UÑAS ACRÍLICAS",
//             "concepto": "CURSO DE CAPACITACIÓN O CERTIFICACIÓN",
//             "documento": "2024/expedientes/481/CA-4002_240213144233_202.pdf",
//             "importe": "1500.00",
//             "importe_letra": "UN MIL QUINIENTOS PESOS 00/100 MN ",
//             "depositos": "[{\"fecha\": \"2024-02-13\", \"folio\": \"6814460469\", \"importe\": \"1500.00\"}]"
//         },
//         {
//             "descripcion": null,
//             "folio": "CA-4003",
//             "curso": "COCTELERÍA CLÁSICA, CONTEMPORÁNEA Y VANGUARDISTA",
//             "concepto": "CURSO DE CAPACITACIÓN O CERTIFICACIÓN",
//             "documento": "2024/expedientes/485/CA-4003_240214095216_350.pdf",
//             "importe": "1500.00",
//             "importe_letra": "UN MIL QUINIENTOS PESOS 00/100 MN ",
//             "depositos": "[{\"fecha\": \"2024-02-13\", \"folio\": \"7662-6011\", \"importe\": \"1500.00\"}]"
//         },
//         {
//             "descripcion": null,
//             "folio": "CA-4004",
//             "curso": "INGLÉS I",
//             "concepto": "CURSO DE CAPACITACIÓN O CERTIFICACIÓN",
//             "documento": "2024/expedientes/486/CA-4004_240214101033_350.pdf",
//             "importe": "2550.00",
//             "importe_letra": "DOS MIL QUINIENTOS CINCUENTA PESOS 00/100 MN ",
//             "depositos": "[{\"fecha\": \"2024-02-13\", \"folio\": \"7662-6013\", \"importe\": \"2550.00\"}]"
//         }
//     ]', true);

//     $param = [
//         'TYPE'             => 'RF001',
//         'unidadUbicacion'  => 'TUXTLA',
//         'memorandum'       => '123/2025',
//         'municipio'        => 'Tuxtla Gutiérrez',
//         'fechaFormateada'  => '12 de Marzo de 2025',
//         'titulo'           => 'C. PROF.',
//         'nombre'           => 'Juan Pérez',
//         'cargo'            => 'DIRECTOR GENERAL',
//         'importeMemo'      => 74833.96,
//         'periodo_inicio'   => '2025-03-24',
//         'periodo_fin'      => '2025-04-01',
//         'id_unidad'        => 8,
//         'creado'           => '2025-03-12 10:49:22',
//         'movimientos'      => $movimiento,
//     ];

//     $documento = (new EFirmaService())->setBody($param);
//     $pdf = app(PlantillaController::class)->generarPdf($documento);
//     return $pdf->stream('documento.pdf');

// });
// return (new EFirmaService())->setBody($param);
Route::get('/servicio/archivo/electronico/{id}', [PlantillaController::class, 'edit'])->name('archivo.electronico.show');
Route::get('/servicio/archivo/electronico', [PlantillaController::class, 'index'])->name('archivo.electronico.index');
Route::get('/servicio/electronico/{id}/{grupo?}', [PlantillaController::class, 'loadFile'])->name('archivo.electronico.load');
