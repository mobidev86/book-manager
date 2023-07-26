<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\BookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



Route::post('register', [AuthController::class,'register']);
Route::post('login', [AuthController::class,'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {
	Route::get('books', [BookController::class,'index']);
	Route::post('books', [BookController::class,'store']);
	Route::put('books/{id}', [BookController::class,'update']);
	Route::delete('books/{id}', [BookController::class,'destroy']);
	Route::post('checkout', [CheckoutController::class,'checkoutBook']);
	Route::put('checkout/{id}', [CheckoutController::class,'returnBook']);
});



