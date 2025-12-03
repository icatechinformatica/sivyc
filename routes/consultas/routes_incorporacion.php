<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function(){        
    /** MODULO DE CONSULTA DE INCORPORACION LABORAL DE EGRESADOS */
    Route::get('consultas/bolsa/alumnos', 'Consultas\BolsaTrabController@index')->name('consultas.bolsa.index');    
    Route::post('consultas/bolsa/alumnos', 'Consultas\BolsaTrabController@index'); 
    Route::post('autocomplet/bolsa/cursos', 'Consultas\BolsaTrabController@autocomplete_cursos')->name('consulta.bolsa.autocomp'); //Post para el autocompletado de cursos    
    Route::post('consultas/bolsa/reporte', 'Consultas\BolsaTrabController@crear_reporte_excel')->name('consultas.bolsa.reporte'); //Generar reporte de excel
    Route::get('consultas/bolsa/guardar', 'Consultas\BolsaTrabController@guardar')->name('consultas.bolsa.guardar'); //get para datos de incorporación
    Route::post('consultas/bolsa/guardar', 'Consultas\BolsaTrabController@guardar'); //Post para datos de incorporación
    Route::post('consultas/bolsa/ver', 'Consultas\BolsaTrabController@ver')->name('consultas.bolsa.ver'); 
});