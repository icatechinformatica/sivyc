<?php
use Illuminate\Support\Facades\Route;
Route::middleware(['auth'])->group(function(){                
    Route::post('/solicitud/apertura/guardar_preapertura', 'Solicitud\aperturaController@guardar_preapertura')->name('solicitud.apertura.guardar_preapertura')->middleware('can:solicitud.apertura');
});