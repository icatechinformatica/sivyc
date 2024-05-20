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
//Deshacer validacion
Route::post('/vista/pat/fechaspat/deshacer', 'PatController\FechasController@return_valid')->name('pat.fechaspat.deshacer');


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

Route::post('/preinscripcion/grupo/upload/pdfs', 'Preinscripcion\grupoController@upload_pdfs')->name('preinscripcion.grupo.uploadpdf');
// Antigua ruta que ya no es funcional
// Route::post('/preinscripcion/grupo/uploadconv/firconv', 'Preinscripcion\grupoController@pdf_conv_firm')->name('preinscripcion.grupo.firmconvpdf');

/**SOLICITUD -> CLAVE DE APERTURA ARC01 GEN PDF SOPORTE CONSTANCIAS*/
Route::post('/solicitud/apertura/pdfsoporte/', 'Solicitud\aperturaController@genpdf_soporte')->name('solicitud.genpdf.soporte');
//Subir pdf de soporte de entrega de constancias
Route::post('/solicitud/apertura/uploadpdf/', 'Solicitud\aperturaController@upload_pdfsoporte')->name('solicitud.soporte.upload');


/** NUEVAS RUTAS PARA EL MODULO DE EXPEDIENTES UNICOS */
Route::middleware(['auth'])->group(function(){
    Route::get('/vista/expedientes/unicos/{folio?}', 'ExpeController\ExpedienteController@index')->name('expunico.principal.mostrar.get')->middleware('can:expedientes.unicos.index');
    Route::post('/vista/expedientes/unicos', 'ExpeController\ExpedienteController@index')->name('expunico.principal.mostrar.post')->middleware('can:expedientes.unicos.index');
    //Envio de valores del form por ajax
    Route::post('/vista/expedientes/guardar', 'ExpeController\ExpedienteController@guardar')->name('expunico.principal.guardar')->middleware('can:expedientes.unicos.index');
    //Subida de pdf vinculacion al servidor
    Route::post('/vista/expedientes/uploadpdf', 'ExpeController\ExpedienteController@uploadpdfs')->name('expunico.save.pdfs')->middleware('can:expedientes.unicos.index');
    //Eliminar PDF
    Route::post('/vista/expedientes/deletepdf', 'ExpeController\ExpedienteController@deletpdfs')->name('expunico.delete.pdf')->middleware('can:expedientes.unicos.index');
    //Enviar DTA
    Route::post('/vista/expedientes/enviar', 'ExpeController\ExpedienteController@validar_form')->name('expunico.envio.valid')->middleware('can:expedientes.unicos.index');
    //Validar O Retornar DTA
    Route::post('/vista/expedientes/validar', 'ExpeController\ExpedienteController@validar_dta')->name('expunico.valid.dta')->middleware('can:expedientes.unicos.index');
    /**Rutas de buzon para visualizar los expedientes pendientes, enviados, validados */
    Route::get('/vista/buzon/expedientes/', 'ExpeController\BuzonexpController@index')->name('buzon.expunico.index')->middleware('can:expunico.buzon.index');
});
/**Generar pdf expedientes unicos */
Route::get('vista/expedientes/genpdf/{folio}', 'ExpeController\ExpedienteController@pdf_expediente')->name('expunico.gen.pdfexpe');


/** MODULO DE CONSULTA DE ALUMNOS VINCULADOS CON LA BOLSA DE TRABAJO */
Route::get('consultas/bolsa/alumnos', 'Consultas\BolsaTrabController@index')->name('consultas.bolsa.index');
//Post para el autocompletado de cursos
Route::post('autocomplet/bolsa/cursos', 'Consultas\BolsaTrabController@autocomplete_cursos')->name('consulta.bolsa.autocomp');
//Generar reporte de excel
Route::get('consultas/bolsa/reporte', 'Consultas\BolsaTrabController@crear_reporte_excel')->name('consulta.bolsa.reporte');

/** MODULO DE EFIRMA BUZON FOLIO ALUMNOS */
// Route::get('grupos/efirma/buzon', 'Grupos\efirmaFoliosAlumnos\BuzonFoliosController@index')->name('grupo.efirma.index');
// Route::post('grupos/efirma/buzon', 'Grupos\efirmaFoliosAlumnos\BuzonFoliosController@index')->name('grupo.efirma.index');
// Route::post('grupos/efirma/buzon/eliminar', 'Grupos\efirmaFoliosAlumnos\BuzonFoliosController@cancelar_doc')->name('grupo.efirma.canceldoc');
// Route::get('grupos/efirma/pdf/{id}', 'Grupos\efirmaFoliosAlumnos\BuzonFoliosController@generar_pdf')->name('grupo.efirma.pdf');
// Route::post('grupos/efirma/token', 'Grupos\efirmaFoliosAlumnos\BuzonFoliosController@generarToken')->name('efirma.token');
// Route::post('grupos/efirma/buzon/update', 'Grupos\efirmaFoliosAlumnos\BuzonFoliosController@firmar_documento')->name('grupo.efirma.update');
// Route::post('grupos/efirma/buzon/sellar', 'Grupos\efirmaFoliosAlumnos\BuzonFoliosController@sellar_documento')->name('grupo.efirma.sellar');


//Ruta para le boton de generar xml de folios en el modulo de asigar folios
// Route::post('/grupos/asignarfolios/generar', 'Grupos\asignarfoliosController@efolios_insert')->name('grupos.asignarfolios.efolios');

//Ruta para la nueva funcionalidad de agregar carta descriptiva de cursos
Route::get('/cursos/carta-descriptiva/{id}/{parte}', 'webController\CursosController@carta_descriptiva')->name('cursos-catalogo.cartadescriptiva');
Route::post('/cursos/save/primera', 'webController\CursosController@save_parte_uno')->name('cursos-catalogo.saveparteuno');  //Guardar primera parte
Route::post('/cursos/save/segunda', 'webController\CursosController@save_parte_dos')->name('cursos-catalogo.savepartedos');  //Guardar segunda parte
Route::post('/cursos/save/tercera', 'webController\CursosController@save_parte_tres')->name('cursos-catalogo.savepartetres');  //Guardar tercera parte
Route::post('/cursos/edit/carta', 'webController\CursosController@edit_cartadescrip')->name('cursos-catalogo.editcartadecrip'); //Editar y eliminar
