<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EstablecimientoController;


Route::prefix('auth')->group(function () {

    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');;
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('logout', [AuthController::class, 'logout']);
});



Route::middleware('auth:api')->group(function () {

    Route::get('/user', function () {
        return auth()->user();
    });

    Route::apiResource('establecimientos', App\Http\Controllers\Api\EstablecimientoController::class);
});

Route::apiResource('categorias', App\Http\Controllers\Api\CategoriaController::class);


Route::get('establecimientos-public', [EstablecimientoController::class, 'establecimientosPublic']);
Route::get('establecimientos-public/{id}', [EstablecimientoController::class, 'establecimientoIdPublic']);
