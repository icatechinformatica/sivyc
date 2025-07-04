<?php
use Illuminate\Support\Facades\Route;
Route::middleware(['auth'])->group(function(){
    // Seccion modulo aspirantes instructores
    Route::get('/instructores/aspirantes/index', 'Solicitudes\InstructorAspiranteController@index')->name('aspirante.instructor.index');
    Route::post('/instructores/aspirantes/prevalidar', 'Solicitudes\InstructorAspiranteController@prevalidar')->name('aspirante.instructor.prevalidar');
    Route::post('/instructores/aspirantes/convocar', 'Solicitudes\InstructorAspiranteController@convocar')->name('aspirante.instructor.convocar');
    // Route::post('/instructores/aspirantes/aprobar', 'Solicitudes\InstructorAspiranteController@aprobar')->name('aspirante.instructor.aprobar');
    Route::post('/instructores/aspirantes/rechazar', 'Solicitudes\InstructorAspiranteController@rechazar')->name('aspirante.instructor.rechazar');
    Route::get('/instructores/aspirantes/filter', 'Solicitudes\InstructorAspiranteController@filter')->name('aspirante.instructor.filter');
    Route::get('/instructores/aspirantes/export', 'Solicitudes\InstructorAspiranteController@export')->name('aspirante.instructor.export');
    Route::get('/instructores/whatsapp/rechazomasivo', 'Solicitudes\InstructorAspiranteController@whatsapp_rechazo_masivo')->name('whatsapp.rechazo.masivo');
    //ruta que sera movida para superadministrador
    Route::get('/instructores/whatsapp/restablecerpwd', 'Solicitudes\InstructorAspiranteController@whatsapp_restablecer_pwd')->name('whatsapp.restablecer.pwd');
});
