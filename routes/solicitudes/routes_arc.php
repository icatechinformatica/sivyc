<?php
use Illuminate\Support\Facades\Route;
Route::middleware(['auth'])->group(function(){            
    Route::post('/solicitudes/aperturas/soporte_pago', 'Solicitudes\aperturasController@soporte_pago')->name('solicitudes.aperturas.soporte_pago')->middleware('can:solicitudes.aperturas');
    Route::post('/solicitudes/aperturas/guardar_fecha', 'Solicitudes\aperturasController@guardar_fecha')->name('solicitudes.aperturas.guardar_fecha')->middleware('can:solicitudes.aperturas');
    Route::post('/solicitudes/aperturas/pdfagenda', 'Solicitudes\aperturasController@pdfAgendaAnexo')->name('solicitudes.generar.agenda.anexo')->middleware('can:solicitudes.aperturas');
    Route::get('/solicitudes/aperturas/pdfagenda', 'Solicitudes\aperturasController@pdfAgendaanexo')->middleware('can:solicitudes.aperturas');
    Route::post('/solicitudes/exoneraciones/guardar_fecha', 'Solicitudes\exoneracionesController@guardar_fecha')->name('solicitudes.exoneraciones.guardar_fecha')->middleware('can:solicitudes.exoneracion');
});