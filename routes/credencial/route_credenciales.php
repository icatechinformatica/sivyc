<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Credencial\CredencialController;

Route::middleware(['auth'])->group(function(){
    Route::get('/credencial/inicio', [CredencialController::class, 'index'])->name('credencial.indice');
    Route::get('/credencial/detalle/{id}', [CredencialController::class, 'show'])->name('credencial.ver');
    Route::get('/credencial/descargar/{id}', [CredencialController::class, 'download'])->name('descargar.codigo');
    Route::post('/credencial/perfil/foto', [CredencialController::class, 'uploadPhoto'])->name('credencial.uploadphoto');

});

Route::get('/credencial/perfil/{id}', [CredencialController::class, 'edit'])->name('perfil');
