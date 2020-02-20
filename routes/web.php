<?php

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
