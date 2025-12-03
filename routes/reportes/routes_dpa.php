<?php
use Illuminate\Support\Facades\Route;
Route::middleware(['auth'])->group(function(){    
    Route::get('/reportes/dpa', 'reportesController\dpaController@index')->name('reportes.dpa')->middleware('can:reportes.dpa');
    Route::post('/reportes/dpa', 'reportesController\dpaController@index')->middleware('can:reportes.dpa');
    Route::get('/reportes/dpa/generar', 'reportesController\dpaController@generar')->name('reportes.dpa.generar')->middleware('can:reportes.dpa');
    Route::post('/reportes/dpa/generar', 'reportesController\dpaController@generar')->middleware('can:reportes.dpa');    
});