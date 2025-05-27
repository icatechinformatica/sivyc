<?php
use Illuminate\Support\Facades\Route;
Route::middleware(['auth'])->group(function(){         
    Route::get('/preinscripcion/grupo/{folio}/referencia', 'Preinscripcion\grupoController@referencias')->name('preinscripcion.grupo.referencias')->middleware('can:preinscripcion.grupo');
    Route::get('/preinscripcion/grupo/{folio}/referencia/{alumno}', 'Preinscripcion\grupoController@referencias')->name('preinscripcion.grupo.referencias.alumno')->middleware('can:preinscripcion.grupo');
});