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
	Route::resource('cursos', 'ApiController\CursosController');
    Route::resource('Instructores', 'ApiController\InstructoresController');
    Route::get('Instructoreshow/{nombre}/{apaterno}/{apmaterno}', 'ApiController\InstructoresController@show');
    Route::resource('catalogo-cursos', 'ApiController\CatalogoCursoController');
    Route::resource('areas', 'ApiController\AreaController');
    Route::resource('especialidades', 'ApiController\EspecialidadController');

});
