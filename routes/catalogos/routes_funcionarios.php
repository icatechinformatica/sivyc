<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function(){
    Route::get('/catalogos/funcionarios/index', 'Catalogos\funcController@index')->name('catalogos.funcionarios.inicio')->middleware('can:funcionarios.inicio');
    Route::post('/catalogos/funcionarios/index', 'Catalogos\funcController@index')->middleware('can:funcionarios.inicio');
    Route::post('/catalogos/funcionarios/store', 'Catalogos\funcController@store')->name('catalogos.funcionarios.store');
    Route::post('/catalogos/funcionarios/getdatos', 'Catalogos\funcController@obtener_datos')->name('frm.obtener.datos');
    Route::get('/catalogos/funcionarios/prueba', 'Catalogos\funcController@prueba');
});
