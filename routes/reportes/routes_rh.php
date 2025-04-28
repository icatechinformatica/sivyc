<?php
use Illuminate\Support\Facades\Route;
Route::middleware(['auth'])->group(function(){
    // Seccion modulo RH
    Route::get('/recursos-humanos/index', 'reportesController\RHController@index')->name('rh.index');
    Route::get('/recursos-humanos/catalogo/index', 'reportesController\RHController@catalogo_index')->name('rh.catalogo-index');
    Route::get('/recursos-humanos/descarga/nube', 'reportesController\RHController@descarga_nube')->name('rh.descarga.nube');
    Route::get('/recursos-humanos/reporte/quincenal', 'reportesController\RHController@reporte_quincenal')->name('rh.reporte.quincenal')->middleware('can:RH.tarjetatiempo');
    Route::post('/recursos-humanos/reporte/quincenal/pdf', 'reportesController\RHController@reporte_quincenal_pdf')->name('rh.reporte.quincenal.pdf');
    Route::get('/recursos-humanos/reporte/quincenal/detalles/{id}', 'reportesController\RHController@reporte_quincenal_detalles')->name('rh.reporte.detalles');
    Route::post('/asistencia/upload', 'reportesController\RHController@upload')->name('asistencia.upload');
    Route::get('/agregar/justificante', 'reportesController\RHController@agregar_justificante')->name('rh.agregar.justificante');
});
