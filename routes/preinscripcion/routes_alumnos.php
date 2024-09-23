<?php
use Illuminate\Support\Facades\Route;
Route::middleware(['auth'])->group(function(){        
    Route::get('/preinscripcion/alumnos', 'preinscripcion\alumnosController@index')->name('preinscripcion.alumnos')
    ->middleware('can:preinscripcion.alumnos');   
});

/*
    Route::get('alumnos/registrados/{id}', 'webController\AlumnoRegistradoController@show')->name('alumnos.inscritos.detail')
    ->middleware('can:alumno.inscrito.show');
    Route::get('alumnos/registrados', 'webController\AlumnoRegistradoController@index')->name('alumnos.inscritos')
    ->middleware('can:alumnos.inscritos.index');
*/