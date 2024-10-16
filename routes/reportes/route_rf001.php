<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Reporte\Rf001Controller;
use App\Http\Controllers\Reporte\Rf001ReporteController;
use App\Http\Controllers\Reporte\Rf001AdministrativoController;

Route::middleware(['auth'])->group(function(){
    Route::get('/reportes/rf001/indice', [Rf001Controller::class, 'dashboard'])->name('reporte.rf001.index');
    Route::get('/reportes/rf001/ingreso-propio', [Rf001Controller::class, 'index'])->middleware('can:solicitud.rf001')->name('reporte.rf001.ingreso-propio');
    Route::post('/reportes/rf001/generar', [Rf001Controller::class, 'store'])->name('reporte.rf001.store');
    Route::get('/reportes/rf001/generados', [Rf001Controller::class, 'getSentFormat'])->middleware(['can:solicitud.rf001','can:vobo.rf001'])->name('reporte.rf001.sent');
    Route::get('/reportes/rf001/detalle/{concentrado}', [Rf001Controller::class, 'index'])->name('reporte.rf001.details');
    Route::get('/reportes/rf001/editar/{id}', [Rf001Controller::class, 'edit'])->name('reporte.rf001.edit');
    Route::put('/reportes/rf001/detalle/actualizar/{id}', [Rf001Controller::class, 'update'])->name('reporte.rf001.update');
    Route::post('/reportes/rf001/store', [Rf001Controller::class, 'storeData'])->name('reporte.rf001.jsonStore');
    Route::get('/reportes/rf001/formato/pdf/{id}', [Rf001Controller::class, 'getPdfReport'])->name('reporte.rf001.getpdf');
    Route::get('/reportes/rf001/cancelado/pdf/{id}', [Rf001Controller::class, 'getReporteCancelado'])->name('reporte.rf001.pdf.cancelado');
    Route::get('/reportes/rf001/concentrado/detalle/{id}', [Rf001Controller::class, 'show'])->name('reporte.rf001.set.details');
    Route::post('/reportes/rf001/add/comment', [Rf001Controller::class, 'addComment'])->name('reporte.rf001.add.comments');
    Route::get('/reportes/rf001/create/xmlFormat/{id}', [Rf001ReporteController::class, 'show'])->name('reporte.rf001.xml.format');

    Route::post('/reportes/rf001/xml/generar/{id}', [Rf001ReporteController::class, 'generate_report'])->name('reportes.rf001.xml.generar');
    Route::post('/reportes/rf001/xml/cancelado/{id}', [Rf001ReporteController::class, 'reporte_cancelado'])->name('reportes.rf001.xml.cancelado');

    // cambios de estado
    Route::get('/reportes/rf001/cambio-estado/{id}', [Rf001Controller::class, 'cambioEstado'])->name('reporte.rf001.cambio.estado');
    Route::get('/reportes/rf001/enviar-sellar/{id}', [Rf001Controller::class, 'cambioSello'])->name('reporte.rf001.cambio.sello');


    Route::get('/reportes/fr001/firma/{id}/{solicitud}', [Rf001ReporteController::class, 'edit'])->name('reporte.generar.firma');

    Route::get('/repore/rf001/firmante', [Rf001ReporteController::class, 'efirma'])->name('firma.electronica');

    Route::post('/reporte/rf001/getToken', [Rf001ReporteController::class, 'getTokenFirma'])->name('firma.gettoken');

    Route::post('/reporte/rf001/firma/update', [Rf001ReporteController::class, 'store'])->name('firma.store.update');

    Route::get('/reporte/rf001/formarf001/{id}', [Rf001ReporteController::class, 'getForma'])->name('reporte.forma.rf001');

    Route::get('/reporte/rf001/administrativo/index', [Rf001AdministrativoController::class, 'index'])->middleware('can:validacion.rf001')->name('administrativo.index');
    Route::get('/reportes/rf001/administrativo/detalle/{id}', [Rf001AdministrativoController::class, 'show'])->name('administrativo.rf001.details');
    Route::post('/reportes/rf001/administrativo/retornar', [Rf001AdministrativoController::class, 'sendBack'])->name('administrativo.rf001.retornar');
    Route::post('/reportes/rf001/administrativo/firmar', [Rf001AdministrativoController::class, 'firmar'])->name('administrativo.rf001.firmar');
    Route::post('/reportes/rf001/administrativo/sellar', [Rf001AdministrativoController::class, 'sellado'])->name('administrativo.rf001.sellado');
    Route::post('/reportes/rf001/administrativo/aprobar', [Rf001AdministrativoController::class, 'aprobar'])->name('administrativo.rf001.aprobar');

});
