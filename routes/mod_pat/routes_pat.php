<?php
use Illuminate\Support\Facades\Route;



/**Unidades de medida */
Route::get('/vista/pat/um', 'PatController\UmController@index')->name('pat.unidadesmedida.mostrar')->middleware('can:unidades.medida.index');
//Eliminar
//Route::get('/vista/pat/um/{id}', 'PatController\UmController@destroy')->name('unidadesm.destroy');
//inactivar o desactivar con ajax
Route::post('/vista/pat/um/status/', 'PatController\UmController@status')->name('unidadesm.mod.status');
//Agregar
Route::post('/vista/pat/um/save', 'PatController\UmController@store')->name('unidadesm.guardar');
//Editar
//Route::get('/vista/pat/um/show/{id}', 'PatController\UmController@show')->name('unidadesm.edit.show');
Route::post('/vista/pat/um/show/', 'PatController\UmController@show')->name('unidadesm.edit.show');
Route::post('/vista/pat/um/update/', 'PatController\UmController@update')->name('unidadesm.update');


/**Funciones */
Route::get('/vista/pat/funciones/{idorg?}', 'PatController\FuncionesController@index')->name('pat.funciones.mostrar')->middleware('can:funproc.pat.index');
//Eliminar
//Route::get('/vista/pat/funciones/{id}', 'PatController\FuncionesController@destroy')->name('funciones.destroy');
//inactivar o desactivar con ajax
Route::post('/vista/pat/funciones/status/', 'PatController\FuncionesController@status')->name('pat.funciones.status');
//Agregar
Route::post('/vista/pat/funciones/save', 'PatController\FuncionesController@store')->name('funciones.guardar');
//Editar
Route::get('/vista/pat/funciones/show/{id}/{idorg}', 'PatController\FuncionesController@show')->name('funciones.edit.show');
Route::post('/vista/pat/funciones/update/{id}', 'PatController\FuncionesController@update')->name('funciones.update');


/**Procedimientos */
Route::get('/vista/pat/procedimientos/{id}/{idorg}', 'PatController\ProcedController@index')->name('pat.proced.mostrar');
//Eliminar
//Route::get('/vista/pat/procedimientos/delete/{idd}/{id}', 'PatController\ProcedController@destroy')->name('proced.destroy');
//inactivar o desactivar con ajax
Route::post('/vista/pat/procedimientos/status/', 'PatController\ProcedController@status')->name('pat.proced.status');
//Agregar
Route::post('/vista/pat/procedimientos/save/{id}', 'PatController\ProcedController@store')->name('proced.guardar');
//Editar
Route::get('/vista/pat/procedimientos/show/{idedi}/{id}/{idorg}', 'PatController\ProcedController@show')->name('pat.proced.edit.show');
Route::post('/vista/pat/procedimientos/update/{idedi}/{id}', 'PatController\ProcedController@update')->name('proced.update');
//post para el autocompletado
Route::post('/vista/pat/procedimientos/auto', 'PatController\ProcedController@autocomplete')->name('pat.proced.autocomp');


/**Metas y avances*/
Route::get('/vista/pat/metasav/{idorg?}', 'PatController\MetavanceController@index')->name('pat.metavance.mostrar')->middleware('can:metasavances.index');
//Agregar datos metas
Route::post('/vista/pat/metasav/guardar/meta/', 'PatController\MetavanceController@store')->name('pat.metavance.guardar.meta');
//Agregar datos avances
Route::post('/vista/pat/metasav/guardar/avance/', 'PatController\MetavanceController@avances')->name('pat.metavance.guardar.avance');
//Ruta para generar pdf metas
Route::get('/vista/pat/metasav/genpdf/meta/{accion}/{idorg}', 'PatController\MetavanceController@genpdf')->name('pat.metavance.genpdf.meta');
//Subir al serv meta pdf firmado
Route::post('/vista/pat/metasav/guardar/pdfmeta/', 'PatController\MetavanceController@uploadpdfmeta')->name('pat.metavance.guardar.updpdfmeta');
//Subir al serv pdf de avances por mes
Route::post('/vista/pat/metasav/guardar/pdfavance/', 'PatController\MetavanceController@uploadpdfavance')->name('pat.metavance.guardar.updpdfavance');


/**Fechas */
Route::get('/vista/pat/fechaspat/{tipo?}', 'PatController\FechasController@index')->name('pat.fechaspat.mostrar')->middleware('can:fechaspat.index');
//Agregar metas
Route::post('/vista/pat/fechaspat/add/metas', 'PatController\FechasController@guardar')->name('pat.fechaspat.guardar');
//Obtener id de fechas
Route::post('/vista/pat/fechaspat/consul', 'PatController\FechasController@consulfech')->name('pat.fechaspat.consulfech');
//Guardar fechas al dar actualizar
Route::post('/vista/pat/fechaspat/saveupd', 'PatController\FechasController@guardarfech')->name('pat.fechaspat.saveupdate');



/**Entrada de planeación */
Route::get('/vista/pat/plane/entrada', 'PatController\MetavanceController@planeindex')->name('pat.metavance.vistaplane');
//Para mostra la pantalla a planeacion en modo validador
Route::get('/vista/pat/plane/envio/{id}', 'PatController\MetavanceController@valid_planeacion')->name('pat.metavance.envioplane')->middleware('can:metava.valid.index');

Route::post('/vista/pat/plane/valid', 'PatController\MetavanceController@registrar_validacion')->name('pat.metavance.validar');

//Buzon Planeación
Route::get('/vista/pat/buzon/index/', 'PatController\BuzonController@index')->name('pat.buzon.index')->middleware('can:metava.valid.index');
// Generar pdf de todos los organismos
Route::get('/vista/pat/buzon/pdf/general/{mes}/{opcion}', 'PatController\BuzonController@pdforg_direc')->name('pat.buzon.pdf.general');



/**CREAMOS NUEVAS RUTAS PARA GENERACION DE PDF, HAGO ESTO DEBIDO A QUE "WEB" YA ESTA CARGADO DE MUCHAS RUTAS" */

/*VINCULACION->PREINSCRIPCION=> NUEVO GRUPO RPN*/
/**Generar pdf Acta de acuerdo y convenio JOSE LUIS */
Route::post('/preinscripcion/grupo/pdfacta', 'Preinscripcion\grupoController@pdf_actaAcuerdo')->name('preinscripcion.grupo.acuerdo_pdf');
Route::post('/preinscripcion/grupo/pdfconvenio', 'Preinscripcion\grupoController@pdf_convenio')->name('preinscripcion.grupo.convenio_pdf');

/**Agregamos ruta para subir pdfs acta y convenio firmados */
//Subir al serv pdf de avances por mes
Route::post('/preinscripcion/grupo/uploadacta/firmacta', 'Preinscripcion\grupoController@pdf_acta_firm')->name('preinscripcion.grupo.firmactapdf');
Route::post('/preinscripcion/grupo/uploadconv/firconv', 'Preinscripcion\grupoController@pdf_conv_firm')->name('preinscripcion.grupo.firmconvpdf');

/**SOLICITUD -> CLAVE DE APERTURA ARC01 GEN PDF SOPORTE CONSTANCIAS*/
Route::post('/solicitud/apertura/pdfsoporte/', 'Solicitud\aperturaController@genpdf_soporte')->name('solicitud.genpdf.soporte');
