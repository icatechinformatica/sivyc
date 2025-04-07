<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function(){    
    Route::get('/solicitudes/vbgrupos', 'Solicitudes\vbgruposController@index')->name('solicitudes.vb.grupos')->middleware('can:solicitudes.vb.grupos');
    Route::post('/solicitudes/vbgrupos', 'Solicitudes\vbgruposController@index')->name('solicitudes.vb.grupos')->middleware('can:solicitudes.vb.grupos');    
    Route::get('/solicitudes/vbgrupos/vistobueno', 'Solicitudes\vbgruposController@vistobueno')->name('solicitudes.vb.grupos.vistobueno')->middleware('can:solicitudes.vb.grupos');
    Route::post('/solicitudes/vbgrupos/vistobueno', 'Solicitudes\vbgruposController@vistobueno')->name('solicitudes.vb.grupos.vistobueno')->middleware('can:solicitudes.vb.grupos');
    Route::post('/solicitudes/vbgrupos/buscar', 'Solicitudes\vbgruposController@autodata')->name('solicitudes.vb.grupos.buscar')->middleware('can:solicitudes.vb.grupos');
});