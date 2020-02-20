<?php
//Rutas Orlando

use App\Http\Controllers\InstructorController;
use Illuminate\Support\Facades\Route;
//Crea instructor
Route::get('/instructor/crear', 'InstructorController@crear_instructor');
Route::post('/instructor/guardar', 'InstructorController@guardar_instructor');

//Crea pago
Route::get('/pago/crear', 'PagoController@crear_pago');
Route::get('/pago/guardar', 'PagoController@guardar_pago');
Route::get('/pago/modificar', 'PagoController@modificar_pago');
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

Route::get('/', function () {
    return view('pages/delegacionadmin');

});

Route::get('/add-cursos', function () {
    return view('layouts.pages.frmcursos');
});
Route::get('/add-contrato', function () {
    return view('layouts.pages.frmcontrato');
});
Route::get('/cursos', function () {
    return view('layouts.pages.table');
});
Route::get('/add-convenio', function () {
    return view('pages/frmconvenio');
});



Route::get('/usuarios', function(){
    return view('layouts.pages.frmcursos');
})->name('usuarios');
