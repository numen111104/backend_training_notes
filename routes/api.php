<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//login
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

//register
Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);

//logout
Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout'])->middleware('auth:sanctum');

//notes
Route::apiResource('/notes', \App\Http\Controllers\Api\NoteController::class)->middleware('auth:sanctum');
