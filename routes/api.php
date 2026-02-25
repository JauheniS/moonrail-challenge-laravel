<?php

use App\Http\Controllers\PlayerController;
use App\Http\Controllers\TeamController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('player')->group(function () {
    Route::get('/', [PlayerController::class, 'index']);
    Route::get('/{player}', [PlayerController::class, 'show']);
    Route::post('/', [PlayerController::class, 'store']);
    Route::put('/{player}', [PlayerController::class, 'update']);
    Route::delete('/{player}', [PlayerController::class, 'destroy'])->middleware('bearer_token');
});

Route::post('team/process', [TeamController::class, 'process']);

