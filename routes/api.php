<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('v1/funcionario', [EventController::class, 'index']);
Route::post('v1/funcionario', [EventController::class, 'store']);
Route::get('v1/funcionario/{id}', [EventController::class, 'show']);
Route::delete('v1/funcionario/{id}', [EventController::class, 'destroy']);
Route::put('v1/funcionario/{id}', [EventController::class, 'update']);

