<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MfaController;

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

Route::post('/token', [AuthController::class, 'token']);

Route::middleware('auth:sanctum')->name('mfa.')->group(function () {
    Route::get('/mfa', [MfaController::class, 'index'])->name('index');
    Route::post('/mfa', [MfaController::class, 'store'])->name('store');
});