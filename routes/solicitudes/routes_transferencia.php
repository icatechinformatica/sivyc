<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function(){    
    Route::get('/solicitudes/transferencia/index', 'Solicitudes\transferenciaController@index')->name('solicitudes.transferencia.index')->middleware('can:solicitudes.transferencia');
    Route::post('/solicitudes/transferencia/index', 'Solicitudes\transferenciaController@index')->name('solicitudes.transferencia.index')->middleware('can:solicitudes.transferencia');
    Route::get('/solicitudes/transferencia/marcar', 'Solicitudes\transferenciaController@marcar')->name('solicitudes.transferencia.marcar')->middleware('can:solicitudes.transferencia');
    Route::post('/solicitudes/transferencia/marcar', 'Solicitudes\transferenciaController@marcar')->name('solicitudes.transferencia.marcar')->middleware('can:solicitudes.transferencia');
    Route::post('/solicitudes/transferencia/generar', 'Solicitudes\transferenciaController@generar')->name('solicitudes.transferencia.generar')->middleware('can:solicitudes.transferencia');
    Route::get('/solicitudes/transferencia/deshacer', 'Solicitudes\transferenciaController@deshacer')->name('solicitudes.transferencia.deshacer')->middleware('can:solicitudes.deshacer');
    Route::post('/solicitudes/transferencia/deshacer', 'Solicitudes\transferenciaController@deshacer')->name('solicitudes.transferencia.deshacer')->middleware('can:solicitudes.deshacer');
    Route::post('/solicitudes/transferencia/excel', 'Solicitudes\transferenciaController@excel')->name('solicitudes.transferencia.excel')->middleware('can:solicitudes.transferencia');
    //Route::get('/solicitudes/transferencia/pagados', 'Solicitudes\transferenciaController@marcar_pagados')->name('solicitudes.transferencia.pagados')->middleware('can:solicitudes.transferencia');
    Route::get('/solicitudes/transferencia/pagado', 'Solicitudes\transferenciaController@pagado')->name('solicitudes.transferencia.pagado')->middleware('can:solicitudes.transferencia');
    Route::post('/solicitudes/transferencia/pagado', 'Solicitudes\transferenciaController@pagado')->name('solicitudes.transferencia.pagado')->middleware('can:solicitudes.transferencia');
    
});