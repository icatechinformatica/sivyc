<?php
use Illuminate\Support\Facades\Route;
Route::middleware(['auth'])->group(function(){            
    Route::post('/solicitud/apertura/vobo', 'Solicitud\aperturaController@vobo')->name('solicitud.apertura.vobo')->middleware('can:solicitud.apertura');
    Route::get('/solicitud/apertura/vobo', 'Solicitud\aperturaController@vobo')->name('solicitud.apertura.vobo')->middleware('can:solicitud.apertura');  
});