<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EstablecimientoController;
use App\Http\Controllers\Api\ServicioController;


Route::prefix('auth')->group(function () {

    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');;
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('change-password', [AuthController::class, 'changePassword'])->middleware('auth:api');
});



Route::middleware('auth:api')->group(function () {

    Route::get('/user', function () {
        return auth()->user();
    });

    Route::apiResource('establecimientos', App\Http\Controllers\Api\EstablecimientoController::class);
    Route::post('servicios', [ServicioController::class, 'store']);
    Route::get('servicios/establecimiento/{id}', [ServicioController::class, 'getByEstablecimiento']);
    Route::put('servicios/{id}', [ServicioController::class, 'update']);
    Route::delete('servicios/{id}', [ServicioController::class, 'destroy']);
});

Route::apiResource('categorias', App\Http\Controllers\Api\CategoriaController::class);


Route::get('establecimientos-public', [EstablecimientoController::class, 'establecimientosPublic']);
Route::get('establecimientos-public/{id}', [EstablecimientoController::class, 'establecimientoIdPublic']);
