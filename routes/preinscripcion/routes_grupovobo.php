<?php
use Illuminate\Support\Facades\Route;
Route::middleware(['auth'])->group(function(){           
    /*VINCULACION->PREINSCRIPCION=> NUEVO GRUPO RPN*/
    Route::get('/preinscripcion/grupovb', 'Preinscripcion\grupovoboController@index')->name('preinscripcion.grupovobo')->middleware('can:preinscripcion.grupovobo');
    Route::get('/preinscripcion/grupovb/cmbcursos', 'Preinscripcion\grupovoboController@cmbcursos')->name('preinscripcion.grupovobo.cmbcursos');
    Route::post('/preinscripcion/grupovb/guardar', 'Preinscripcion\grupovoboController@save')->name('preinscripcion.grupovobo.save')->middleware('can:preinscripcion.grupovobo');
    Route::post('/preinscripcion/grupovb/update', 'Preinscripcion\grupovoboController@update')->name('preinscripcion.grupovobo.update')->middleware('can:preinscripcion.grupovobo');
    Route::post('/preinscripcion/grupovb/generar', 'Preinscripcion\grupovoboController@generar')->name('preinscripcion.grupovobo.generar');
    Route::get('/preinscripcion/grupovb/nuevo', 'Preinscripcion\grupovoboController@nuevo')->name('preinscripcion.grupovobo.nuevo');
    Route::post('/preinscripcion/grupovb/nuevo', 'Preinscripcion\grupovoboController@nuevo')->name('preinscripcion.grupovobo.nuevo');
    Route::post('/preinscripcion/grupovb/vobo', 'Preinscripcion\grupovoboController@vobo')->name('preinscripcion.grupovobo.vobo')->middleware('can:preinscripcion.grupovobo');
    Route::post('/preinscripcion/grupovb/turnar', 'Preinscripcion\grupovoboController@turnar')->name('preinscripcion.grupovobo.turnar')->middleware('can:preinscripcion.grupovobo');
    Route::get('/preinscripcion/grupovb/eliminar', 'Preinscripcion\grupovoboController@delete')->name('preinscripcion.grupovobo.eliminar')->middleware('can:preinscripcion.grupovobo');
    Route::post('/preinscripcion/grupovb/comrobante','Preinscripcion\grupovoboController@subir_comprobante')->name('preinscripcion.grupovobo.comprobante');
    Route::get('/preinscripcion/municipio', 'Preinscripcion\grupovoboController@showlm');
    Route::post('/preinscripcion/grupovb/remplazar', 'Preinscripcion\grupovoboController@remplazar')->name('preinscripcion.grupovobo.remplazar');
    Route::get('/preinscripcion/grupovb/cmbinstructor', 'Preinscripcion\grupovoboController@cmbinstructor')->name('preinscripcion.grupovobo.cmbinstructor');
    Route::get('/preinscripcion/grupovb/cmbmuni', 'Preinscripcion\grupovoboController@cmbmuni')->name('preinscripcion.grupovobo.cmbmunicipio');
    Route::get('/preinscripcion/grupovb/cmbrepre', 'Preinscripcion\grupovoboController@cmbrepre')->name('preinscripcion.grupovobo.cmbrepresentante');
    /*VINCULACION->PREINSCRIPCION=>AGENDAR INSTRUCTOR*/
    Route::get('/preinscripcion/grupovb/calendarioShow/{id}','Preinscripcion\grupovoboController@showCalendar')->middleware('can:preinscripcion.grupovobo');
    Route::post('/preinscripcion/grupovb/calendario/guardar','Preinscripcion\grupovoboController@storeCalendar')->middleware('can:agenda.vinculacion');
    Route::post('/preinscripcion/grupovb/calendario/eliminar','Preinscripcion\grupovoboController@deleteCalendar')->middleware('can:agenda.vinculacion');
    Route::post('/preinscripcion/grupovb/grupovb/apertura', 'Preinscripcion\grupovoboController@generarApertura')->name('preinscripcion.grupovobo.gape')->middleware('can:preinscripcion.grupovobo');
    /*VINCULACION->PREINSCRIPCION=> BUSCAR GRUPO RPN*/
    Route::get('/preinscripcion/showvb', 'Preinscripcion\buscarController@showvb')->name('preinscripcion.showvb');
    Route::post('/preinscripcion/showvb', 'Preinscripcion\buscarController@showvb')->name('preinscripcion.showvb');

});