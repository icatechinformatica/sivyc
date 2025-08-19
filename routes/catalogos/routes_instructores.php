<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Catalogos\Instructor\InstructorController;

Route::middleware(['auth'])->group(function(){
    Route::post('/instructores/cursos/autocomplete', 'webController\InstructorController@cursosAutocomplete')->name('instructores.cursos.autocomplete');

    //Proyecto 2026 Rutas de Instructores
    Route::get('/instructores/registro/index', [InstructorController::class, 'index'])->name('instructor-inicio_new');
    Route::get('/instructor/crear/instructor', [InstructorController::class, 'vista_crear_instructor'])->name('instructor-vista-crear_new');
});
