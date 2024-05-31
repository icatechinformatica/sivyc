<?php
use Illuminate\Support\Facades\Route;
Route::middleware(['auth'])->group(function(){    
    Route::get('/reportes/pat', 'ReportesController\patController@index')->name('reportes.pat')->middleware('can:reportes.pat');
    Route::post('/reportes/pat', 'ReportesController\patController@index')->name('reportes.pat')->middleware('can:reportes.pat');
    Route::get('/reportes/pat/generar', 'ReportesController\patController@generar')->name('reportes.pat.generar')->middleware('can:reportes.pat');
    Route::post('/reportes/pat/generar', 'ReportesController\patController@generar')->name('reportes.pat.generar')->middleware('can:reportes.pat');    
});