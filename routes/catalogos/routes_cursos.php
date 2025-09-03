<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Catalogos\Cursos\CursoController;

Route::middleware(['auth'])->group(function(){
    //Proyecto 2026 Rutas de Cursos
    Route::get('/cursos/registro/index', [CursoController::class, 'index'])->name('curso-inicio_new');
    Route::get('/cursos/registro/crear', [CursoController::class, 'vista_crear_curso'])->name('curso-crear_new');
});
