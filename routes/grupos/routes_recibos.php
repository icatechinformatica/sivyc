<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function(){    
    Route::get('/grupos/recibos/index', 'Grupos\recibosController@index')->name('grupos.recibos')->middleware('can:grupos.recibos');
    Route::post('/grupos/recibos/index', 'Grupos\recibosController@index')->name('grupos.recibos')->middleware('can:grupos.recibos');
    Route::post('/grupos/recibos/asignar', 'Grupos\recibosController@asignar')->name('grupos.recibos.asignar')->middleware('can:grupos.recibos');
});