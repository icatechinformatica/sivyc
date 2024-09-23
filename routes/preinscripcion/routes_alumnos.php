<?php
use Illuminate\Support\Facades\Route;
Route::middleware(['auth'])->group(function(){        
    Route::get('/preinscripcion/alumnos', 'Preinscripcion\alumnosController@index')->name('preinscripcion.alumnos')
    ->middleware('can:preinscripcion.alumnos');   
});