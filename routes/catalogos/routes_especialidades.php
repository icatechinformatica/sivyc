<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function(){    
    Route::get('/catalogos/especialidades/xls', 'webController\EspecialidadesController@xls')->name('catalogos.especialidades.xls')->middleware('can:especialidades.inicio');
    Route::post('/catalogos/especialidades/xls', 'webController\EspecialidadesController@xls')->name('catalogos.especialidades.xls')->middleware('can:especialidades.inicio');
});