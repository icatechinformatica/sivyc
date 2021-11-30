<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::post('login', 'ApiController\Api\PassportController@login');
// Route::get('signup', 'ApiController\Api\PassportController@registerData');
// // modificacion de api rest

 Route::group(['middleware' => 'api'], function () {

     Route::apiResource('cursos', 'ApiController\CursosController');
     Route::apiResource('Instructores', 'ApiController\InstructoresController');
     Route::get('Instructoreshow/{nombre}/{apaterno}/{apmaterno}', 'ApiController\InstructoresController@show');
     Route::resource('catalogo-cursos', 'ApiController\CatalogoCursoController');
     Route::apiResource('areas', 'ApiController\AreaController');
     Route::apiResource('especialidades', 'ApiController\EspecialidadController');
     Route::apiResource('municipios', 'ApiController\MunicipioController');
     Route::post('updateCursos/{id}', 'ApiController\CursosController@update');
     Route::post('updateInstructores/{id}', 'ApiController\InstructoresController@update');
     Route::post('updateAreas/{id}', 'ApiController\AreaController@update');
     Route::post('updateEspecialidades/{id}', 'ApiController\EspecialidadController@update');
     Route::post('updateMunicipios/{id}', 'ApiController\MunicipioController@update');
     Route::apiResource('estados', 'ApiController\EstadoController');
     Route::apiResource('directorio', 'ApiController\DirectorioController');
     Route::apiResource('acceso', 'ApiController\AccesoController');
     Route::apiResource('unidad', 'ApiController\UnidadController');
     Route::apiResource('alumnopre', 'ApiController\AlumnoController');
     Route::post('updateUnidades/{id}', 'ApiController\UnidadController@update');
     Route::post('alumno-inscrito/{id}', 'ApiController\AlumnoController@update_alumno');
     Route::apiResource('alumno-inscrito', 'ApiController\AlumnoRegistradoController');
     Route::apiResource('inscripcion', 'ApiController\InscripcionController');
     Route::post('inscripcion/{id}/{matricula}', 'ApiController\InscripcionController@update');
     Route::apiResource('calificacion', 'ApiController\CalificacionController');
     Route::post('calificacion/{idcurso}/{matricula}', 'ApiController\CalificacionController@update');
     Route::post('updateCatalogoCurso/{id}', 'ApiController\CatalogoCursoController@update');
     Route::apiResource('afolios', 'ApiController\AfoliosController');
     Route::post('afolios/{id}', 'ApiController\AfoliosController@update');
     Route::apiResource('folios', 'ApiController\FolioController');
     Route::post('folios/{curso}/{id}', 'ApiController\FolioController@update');
     Route::get('instructores/perfil/{id}', 'ApiController\InstructorPerfilController@show');
     Route::post('cursos/actualizar/{id}', 'ApiController\CursosController@updateCursosCalificaciones');

 });


Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'ApiController\Api\PassportController@login');
    Route::post('register', 'ApiController\Api\PassportController@signUp');

    Route::group([
      'middleware' => 'auth:api_sice'
    ], function() {
        Route::post('details', 'ApiController\Api\PassportController@details');
        Route::apiResource('afolios-check', 'ApiController\AfoliosController');
        Route::post('logout', 'ApiController\Api\PassportController@logout');
    });
});


// apis del sistema de instructores
// registro
Route::get('instructores/{curp}', 'ApiController\ApisInstructores\RegistroController@getInstructor');
// asistencia
Route::get('instructores/asistencia/{clave}', 'ApiController\ApisInstructores\AsistenciaController@getCurso');
Route::get('instructores/asistencia/alumnos/{idCurso}', 'ApiController\ApisInstructores\AsistenciaController@getAlumnos');
Route::post('instructores/asistencia/alumnos/update', 'ApiController\ApisInstructores\AsistenciaController@updateAsistencias');
Route::get('instructores/asistencia/pdf/{clave}', 'ApiController\ApisInstructores\AsistenciaController@getCursoAsistenciaPdf');
Route::get('instructores/asistencia/curso/{id}', 'ApiController\ApisInstructores\AsistenciaController@updateAsisFinalizado');


// php artisan serve --host 192.168.100.176
// api movil
Route::post('sivycMovil/login', 'ApiController\ApisMovil\LoginMovil@login');
Route::post('sivycMovil/updateRead', 'ApiController\ApisMovil\HomeMovil@updateRead');
Route::post('sivycMovil/updateToken', 'ApiController\ApisMovil\HomeMovil@updateToken');
Route::post('sivycMovil/getNotificaciones', 'ApiController\ApisMovil\HomeMovil@getNotificaciones');