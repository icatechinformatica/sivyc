<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function(){    
    Route::get('/consultas/pagos', 'Consultas\pagosController@index')->name('solicitudes.pagos')->middleware('can:solicitudes.pagos');
    Route::post('/consultas/pagos', 'Consultas\pagosController@index')->name('solicitudes.pagos')->middleware('can:solicitudes.pagos');
    Route::post('/consultas/pagos/excel', 'Consultas\pagosController@excel')->name('solicitudes.pagos.excel')->middleware('can:solicitudes.pagos');    
});