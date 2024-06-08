<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
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

Route::middleware('auth:sanctum')->get(
    '/user',
    function (Request $request) {
        return $request->user();
    }
);

Route::group(
    [
        'prefix' => 'orders'
    ],
    function () {
        return
            [
                Route::get('', [OrderController::class, 'index']),
                Route::get('/{order:id}', [OrderController::class, 'show']),
                Route::post('', [OrderController::class, 'store']),
                Route::put('/{order:id}', [OrderController::class, 'update']),
                Route::delete('/{order:id}', [OrderController::class, 'destroy']),
            ];
    }
);

Route::group(
    [
        'prefix' => 'products'
    ],
    function () {
        return
            [
                Route::get('', [ProductController::class, 'index']),
                Route::get('/{product:id}', [ProductController::class, 'show']),
                Route::post('', [ProductController::class, 'store']),
                Route::put('/{product:id}', [ProductController::class, 'update']),
                Route::delete('/{product:id}', [ProductController::class, 'destroy']),
            ];
    }
);

