<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function(){    
    Route::get('/grupos/recibos/buscar', 'Grupos\recibosController@buscar')->name('grupos.recibos.buscar')->middleware('can:grupos.recibos');
    Route::post('/grupos/recibos/buscar', 'Grupos\recibosController@buscar')->name('grupos.recibos.buscar')->middleware('can:grupos.recibos');

    Route::get('/grupos/recibos/index', 'Grupos\recibosController@index')->name('grupos.recibos')->middleware('can:grupos.recibos');
    Route::post('/grupos/recibos/index', 'Grupos\recibosController@index')->name('grupos.recibos')->middleware('can:grupos.recibos');
    Route::post('/grupos/recibos/asignar', 'Grupos\recibosController@asignar')->name('grupos.recibos.asignar')->middleware('can:grupos.recibos');
    Route::post('/grupos/recibos/deshacer', 'Grupos\recibosController@deshacer')->name('grupos.recibos.deshacer')->middleware('can:grupos.recibos');
    Route::post('/grupos/recibos/modificar', 'Grupos\recibosController@modificar')->name('grupos.recibos.modificar')->middleware('can:grupos.recibos');
    Route::post('/grupos/recibos/subir', 'Grupos\recibosController@subir')->name('grupos.recibos.subir')->middleware('can:grupos.recibos');
    Route::post('/grupos/recibos/enviar', 'Grupos\recibosController@enviar')->name('grupos.recibos.enviar')->middleware('can:grupos.recibos');
});
Route::get('/grupos/recibos/pdf', 'Grupos\recibosController@pdfRecibo')->name('grupos.recibos.pdf');
Route::post('/grupos/recibos/pdf', 'Grupos\recibosController@pdfRecibo')->name('grupos.recibos.pdf');