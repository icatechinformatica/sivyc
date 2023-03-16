<?php
use Illuminate\Support\Facades\Route;



/**Unidades de medida */
Route::get('/vista/pat/um', 'PatController\UmController@index')->name('pat.unidadesmedida.mostrar');
//Eliminar
//Route::get('/vista/pat/um/{id}', 'PatController\UmController@destroy')->name('unidadesm.destroy');
//inactivar o desactivar con ajax
Route::post('/vista/pat/um/status/', 'PatController\UmController@status')->name('unidadesm.mod.status');
//Agregar
Route::post('/vista/pat/um/save', 'PatController\UmController@store')->name('unidadesm.guardar');
//Editar
//Route::get('/vista/pat/um/show/{id}', 'PatController\UmController@show')->name('unidadesm.edit.show');
Route::post('/vista/pat/um/show/', 'PatController\UmController@show')->name('unidadesm.edit.show');

Route::post('/vista/pat/um/update/{id}', 'PatController\UmController@update')->name('unidadesm.update');


/**Funciones */
Route::get('/vista/pat/funciones', 'PatController\FuncionesController@index')->name('pat.funciones.mostrar');
//Eliminar
Route::get('/vista/pat/funciones/{id}', 'PatController\FuncionesController@destroy')->name('funciones.destroy');
//Agregar
Route::post('/vista/pat/funciones/save', 'PatController\FuncionesController@store')->name('funciones.guardar');
//Editar
Route::get('/vista/pat/funciones/show/{id}', 'PatController\FuncionesController@show')->name('funciones.edit.show');
Route::post('/vista/pat/funciones/update/{id}', 'PatController\FuncionesController@update')->name('funciones.update');


/**Procedimientos */
Route::get('/vista/pat/procedimientos/{id}', 'PatController\ProcedController@index')->name('pat.proced.mostrar');
//Eliminar
Route::get('/vista/pat/procedimientos/delete/{idd}/{id}', 'PatController\ProcedController@destroy')->name('proced.destroy');
//Agregar
Route::post('/vista/pat/procedimientos/save/{id}', 'PatController\ProcedController@store')->name('proced.guardar');
//Editar
Route::get('/vista/pat/procedimientos/show/{idedi}/{id}', 'PatController\ProcedController@show')->name('pat.proced.edit.show');
Route::post('/vista/pat/procedimientos/update/{idedi}/{id}', 'PatController\ProcedController@update')->name('proced.update');
