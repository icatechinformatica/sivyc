<?php
use Illuminate\Support\Facades\Route;
//Rutas dta_ --creado MIS Daniel Méndez Cruz
Route::post('/validacion/dta/revision/cursos/envio/planeacion', 'Validacion\validacionDtaController@entrega_planeacion')->name('validacion.dta.cursos.envio.planeacion');
// Rutas de planeación
Route::get('/planeacion/formatot/index', 'Validacion\planeacionController@index')->name('planeacion.formatot.index')->middleware('can:vista.revision.validacion.planeacion.indice');
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
/**
 * REPORTE FORMATO T APERTURADO
 */
Route::get('/reportes/formatot/aperturado/index', 'Validacion\validacionDtaController@ReporteAperturaIndexDta')->name('indice.dta.aperturado.indice');
Route::post('/reportes/formatot/aperturado/generar', 'Validacion\validacionDtaController@generarreporteapertura')->name('generar.reporte.apertura');
/**
 * RUTAS MEMORANDUM DEL FORMATO T
 */
Route::get('/consultar/memorandum/unidad/index', 'ftcontroller@memorandumporunidad')->name('checar.memorandum.unidad')->middleware('can:vista.formatot.unidades.indice');
Route::get('/consultar/memorandum/dta/index', 'Validacion\validacionDtaController@memorandumpordta')->name('checar.memorandum.dta.mes')->middleware('can:vista.validacion.direccion.dta');
Route::get('/consultar/memorandum/dta/index', 'Validacion\validacionDtaController@memorandumpordta')->name('checar.memorandum.dta.mes')->middleware('can:vista.validacion.enlaces.dta');
Route::get('/consultar/memorandum/planeacion/index', 'Validacion\PlaneacionController@memorandumplaneacion')->name('checar.memorandum.planeacion')->middleware('can:vista.revision.validacion.planeacion.indice');
/**
 * modificaciones de busqueda por especialidad, tipo de curso y cursos
 */
Route::post('/alumnos/sid/cursos/modificar', 'webController\AlumnoController@getcursosModified')->name('alumnos.sid.cursos.modificado');
Route::get('/seguimiento/avances/unidades/formatot/index', 'webController\SeguimientoController@index')->name('seguimento.avance.unidades.formatot.ejecutiva.index');
/**
 * CONSULTAR CURSOS REPORTADOS HISTORICOS MESES ANTERIORES
 */
Route::get('/cursos/reportados/historico/unidad/index', 'ftcontroller@cursosreportados')->name('cursos.reportados.historico.index')->middleware('can:vista.formatot.unidades.indice');
Route::get('/cursos/reportados/historico/dta/index', 'Validacion\validacionDtaController@cursosReportadosDta')->name('cursos.reportados.historico.dta.index')->middleware('can:vista.validacion.enlaces.dta');
Route::get('/cursos/reportados/historico/direccion/dta/index', 'Validacion\validacionDtaController@cursosReportatosDireccionDta')->name('cursos.reportados.historico.direccion.dta.index')->middleware('can:vista.validacion.direccion.dta');
Route::get('/cursos/reportados/historico/planeacion/index', 'Validacion\planeacionController@cursosReportadosPlaneacion')->name('cursos.reportados.historico.planeacion.index')->middleware('can:vista.revision.validacion.planeacion.indice');
