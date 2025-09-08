<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentoElectronico\DocumentoController;

// Grupo de rutas para documentos
Route::prefix('documentos')->group(function () {
    Route::get('/inicio', [DocumentoController::class, 'index'])->name('documentos.index');
});
