<?php
use Illuminate\Support\Facades\Route;
//Rutas dta_ --creado MIS Daniel Méndez Cruz
Route::post('/validacion/dta/revision/cursos/envio/planeacion', 'Validacion\validacionDtaController@entrega_planeacion')->name('validacion.dta.cursos.envio.planeacion');
// Rutas de planeación
Route::get('/planeacion/formatot/index', 'validacion\planeacionController@index')->name('planeacion.formatot.index');
Route::post('/planeacion/generar/memorandum', 'Validacion\planeacionController@generarMemorandum')->name('planeacion.generate.memo');
Route::post('/planeacion/enviar/dta', 'Validacion\planeacionController@sendtodta')->name('planeacion.send.to.dta');
Route::post('/planeacion/finalizar/proceso', 'Validacion\planeacionController@finishPlaneacion')->name('planeacion.finish');