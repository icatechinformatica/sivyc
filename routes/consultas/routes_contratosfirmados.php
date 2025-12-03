<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function(){        
    Route::get('/consultas/contratosfirmados', 'Consultas\contratosfirmadosController@index')->name('consultas.contratosfirmados')->middleware('can:consultas.contratosfirmados');
    Route::post('/consultas/contratosfirmados', 'Consultas\contratosfirmadosController@index')->middleware('can:consultas.contratosfirmados');
    Route::post('/consultas/contratosfirmados/xls', 'Consultas\contratosfirmadosController@xls')->name('consultas.contratosfirmados.xls')->middleware('can:consultas.contratosfirmados');
});