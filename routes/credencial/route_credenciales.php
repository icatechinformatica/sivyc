<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Credencial\CredencialController;

Route::middleware(['auth'])->group(function(){
    Route::get('/credencial/indice', [CredencialController::class, 'index'])->name('credencial.index');

});
