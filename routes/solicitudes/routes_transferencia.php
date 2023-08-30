<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function(){    
    Route::get('/solicitudes/transferencia/index', 'solicitudes\transferenciaController@index')->name('solicitudes.transferencia.index')->middleware('can:solicitudes.transferencia');
    Route::post('/solicitudes/transferencia/index', 'solicitudes\transferenciaController@index')->name('solicitudes.transferencia.index')->middleware('can:solicitudes.transferencia');
    Route::get('/solicitudes/transferencia/marcar', 'solicitudes\transferenciaController@marcar')->name('solicitudes.transferencia.marcar')->middleware('can:solicitudes.transferencia');
    Route::post('/solicitudes/transferencia/marcar', 'solicitudes\transferenciaController@marcar')->name('solicitudes.transferencia.marcar')->middleware('can:solicitudes.transferencia');
    Route::post('/solicitudes/transferencia/generar', 'solicitudes\transferenciaController@generar')->name('solicitudes.transferencia.generar')->middleware('can:solicitudes.transferencia');
    Route::get('/solicitudes/transferencia/deshacer', 'solicitudes\transferenciaController@deshacer')->name('solicitudes.transferencia.deshacer')->middleware('can:solicitudes.deshacer');
    Route::post('/solicitudes/transferencia/deshacer', 'solicitudes\transferenciaController@deshacer')->name('solicitudes.transferencia.deshacer')->middleware('can:solicitudes.deshacer');
});