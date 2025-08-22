<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Grupo\GrupoController;

// Grupo unificado de rutas de Grupos (incluye agenda)
Route::group(['middleware' => ['auth'], 'prefix' => 'grupos', 'as' => 'grupos.'], function () {
    // Gestión general de grupos
    Route::get('/inicio', [GrupoController::class, 'index'])->name('index');
    Route::get('/crear', [GrupoController::class, 'create'])->name('crear');
    Route::get('/editar/{id}/{curp?}', [GrupoController::class, 'editarGrupo'])->name('editar');
    Route::post('/editar/{grupo_id}', [GrupoController::class, 'eliminarAlumno'])->name('eliminar.alumno');
    Route::post('/registrar', [GrupoController::class, 'store'])->name('store');
    Route::get('/localidades/{municipioId}', [GrupoController::class, 'getLocalidades'])->name('localidades');
    Route::get('/organismo/{organismoId}', [GrupoController::class, 'getOrganismoInfo'])->name('organismo.info');
    Route::post('/guardar/seccion/grupo', [GrupoController::class, 'guardarSeccionGrupo'])->name('guardar.seccion.grupo');

    // Asignación de alumnos
    Route::match(['get', 'post'], '/asignar/alumnos', [GrupoController::class, 'asignarAlumnos'])->name('asignar.alumnos');

    // Rutas de Agenda del Grupo (preinscripción)
    Route::get('{grupo}/agenda', [GrupoController::class, 'getAgenda'])->name('agenda.index');
    Route::post('{grupo}/agenda', [GrupoController::class, 'storeAgenda'])->name('agenda.store');
    Route::put('{grupo}/agenda/{agenda}', [GrupoController::class, 'updateAgenda'])->name('agenda.update');
    Route::delete('{grupo}/agenda/{agenda}', [GrupoController::class, 'destroyAgenda'])->name('agenda.destroy');

    Route::post('{grupo_id}/turnar', [GrupoController::class, 'turnarGrupo'])->name('turnar');
});
