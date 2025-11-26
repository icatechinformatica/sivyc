<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\webController\AlumnoController;

Route::middleware(['auth'])->group(function(){
    Route::get('/aspirantes/check-email', [AlumnoController::class, 'checkEmail'])->name('aspirantes.checkEmail');
});
