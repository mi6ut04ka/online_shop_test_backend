<?php

use App\Http\Controllers\api\admin\CategoryController;
use App\Http\Controllers\api\admin\ProductController;
use App\Http\Controllers\api\admin\SaleController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/categories', \App\Http\Controllers\api\CategoryController::class.'@index');
Route::get('/categories/{id}/products', \App\Http\Controllers\api\CategoryController::class.'@getProducts');
Route::get('/categories/{id}', \App\Http\Controllers\api\CategoryController::class.'@show');

Route::get('/products/{id}', \App\Http\Controllers\api\ProductController::class. '@show');
Route::get('/products', \App\Http\Controllers\api\ProductController::class.'@index');

Route::prefix('/admin')->group(function () {
    Route::prefix('/categories')->group(function () {
        Route::post('/', CategoryController::class.'@store');
        Route::put('/{id}', CategoryController::class.'@update');
        Route::delete('/{id}', CategoryController::class.'@destroy');
        Route::patch('/{id}/update-position', CategoryController::class.'@updatePosition');
    });

    Route::prefix('/products')->group(function () {
        Route::post('/', ProductController::class.'@store');
        Route::put('/{id}', ProductController::class.'@update');
        Route::patch('/{id}/update-position', ProductController::class.'@updatePosition');
        Route::delete('/{id}', ProductController::class.'@destroy');
    });
    Route::prefix('/sales')->group(function () {
        Route::get('/', SaleController::class.'@index');
        Route::post('/', SaleController::class.'@store');
        Route::delete('/{id}', SaleController::class.'@destroy');
        Route::get('/reports', SaleController::class.'@reports');
    });
});

