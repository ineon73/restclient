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


Route::get('/api/', 'DataAccess@index');
Route::get('/db/', 'DataAccess@db');
Route::any('/', function () {
   echo "Для получения апи использовать адрес /api/?id[]=id";
});

