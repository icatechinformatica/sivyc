<?php
use Illuminate\Support\Facades\Route;
Route::get('/formatot/consulta', 'FormatoT\Consultaftcontroller@index')->name('formatot.consulta.index');
Route::post('/formatot/consulta', 'FormatoT\Consultaftcontroller@index');

Route::get('/formatot/consulta/xls', 'FormatoT\Consultaftcontroller@xls')->name('formatot.consulta.xls');
Route::post('/formatot/consulta/xls', 'FormatoT\Consultaftcontroller@xls');
