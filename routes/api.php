<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\TicketsController;
use App\Http\Controllers\TypeTicketsController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "²" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('/api')->group(function (){
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::get('/{id}/orders', [UserController::class, 'getUserOrders']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

    Route::prefix('events')->group(function () {
        Route::get('/', [EventController::class, 'index']);
        Route::post('/', [EventController::class, 'store']);
        Route::get('/{id}', [EventController::class, 'show']);
        Route::put('/{id}', [EventController::class, 'update']);
        Route::delete('/{id}', [EventController::class, 'destroy']);
        Route::post('/{id}/attachCategory/{category_id}', [EventController::class, 'attachCategory']);
        Route::get('/{id}/categories', [EventController::class, 'getCategories']);
    });

    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::get('/{id}', [CategoryController::class, 'show']);
        Route::put('/{id}', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'destroy']);
        Route::post('/{id}/attachEvent/{event_id}', [CategoryController::class, 'attachEvent']);
        Route::get('/{id}/events', [CategoryController::class, 'getEvents']);
    });

    Route::prefix('typetickets')->group(function () {
        Route::get('/', [TypeTicketsController::class, 'index']);
        Route::post('/', [TypeTicketsController::class, 'store']);
        Route::get('/{id}', [TypeTicketsController::class, 'show']);
        Route::get('/{id}/tickets', [TypeTicketsController::class, 'showTypeTicketDetails']);
        Route::put('/{id}', [TypeTicketsController::class, 'update']);
        Route::delete('/{id}', [TypeTicketsController::class, 'destroy']);
    });

    Route::prefix('tickets')->group(function () {
        Route::get('/', [TicketsController::class, 'index']);
        Route::post('/', [TicketsController::class, 'store']);
        Route::get('/{id}', [TicketsController::class, 'showTicketDetails']);
        Route::get('/{id}/orders', [TicketsController::class, 'getOrders']);
        Route::put('/{id}', [TicketsController::class, 'update']);
        Route::delete('/{id}', [TicketsController::class, 'destroy']);
    });

    Route::prefix('orders')->group(function () {
        Route::get('/', [OrdersController::class, 'index']);
        Route::post('/', [OrdersController::class, 'store']);
        Route::get('/{id}', [OrdersController::class, 'show']);
        Route::put('/{id}', [OrdersController::class, 'update']);
        Route::delete('/{id}', [OrdersController::class, 'destroy']);
    });
});


