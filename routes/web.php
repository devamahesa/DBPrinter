<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatabaseController;
use App\Services\DatabaseServices;


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
    return view('welcome');
});

Route::get('/dblogin', [DatabaseController::class, 'formlogin']); //form untuk input data login database driver dan username pass
Route::post('/authdb', [DatabaseController::class, 'authenticate']); //route authentikasi database
Route::get('/listdb', [DatabaseController::class, 'chooseDB']); //form untuk input database yg perlu diprint dan ekstensi yang perlu diprint
Route::post('/tablelist', [DatabaseController::class, 'showTable']);
Route::get('/printedtable', [DatabaseController::class, 'chooseTable']);
Route::post('/printdb', [DatabaseController::class, 'get_tables_columns']); //route ke controller printing