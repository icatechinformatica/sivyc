<?php
//Rutas Orlando

use App\Http\Controllers\InstructorController;
use Illuminate\Support\Facades\Route;
//Crea instructor
Route::get('/instructor/crear', 'InstructorController@crear_instructor')->name('instructor-crear');
Route::post('/instructor/guardar', 'InstructorController@guardar_instructor')->name('instructor-guardar');

//Crea pago
Route::get('/pago/crear', 'PagoController@crear_pago')->name('pago-crear');
Route::get('/pago/guardar', 'PagoController@guardar_pago')->name('pago-guardar');
Route::get('/pago/modificar', 'PagoController@modificar_pago')->name('pago-modificar');
Route::post('/pago/fill', 'PagoController@index');
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/supre/paso1', function () {
    return view('layouts.pages.delegacionadmin');

})->name('supre-fase1');

Route::get('/curso/crear', function () {
    return view('layouts.pages.frmcursos');
})->name('frm-cursos');
Route::get('/add-contrato', function () {
    return view('layouts.pages.frmcontrato');
})->name('contrato');
Route::get('/', function () {
    return view('layouts.pages.table');
});
Route::get('/add-convenio', function () {
    return view('layouts.pages.frmconvenio');
})->name('frm-convenio');
Route::get('/usuarios', function(){
    return view('layouts.pages.frmcursos');
})->name('usuarios');
