<?php
//Rutas Orlando

use App\Http\Controllers\webController\InstructorController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::post('/alumnos/sid/municipios', 'webController\AlumnoController@getmunicipios');

//Ruta Instructor
Route::get('/instructor/validar/{id}', 'webController\InstructorController@validar')->name('instructor-validar');
Route::get('/instructor/editar/{id}', 'webController\InstructorController@editar')->name('instructor-editar');
Route::get('/instructor/editar/especialidad-validada/{id}/{idins}', 'webController\InstructorController@edit_especval')->name('instructor-editespectval');
Route::get('/instructor/editar/especialidad-valid/{id}/{idins}/{idesp}', 'webController\InstructorController@edit_especval2')->name('instructor-editespectval2');
Route::get('/instructor/mod/perfil-profesional/{id}/{idins}', 'webController\InstructorController@mod_perfil')->name('instructor-perfilmod');
Route::post('/instructor/rechazo','webController\InstructorController@rechazo_save')->name('instructor-rechazo');
Route::post('/instructor/validado','webController\InstructorController@validado_save')->name('instructor-validado');
Route::post('/instructor/guardar-mod','webController\InstructorController@guardar_mod')->name('instructor-guardarmod');
Route::post('/instructor/saveins','webController\InstructorController@save_ins')->name('saveins');
Route::post('/instructor/espec-ins/guardar','webController\InstructorController@espec_val_save')->name('especinstructor-guardar');
Route::post('/instructor/espec-ins/modificacion/guardar','webController\InstructorController@especval_mod_save')->name('especinstructor-modguardar');
Route::post('/instructor/mod/perfilinstructor/guardar', 'webController\InstructorController@modperfilinstructor_save')->name('modperfilinstructor-guardar');

Route::get('/alumnos_registrados/modificar/index', 'adminController\AlumnoRegistradoModificarController@index')->name('alumno_registrado.modificar.index');
Route::get('/alumnos_registrados/modificar/show/{id}', 'adminController\AlumnoRegistradoModificarController@edit')->name('alumno_registrado.modificar.show');
Route::put('alumnos_registrados/modificar/update/{id}', 'adminController\AlumnoRegistradoModificarController@update')->name('alumno_registrado.modificar.update');



Route::get('/alumno/registro/pdf', 'webController\AlumnoController@pdf_registro')->name('pdf-alumno');

Route::get('/exportarpdf/solicitudsuficiencia', 'webController\presupuestariaController@index')->name('procesodepago');
Auth::routes();

//Ruta supre
Route::get('/supre/solicitud/opc', 'webController\supreController@opcion')->name('solicitud-opcion');
Route::get('/supre/solicitud/folio', 'webController\supreController@solicitud_folios')->name('solicitud-folio');
Route::get('/supre/tabla-pdf/{id}', 'webController\supreController@tablasupre_pdf')->name('tablasupre-pdf');

//Ruta Contrato
Route::get('/contrato/inicio', 'webController\ContratoController@index')->name('contrato-inicio');
Route::get('/contrato/solicitud-pago/{id}','webController\ContratoController@solicitud_pago')->name('solicitud-pago');
Route::post('/contrato/save','webController\ContratoController@contrato_save')->name('contrato-save');
Route::post('/contrato/save-mod','webController\ContratoController@save_mod')->name('contrato-savemod');
Route::post('/contrato/rechazar-contrato','webController\ContratoController@rechazar_contrato')->name('contrato-rechazar');
Route::get('/contrato/validar/{id}', 'webController\ContratoController@validar_contrato')->name('contrato-validar');
Route::get('/contrato/{id}', 'webController\ContratoController@contrato_pdf')->name('contrato-pdf');
Route::post('/contrato/save-doc','webController\ContratoController@save_doc')->name('save-doc');
Route::get('/contrato/valcontrato/{id}', 'webController\ContratoController@valcontrato')->name('valcontrato');
Route::get('/contrato/modificar/{id}', 'webController\ContratoController@modificar')->name('contrato-mod');
Route::get('/contrato/solicitud-pago/pdf/{id}', 'webController\ContratoController@solicitudpago_pdf')->name('solpa-pdf');
Route::post('/directorio/getdirectorio','webController\ContratoController@get_directorio')->name('get-directorio');
Route::get('/pagos/documento/{docs}', 'webController\ContratoController@docs')->name('get-docs');

//Ruta Pago
Route::get('/pago/vista/{id}', 'webController\PagoController@mostrar_pago')->name('mostrar-pago');

// Ruta Validacion
Route::post('/supre/validacion/Rechazado', 'webController\supreController@supre_rechazo')->name('supre-rechazo');
Route::post('/supre/validacion/Validado', 'webController\supreController@supre_validado')->name('supre-validado');
Route::get('/supre/validacion/pdf/{id}', 'webController\supreController@valsupre_pdf')->name('valsupre-pdf');
Route::get('/supre/pdf/{id}', 'webController\supreController@supre_pdf')->name('supre-pdf');

