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
    Route::put('updateCursos/{id}', 'ApiController\CursosController@update');
});
