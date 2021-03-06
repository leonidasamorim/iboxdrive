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


Route::get('/', 'WebServiceController@index');

Route::get('put/{search}', 'WebServiceController@put')->where('search', '.*');

//Route::get('/put/{*}', 'WebServiceController@put');
Route::post('/put', 'WebServiceController@put');
