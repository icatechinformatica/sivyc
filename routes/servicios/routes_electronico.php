<?php
use Illuminate\Support\Facades\Route;
use App\Services\EFirmaService;


Route::get('/servicio/archivo/electronico', function () {
    $param = [
        'TYPE'             => 'RF001',
        'unidadUbicacion'  => 'TUXTLA GUTIÉRREZ',
        'memorandum'       => '123/2025',
        'municipio'        => 'Tuxtla Gutiérrez',
        'fechaFormateada'  => '04 de abril de 2025',
        'titulo'           => 'C. PROF.',
        'nombre'           => 'Juan Pérez',
        'cargo'            => 'DIRECTOR GENERAL',
        'importeMemo'      => 74833.96,
        'periodo_inicio'   => '2025-03-24',
        'periodo_fin'      => '2025-04-01',
        'id_unidad'        => 20,
    ];

    return (new EFirmaService())->setBody($param);
});
