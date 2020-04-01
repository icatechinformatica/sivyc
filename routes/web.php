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

Route::get('/curso/inicio', 'webController\CursosController@index')->name('curso-inicio');

Route::get('/exportarpdf/solicitudsuficiencia', 'webController\presupuestariaController@index')->name('procesodepago');
//Route::get('/home', 'HomeController@index')->name('home');
Auth::routes();

//Ruta supre sin Middleware
Route::get('/supre/solicitud/opc', 'webController\supreController@opcion')->name('solicitud-opcion');
Route::get('/supre/solicitud/folio', 'webController\supreController@solicitud_folios')->name('solicitud-folio');
Route::get('/supre/tabla-pdf/{id}', 'webController\supreController@tablasupre_pdf')->name('tablasupre-pdf');

//Ruta Contrato sin Middleware
Route::get('/Contrato/inicio', 'webController\ContratoController@index')->name('contrato-inicio');
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
Route::get('/directorio/getdirectorio','webController\ContratoController@get_directorio')->name('get-directorio');

//Ruta Pago sin Middleware
Route::get('/pago/vista/{id}', 'webController\PagoController@mostrar_pago')->name('mostrar-pago');

// Ruta Validacion sin middleware
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
    Route::get('/alumnos/indice', 'webController\AlumnoController@index')
           ->name('alumnos.index')->middleware('can:alumnos.index');
    Route::post('/alumnos/save', 'webController\AlumnoController@store')->name('alumnos.save');
    Route::get('/alumnos/paso1', 'webController\AlumnoController@create')
           ->name('alumnos.inscripcion-paso1')->middleware('can:alumnos.create');
    Route::get('/cursos/crear', 'webController\CursosController@create')->name('frm-cursos');
    // supre
    Route::post("/supre/save","webController\supreController@store")->name('store-supre');
    // alumnos
    Route::get('/inscripcion/paso2', 'webController\AlumnoController@createpaso2sid')->name('inscripcion-paso2');
    // documentos pdf Desarrollado por Adrian
    Route::get('/exportarpdf/presupuestaria', 'webController\presupuestariaController@export_pdf')->name('presupuestaria');
    Route::get('/exportarpdf/contratohonorarios', 'webController\presupuestariaController@export_pdf')->name('contratohonorarios');
    Route::get('/exportarpdf/solicitudsuficiencia/{id}', 'webController\presupuestariaController@export_pdf')->name('solicitudsuficiencia');
    /**
     * contratos Desarrollando por Daniel
     */
    Route::get('/contratos/crear/{id}', 'webController\ContratoController@create')->name('contratos.create');
    Route::get('/', function () {
        return view('layouts.pages.home');
    });

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
    Route::post('/instructor/curso-impartir/guardar/{id}{idInstructor}', 'webController\InstructorController@cursoimpartir_save')->name('cursoimpartir-guardar');
    Route::get('/instructor/crear-institucional/{id}', 'webController\InstructorController@institucional')->name('instructor-crear');
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
    // Route::get('/cursos/crear', 'webController\CursoValidadoController@cv_crear')->name('cv_crear');
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
    Route::get('/convenios/show/{id}', 'UserProfileController@show')->name('convenios.show');
    Route::get('/convenios/edit/{id}', 'webController\ConveniosController@edit')->name('convenios.edit');
    /**
     * agregando financiero rutas -- DMC
     */
    Route::get('financiero/indice', 'webController\FinancieroController@index')
           ->name('financiero.index');
});
