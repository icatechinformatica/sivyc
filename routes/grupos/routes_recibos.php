<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function(){    
    Route::get('/grupos/recibos/index', 'grupos\recibosController@index')->name('grupos.recibos')->middleware('can:grupos.recibos');
    Route::post('/grupos/recibos/index', 'grupos\recibosController@index')->name('grupos.recibos')->middleware('can:grupos.recibos');
    Route::post('/grupos/recibos/asignar', 'grupos\recibosController@asignar')->name('grupos.recibos.asignar')->middleware('can:grupos.recibos');
});