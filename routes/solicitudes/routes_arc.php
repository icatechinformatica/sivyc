<?php
use Illuminate\Support\Facades\Route;
Route::middleware(['auth'])->group(function(){            
    Route::post('/solicitudes/aperturas/soporte_pago', 'Solicitudes\aperturasController@soporte_pago')->name('solicitudes.aperturas.soporte_pago');
  
});