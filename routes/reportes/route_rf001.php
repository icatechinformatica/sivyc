<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Reporte\Rf001Controller;
use App\Http\Controllers\Reporte\Rf001ReporteController;

Route::middleware(['auth'])->group(function(){
    Route::get('/reportes/rf001/indice', [Rf001Controller::class, 'dashboard'])->name('reporte.rf001.index');
    Route::get('/reportes/rf001/ingreso-propio', [Rf001Controller::class, 'index'])->name('reporte.rf001.ingreso-propio');
    Route::post('/reportes/rf001/generar', [Rf001Controller::class, 'store'])->name('reporte.rf001.store');
    Route::get('/reportes/rf001/generados', [Rf001Controller::class, 'getSentFormat'])->name('reporte.rf001.sent');
    Route::get('/reportes/rf001/detalle/{concentrado}', [Rf001Controller::class, 'index'])->name('reporte.rf001.details');
    Route::put('/reportes/rf001/detalle/actualizar/{id}', [Rf001Controller::class, 'update'])->name('reporte.rf001.update');
    Route::post('/reportes/rf001/store', [Rf001Controller::class, 'storeData'])->name('reporte.rf001.jsonStore');
    Route::get('/reportes/rf001/formato/pdf', [Rf001Controller::class, 'getPdfReport'])->name('reporte.rf001.getpdf');
    Route::get('/reportes/rf001/concentrado/detalle/{id}/{solicitud}', [Rf001Controller::class, 'show'])->name('reporte.rf001.set.details');
    Route::post('/reportes/rf001/add/comment', [Rf001Controller::class, 'addComment'])->name('reporte.rf001.add.comments');
    Route::get('/reportes/rf001/create/xmlFormat/{id}', [Rf001ReporteController::class, 'show'])->name('reporte.rf001.xml.format');

    Route::post('/reportes/rf001/xml/generar/{id}', [Rf001ReporteController::class, 'generate_report'])->name('reportes.rf001.xml.generar');

    Route::get('/reportes/fr001/firma/{id}/{solicitud}', [Rf001ReporteController::class, 'edit'])->name('reporte.generar.firma');



});
