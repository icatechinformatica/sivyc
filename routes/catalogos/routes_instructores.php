<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function(){    
    Route::post('/instructores/cursos/autocomplete', 'webController\InstructorController@cursosAutocomplete')->name('instructores.cursos.autocomplete');
});