<?php

use App\Http\Controllers\Solicitud\RequisicionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/solicitud/requisicion/index', [RequisicionController::class, 'index'])->name('solicitud.requisicion.index');
    Route::get('/solicitud/busqueda/requisicion', [RequisicionController::class, 'searchRquest'])->name('solicitud.busqueda.requisicion');
    Route::get('/solicitud/busqueda/catalogo', [RequisicionController::class, 'searchCatalogoVienes'])->name('solcitud.busqueda.cat');
    Route::post('/solicitud/requisicion/store', [RequisicionController::class, 'store'])->name('solicitud.requisicion.store');
    Route::get('/solicitud/requisicion/form/create/{id}', [RequisicionController::class, 'create'])->name('solicitud.requisicion.form.create');
    Route::post('/solicitud/requisicion/add/justificacion', [RequisicionController::class, 'justificacion'])->name('solicitud.requisicion.add.justificacion');
    Route::delete('/solicitud/requisicion/delete/{id}', [RequisicionController::class, 'destroy'])->name('solicitud.requisicion.delete');
    Route::put('/solicitud/requisicion/update/{id}', [RequisicionController::class, 'update'])->name('solicitud.requisicion.update');
    Route::post('/solicitud/requisicion/upload/file', [RequisicionController::class, 'uploadDocumentReq'])->name('solicitud.requisicion.uploadFile');


    // reportes pdf requisiciones
    Route::get('/solicitud/reportes/requisicion/{id}', [RequisicionController::class, 'generarReporte'])->name('reporte.solicitud.requisicion');

});
