<?php

use App\Http\Controllers\TestController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

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
Route::group(['middleware' => 'web'], function($router) {
    // Route::get('/test', function () {
    //     $all = User::all();
    //     return $all;
    // });
    Route::resource('/test',TestController::class);
});


