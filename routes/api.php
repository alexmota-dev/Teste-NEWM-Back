<?php

use App\Http\Controllers\ClientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('v1/client', [ClientController::class, 'index']);
Route::post('v1/client', [ClientController::class, 'store']);
Route::get('v1/client/{id}', [ClientController::class, 'show']);
Route::delete('v1/client/{id}', [ClientController::class, 'destroy']);
Route::put('v1/client/{id}', [ClientController::class, 'update']);

