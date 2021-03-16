<?php
use Illuminate\Support\Facades\Route;
//Rutas dta_ --creado MIS Daniel Méndez Cruz
Route::post('/validacion/dta/revision/cursos/envio/planeacion', 'Validacion\validacionDtaController@entrega_planeacion')->name('validacion.dta.cursos.envio.planeacion');
// Rutas de planeación
Route::get('/planeacion/formatot/index', 'validacion\planeacionController@index')->name('planeacion.formatot.index')->middleware('can:vista.revision.validacion.planeacion.indice');
Route::post('/planeacion/generar/memorandum', 'Validacion\planeacionController@generarMemorandum')->name('planeacion.generate.memo');
Route::post('/planeacion/enviar/dta', 'Validacion\planeacionController@sendtodta')->name('planeacion.send.to.dta');
Route::post('/planeacion/finalizar/proceso', 'Validacion\planeacionController@finishPlaneacion')->name('planeacion.finish');
// ruta para una función ajax --- 
Route::get('/reportes/formato/checkToDeliver', 'ftcontroller@chkDateToDeliver')->name('formato.check.to.deliver');
/**
 * RUTA PARA GENERAR EL REPORTE T PARA PLANEACIÓN
 */
Route::post('/planeacion/reportes/formatot/xls', 'Validacion\planeacionController@xlsExportReporteT')->name('reportes.planeacion.formatot.xls');
Route::post('/reportes/formatot/unidad/xls', 'ftcontroller@xlxExportReporteTbyUnidad')->name('reportes.formatot.unidad.xls');
Route::post('/reportes/formatot/enlaces/unidad/xls', 'Validacion\validacionDtaController@xlsExportReporteFormatotEnlacesUnidad')->name('reportes.formatot.enlaces.unidad.xls');
Route::post('/reportes/formatot/director/dta/xls', 'Validacion\validacionDtaController@xlsExportReporteFormatoTDirectorDTA')->name('reportes.formatot.director.dta.xls');