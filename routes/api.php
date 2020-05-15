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

Route::group(['middleware' => ['cors']], function(){
	Route::apiResource('cursos', 'ApiController\CursosController');
    Route::apiResource('Instructores', 'ApiController\InstructoresController');
    Route::get('Instructoreshow/{nombre}/{apaterno}/{apmaterno}', 'ApiController\InstructoresController@show');
    Route::apiResource('catalogo-cursos', 'ApiController\CatalogoCursoController');
    Route::apiResource('areas', 'ApiController\AreaController');
    Route::apiResource('especialidades', 'ApiController\EspecialidadController');
    Route::apiResource('municipios', 'ApiController\MunicipioController');
    Route::post('updateCursos/{id}', 'ApiController\CursosController@update');
    Route::post('updateInstructores/{id}', 'ApiController\InstructoresController@update');
    Route::post('updateCatalogo-cursos/{id}', 'ApiController\CatalogoCursoController@update');
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
});
