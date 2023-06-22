<?php

use App\Http\Controllers\ClientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('v1/funcionario', [ClientController::class, 'index']);
Route::post('v1/funcionario', [ClientController::class, 'store']);
Route::get('v1/funcionario/{id}', [ClientController::class, 'show']);
Route::get('v1/searchforclientsbyemail/{email}', [ClientController::class, 'SearchFor10UsersByEmail']);
Route::get('v1/searchforclientsbyname/{name}', [ClientController::class, 'SearchFor10UsersByName']);
Route::delete('v1/funcionario/{id}', [ClientController::class, 'destroy']);
Route::put('v1/funcionario/{id}', [ClientController::class, 'update']);

