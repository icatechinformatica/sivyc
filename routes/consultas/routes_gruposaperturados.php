<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'can:consultas.gruposaperturados'])->namespace('Consultas')->prefix('consultas/gruposaperturados')->as('consultas.gruposaperturados')
    ->group(function () {
        Route::match(['get', 'post'], '/', 'gruposaperturadosController@index')->name('');
        Route::post('xls', 'gruposaperturadosController@xls')->name('.xls');
});