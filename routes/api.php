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

Route::group(['middleware' => 'auth:api'], function () {

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
    //Route::apiResource('afolios', 'ApiController\AfoliosController');
    Route::post('afolios/{id}', 'ApiController\AfoliosController@update');
    Route::apiResource('folios', 'ApiController\FolioController');
    Route::post('folios/{curso}/{id}', 'ApiController\FolioController@update');
    Route::get('instructores/perfil/{id}', 'ApiController\InstructorPerfilController@show');
    Route::post('cursos/actualizar/{id}', 'ApiController\CursosController@updateCursosCalificaciones');

    Route::post('details', 'ApiController\Api\PassportController@details');
    Route::get('logout', 'ApiController\Api\PassportController@logout');

});


Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'ApiController\Api\PassportController@login');
    Route::post('register', 'ApiController\Api\PassportController@signUp');

    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::post('details', 'ApiController\Api\PassportController@details');
        Route::apiResource('afolios', 'ApiController\AfoliosController');
        Route::post('logout', 'ApiController\Api\PassportController@logout');
    });
});
