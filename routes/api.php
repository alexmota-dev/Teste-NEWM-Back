<?php

use App\Http\Controllers\EmployeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('v1/funcionario', [EmployeeController::class, 'index']);
Route::post('v1/funcionario', [EmployeeController::class, 'store']);
Route::get('v1/funcionario/{id}', [EmployeeController::class, 'show']);
Route::delete('v1/funcionario/{id}', [EmployeeController::class, 'destroy']);
Route::put('v1/funcionario/{id}', [EmployeeController::class, 'update']);

