<?php
use Illuminate\Support\Facades\Route;
Route::middleware(['auth'])->group(function(){    
    Route::get('/reportes/dv', 'reportesController\dvController@index')->name('reportes.dv')->middleware('can:reportes.dv');
    Route::post('/reportes/dv', 'reportesController\dvController@index')->name('reportes.dv')->middleware('can:reportes.dv');
    Route::get('/reportes/dv/generar', 'reportesController\dvController@generar')->name('reportes.dv.generar')->middleware('can:reportes.dv');
    Route::post('/reportes/dv/generar', 'reportesController\dvController@generar')->name('reportes.dv.generar')->middleware('can:reportes.dv');    
});