/**
 * Middleware con permisos de los usuarios de autenticacion
 */
Route::middleware(['auth'])->group(function () {
    /**
     * Desarrollado por Adrian y Daniel
     */
    //Route::get('/alumnos/indice', 'webController\AlumnoController@index')
        //   ->name('alumnos.index')->middleware('can:alumnos.index');
    Route::get('/alumnos/indice', 'webController\AlumnoController@index')
        ->name('alumnos.index')->middleware('can:alumnos.index');

    Route::get('alumnos/sid', 'webController\AlumnoController@create')
        ->name('alumnos.preinscripcion')->middleware('can:alumnos.inscripcion-paso1');
    Route::post('/alumnos/save', 'webController\AlumnoController@store')->name('alumnos.save');
    // alumnos
    Route::get('/alumnos/preinscripcion/paso2/{id}', 'webController\AlumnoController@steptwo')->name('alumnos.preinscripcion.paso2')
    ->middleware('can:alumnos.inscripcion-paso2');
    Route::get('alumnos/sid-paso2/{id}', 'webController\AlumnoController@show')
        ->name('alumnos.presincripcion-paso2')->middleware('can:alumnos.inscripcion-paso3');
    Route::post('alumnos/sid/update', 'webController\AlumnoController@update')->name('alumnos.update-sid');
    Route::post('alumnos/sid/update_pregistro', 'webController\AlumnoController@update_pregistro')->name('alumnos.update.documentos.registro');
    // modificacion alumnos
    Route::get('alumnos/modificar/sid-paso2/{id}', 'webController\AlumnoRegistradoController@edit')->name('alumnos.update.registro')
    ->middleware('can:alumno.inscrito.edit');
    // modificar la preinscripcion
    Route::get('alumnos/modificar/sid/{id}', 'webController\AlumnoController@showUpdate')->name('alumnos.presincripcion-modificar');
    Route::put('alumnos/sid/modificar/{idAspirante}', 'webController\AlumnoController@updateSid')->name('sid.modificar');
    Route::get('alumnos/sid/documento/{nocontrol}', 'webController\AlumnoRegistradoController@getDocumentoSid')->name('documento.sid');
    // nueva ruta
    Route::get('alumnos/registrados/{id}', 'webController\AlumnoRegistradoController@show')->name('alumnos.inscritos.detail')
    ->middleware('can:alumno.inscrito.show');
    Route::get('alumnos/registrados', 'webController\AlumnoRegistradoController@index')->name('alumnos.inscritos')
    ->middleware('can:alumnos.inscritos.index');
    // supre
    Route::post("/supre/save","webController\supreController@store")->name('store-supre');
    // documentos pdf Desarrollado por Adrian
    // Route::get('/exportarpdf/presupuestaria', 'webController\presupuestariaController@export_pdf')->name('presupuestaria');
    Route::get('/exportarpdf/contratohonorarios', 'webController\presupuestariaController@propa')->name('contratohonorarios');
    Route::get('/exportarpdf/solicitudsuficiencia/{id}', 'webController\presupuestariaController@export_pdf')->name('solicitudsuficiencia');
    Route::post('/alumnos/sid/cursos', 'webController\AlumnoController@getcursos');

    /**
     * ============================================================================================================================================
     * CURSOS
     * ============================================================================================================================================
     */
    Route::get('/curso/inicio', 'webController\CursosController@index')->name('curso-inicio')->middleware('can:cursos.index');
    Route::get('/cursos/crear', 'webController\CursosController@create')->name('frm-cursos')->middleware('can:cursos.create');
    Route::get('/cursos/editar-catalogo/{id}', 'webController\CursosController@show')->name('cursos-catalogo.show')->middleware('can:cursos.show');
   // Route::get('/cursos/crear', 'webController\CursoValidadoController@cv_crear')->name('cv_crear');
   Route::put('/cursos/actualiza-catalogo/{id}', 'webController\CursosController@update')->name('cursos-catalogo.update')->middleware('can:cursos.update');
   Route::post('cursos/guardar-catalogo', 'webController\CursosController@store')->name('cursos.guardar-catalogo')->middleware('can:cursos.store');
   Route::get('/cursos/especialidad_by_area/{id_especialidad}', 'webController\CursosController@get_by_area')->name('cursos.get_by_area');
    /**
     * obtener toda la información del curso por id
     */
    Route::get('cursos/get_by_id/{idCurso}', 'webController\CursosController@get_by_id')->name('cursos.get_by_id');
    /**
     * ============================================================================================================================================
     * CURSOS END
     * ============================================================================================================================================
     */

    /**
     * UNIDADES DE CAPACITACION
     */
    Route::get('/unidades/unidades_by_ubicacion/{ubicacion}', 'webController\UnidadController@ubicacion')->name('unidades.get_by_ubicacion');
    /**
     * contratos Desarrollando por Daniel
     */
    Route::get('/contratos/crear/{id}', 'webController\ContratoController@create')->name('contratos.create');
    Route::get('/', function () {
        return view('layouts.pages.home');
    });
    Route::get('/home', function() {
        return view('layouts.pages.home');
    })->name('home');

    /***
     * Desarrollado por Orlando
     */

    // Crea pago
    Route::get('/pago/inicio', 'webController\PagoController@index')->name('pago-inicio');
    Route::get('/pago/crear/{id}', 'webController\PagoController@crear_pago')->name('pago-crear');
    Route::post('/pago/guardar', 'webController\PagoController@guardar_pago')->name('pago-guardar');
    Route::get('/pago/modificar', 'webController\PagoController@modificar_pago')->name('pago-modificar');
    Route::post('/pago/fill', 'webController\PagoController@fill');
    // cambiando status
    Route::get('/pago/verificar_pago/{id}', 'webController\PagoController@show')->name('pago.verificarpago');
    Route::post('/pago/validar_pago', 'webController\PagoController@guardar_pago')->name('pago.validar');
    Route::get('/pago/validacion/{idfolio}', 'webController\PagoController@pago_validar')->name('pago.validacion');

    // Crea instructor
    Route::get('/instructor/inicio', 'webController\InstructorController@index')->name('instructor-inicio');
    Route::get('/instructor/crear', 'webController\InstructorController@crear_instructor')->name('instructor-crear');
    Route::post('/instructor/guardar', 'webController\InstructorController@guardar_instructor')->name('instructor-guardar');
    Route::get('/instructor/ver/{id}', 'webController\InstructorController@ver_instructor')->name('instructor-ver');
    Route::get('/instructor/add/perfil-profesional/{id}', 'webController\InstructorController@add_perfil')->name('instructor-perfil');
    Route::get('/instructor/add/curso-impartir/{id}','webController\InstructorController@add_cursoimpartir')->name('instructor-curso');
    Route::post('/perfilinstructor/guardar', 'webController\InstructorController@perfilinstructor_save')->name('perfilinstructor-guardar');
    Route::get('/instructor/curso-impartir/form/{id}/{idins}', 'webController\InstructorController@cursoimpartir_form')->name('cursoimpartir-form');
    Route::get('/instructor/crear-institucional/{id}', 'webController\InstructorController@institucional')->name('instructor-institucional-crear');
    Route::post('/instructor/institucional/guardar', 'webController\InstructorController@institucional_save')->name('instructor-institucional-save');

    // Solicitud de Suficiencia Presupuestal
    Route::get('/supre/solicitud/inicio', 'webController\supreController@solicitud_supre_inicio')
           ->name('supre-inicio')->middleware('can:supre.index');
    Route::get('/supre/solicitud/crear', 'webController\supreController@frm_formulario')
           ->name('frm-supre')->middleware('can:supre.create');
    Route::post('/supre/solicitud/guardar',"webController\supreController@store")
           ->name('solicitud-guardar');
    Route::get('/supre/solicitud/modificar/{id}', 'webController\supreController@solicitud_modificar')
         ->name('modificar_supre')->middleware('can:supre.edit');
    Route::post('/supre/solicitud/mod-save',"webController\supreController@solicitud_mod_guardar")->name('supre-mod-save');

    // Validar Cursos
    Route::get('/cursos/inicio', 'webController\CursoValidadoController@cv_inicio')->name('cursos.index');
    Route::post('/cursos/fill1', 'webController\CursoValidadoController@fill1');
    Route::post("/cursos/guardar","webController\CursoValidadoController@cv-guardar")->name('addcv');

    // Validacion de Suficiencia Presupuestal
    Route::get('/supre/validacion/inicio', 'webController\supreController@validacion_supre_inicio')->name('vasupre-inicio');
    Route::get('/supre/validacion/{id}', 'webController\supreController@validacion')->name('supre-validacion');
    /**
     * agregado en 06 de marzo del 2020
     */
    Route::get('/convenios/indice', 'webController\ConveniosController@index')->name('convenios.index');
    Route::get('/convenios/crear', 'webController\ConveniosController@create')->name('convenio.create');
    Route::post('/convenios/guardar', 'webController\ConveniosController@store')->name('convenios.store');
    //Route::get('/convenios/show/{id}', 'UserProfileController@show')->name('convenios.show');
    Route::get('/convenios/edit/{id}', 'webController\ConveniosController@edit')->name('convenios.edit');
    /**
     * agregando financiero rutas -- DMC
     */
    Route::get('financiero/indice', 'webController\FinancieroController@index')
           ->name('financiero.index');
});
