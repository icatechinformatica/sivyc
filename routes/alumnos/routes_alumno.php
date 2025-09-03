<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Alumno\AlumnoController;

Route::group(['middleware' => ['auth'], 'prefix' => 'alumnos'], function () {
    Route::get('/cosulta', [AlumnoController::class, 'index'])->name('alumnos.consulta.alumno');
    Route::post('/guardar', [AlumnoController::class, 'store'])->name('alumnos.store');
    Route::post('/consultar_curp', [AlumnoController::class, 'consultarCurp'])->name('alumnos.consultar.curp');
    Route::post('/obtener/datos/curp/{encodeCURP}', [AlumnoController::class, 'obtenerDatosCurp'])->name('alumnos.obtener.datos.curp');

    Route::get('/registro/alumno/{encodeCURP}', [AlumnoController::class, 'verRegistroAlumno'])->name('alumnos.ver.registro.alumno');
    Route::get('/registro/nuevo/alumno/{encodeCURP}/{grupoId?}', [AlumnoController::class, 'nuevoRegistroAlumno'])->name('alumnos.nuevo.registro.alumno');

    Route::post('/guardar/seccion/alumno', [AlumnoController::class, 'guardarSeccionAlumno'])->name('alumnos.guardar.seccion.alumno');

    // Endpoints para selects dinámicos de domicilio
    Route::post('/estados', [AlumnoController::class, 'estadosPorPais'])->name('alumnos.estados.pais');
    Route::post('/municipios', [AlumnoController::class, 'municipiosPorEstado'])->name('alumnos.municipios.estado');
});
