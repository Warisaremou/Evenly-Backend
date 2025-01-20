<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TicketsController;
use App\Http\Controllers\TypeTicketsController;
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

Route::prefix('events')->group(function () {
    Route::get('/', [EventController::class, 'index']);
    Route::post('/', [EventController::class, 'store']);
    Route::get('/{id}', [EventController::class, 'show']);
    Route::get('/{id}/categories', [EventController::class, 'showCategories']);
    Route::put('/{id}', [EventController::class, 'update']);
    Route::delete('/{id}', [EventController::class, 'destroy']);
});

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::get('/{id}', [CategoryController::class, 'show']);
    Route::put('/{id}', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
});

Route::prefix('type-tickets')->group(function () {
    Route::get('/', [TypeTicketsController::class, 'index']);
    Route::post('/', [TypeTicketsController::class, 'store']);
    Route::get('/{id}', [TypeTicketsController::class, 'show']);
    Route::put('/{id}', [TypeTicketsController::class, 'update']);
    Route::delete('/{id}', [TypeTicketsController::class, 'destroy']);
});

Route::prefix('tickets')->group(function () {
    Route::get('/', [TicketsController::class, 'index']);
    Route::post('/', [TicketsController::class, 'store']);
    Route::get('/{id}', [TicketsController::class, 'show']);
    Route::put('/{id}', [TicketsController::class, 'update']);
    Route::delete('/{id}', [TicketsController::class, 'destroy']);
});




