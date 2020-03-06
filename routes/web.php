<?php
//Rutas Orlando

use App\Http\Controllers\webController\InstructorController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

//Crea instructor
Route::get('/instructor/inicio', 'webController\InstructorController@index')->name('instructor-inicio');
Route::get('/instructor/crear', 'webController\InstructorController@crear_instructor')->name('instructor-crear');
Route::post('/instructor/guardar', 'webController\InstructorController@guardar_instructor')->name('instructor-guardar');
Route::get('/instructor/ver/{id}', 'webController\InstructorController@ver_instructor')->name('instructor-ver');
Route::get('/instructor/add/perfil-profesional/{id}', 'webController\InstructorController@add_perfil')->name('instructor-perfil');
Route::get('/instructor/add/curso-impartir/{id}','webController\InstructorController@add_cursoimpartir')->name('instructor-curso');
Route::post('/perfilinstructor/guardar', 'webController\InstructorController@perfilinstructor_save')->name('perfilinstructor-guardar');
Route::post('/instructor/curso-impartir/guardar/{id}{idInstructor}', 'webController\InstructorController@cursoimpartir_save')->name('cursoimpartir-guardar');

//Validar Cursos
Route::get('/validar-curso/inicio', 'webController\CursoValidadoController@cv_inicio')->name('cv_inicio');
Route::get('/validar-curso/crear', 'webController\CursoValidadoController@cv_crear')->name('cv_crear');
Route::post('/validar-curso/fill1', 'webController\CursoValidadoController@fill1');
Route::post("/validar-curso/guardar","webController\CursoValidadoController@cv-guardar")->name('addcv');

//Solicitud de Suficiencia Presupuestal
Route::get('/supre/solicitud/inicio', 'webController\supreController@solicitud_supre_inicio')->name('supre-inicio');
Route::get('/supre/solicitud/crear', 'webController\supreController@solicitud_formulario')->name('solicitud_crear');
Route::post("/supre/solicitud/guardar","webController\supreController@solicitud_guardar")->name('addsupre');
Route::get('/supre/solicitud/modificar/{id_supre}', 'webController\supreController@solicitud_modificar')->name('modificar_supre');

//Validacion de Suficiencia Presupuestal
Route::get('/supre/validacion/inicio', 'webController\supreController@validacion_supre_inicio')->name('vasupre-inicio');
Route::get('/supre/validacion', 'webController\supreController@validacion')->name('supre-validacion');

//Crea pago
Route::get('/pago/inicio', 'webController\PagoController@index')->name('pago-inicio');
Route::get('/pago/crear', 'webController\PagoController@crear_pago')->name('pago-crear');
Route::get('/pago/guardar', 'webController\PagoController@guardar_pago')->name('pago-guardar');
Route::get('/pago/modificar', 'webController\PagoController@modificar_pago')->name('pago-modificar');
Route::post('/pago/fill', 'webController\PagoController@fill');
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

Route::get('/curso/crear', function () {
    return view('layouts.pages.frmcursos');
})->name('frm-cursos');
Route::get('/contratos/add-contrato', 'webController\ContratoController@create')->name('contratos.create');
Route::get('/', function () {
    return view('layouts.pages.table');
});
Route::get('/add-convenio', function () {
    return view('layouts.pages.frmconvenio');
})->name('frm-convenio');
Route::get('/usuarios', function(){
    return view('layouts.pages.frmcursos');
})->name('usuarios');

Route::get('/inscripcion/paso2', 'webController\AlumnoController@createpaso2sid')->name('inscripcion-paso2');
Route::get('/exportarpdf/presupuestaria', 'webController\presupuestariaController@index')->name('presupuestaria');
Route::get('/exportarpdf/contratohonorarios', 'webController\presupuestariaController@index')->name('contratohonorarios');
Route::get('/exportarpdf/solicitudsuficiencia', 'webController\presupuestariaController@index')->name('solicitudsuficiencia');

/**
 * Metodo post o put exclusivamente
 * elaborado por DMC
 */
Route::post("addsupre","webController\supreController@store")->name('addsupre');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

/**
 * Middleware con permisos
 */
Route::middleware(['auth'])->group(function () {
    Route::get('/alumnos/indice', 'webController\AlumnoController@index')
           ->name('alumnos.index')->middleware('can:alumnos.index');
    Route::post('/alumnos/save', 'webController\AlumnoController@store')->name('alumnos.save');
    Route::get('/inscripcion/paso1', 'webController\AlumnoController@create')
           ->name('alumnos.inscripcion-paso1')->middleware('can:alumnos.inscripcion-paso1');
});
