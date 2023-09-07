<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\BaseController;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\api\SendMail;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'api'], function($router) {
    Route::post('/login', [BaseController::class, 'login']);
    Route::post('/register', [BaseController::class, 'register']);
    Route::post('/editPro', [BaseController::class, 'editPro']);
     Route::get('/profile', [BaseController::class, 'profile']);
     Route::post('/logout', [BaseController::class, 'logout']);
     Route::get('/ratep/{id}', [BaseController::class, 'ratep']);
     Route::get('/ratem/{id}', [BaseController::class, 'ratem']);
     Route::get('/getrate/{id}', [BaseController::class, 'getrate']);
    });


Route::resource('/products', ProductController::class);
Route::get('/section/{name}',[ProductController::class,'filter']);    //  filter products
Route::get('/Detail/{id}',[ProductController::class,'Detail']);       //  return details of products
Route::get('/Sell/{id}',[ProductController::class,'Sell']);
Route::get('/destroy/{id}',[ProductController::class,'destroy']);
Route::post('/update',[ProductController::class,'updatee']);


Route::get('/reset/{email}',[SendMail::class,'reset']);
// Route::get('/reset',function (Request $request){
// $user=User::find($request->id);
// return response()->json($user, 'successfully.');
// });




Route::any('{any}', function(){
return response()->json([
    'status'=>'error',
    'message'=>'Resource not found'],404);
})->where('any','.*');
