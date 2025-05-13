<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function(){    
    Route::get('/consultas/pagos', 'Consultas\pagosController@index')->name('consultas.pagos')->middleware('can:consultas.pagos');
    Route::post('/consultas/pagos', 'Consultas\pagosController@index')->name('consultas.pagos')->middleware('can:consultas.pagos');
    Route::post('/consultas/pagos/excel', 'Consultas\pagosController@excel')->name('consultas.pagos.excel')->middleware('can:consultas.pagos');
});