<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolesController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::prefix('/api')->group(function (){
    Route::prefix('roles')->group(function () {
        Route::get('/', [RolesController::class, 'getRoles']);
        // Route::post('/', [RolesController::class, 'createRoles']);
        Route::get('/{id}', [RolesController::class, 'getRolesById']);
        Route::patch('/{id}', [RolesController::class, 'updateRoles']);
        Route::delete('/{id}', [RolesController::class, 'destroyRoles']);
    });

    Route::prefix('users')->group(function () {
        Route::post('/register', [UserController::class, 'registerUsers']);
        Route::post('/login', [UserController::class, 'loginUsers']);
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/profile', [UserController::class, 'getProfile']);
            Route::delete('/{id}', [UserController::class, 'destroyUsers']);
        });
    });

    Route::prefix('events')->group(function () {
        Route::get('/', [EventController::class, 'getEvents']);
        Route::post('/', [EventController::class, 'createEvents']);
        Route::get('/{id}', [EventController::class, 'getEventsById']);
        Route::patch('/{id}', [EventController::class, 'updateEvents']);
        Route::delete('/{id}', [EventController::class, 'destroyEvents']);
        Route::get('/{id}/events', [UserController::class, 'getEventsByUser'])->middleware('auth:sanctum');
        Route::post('/{id}/categories', [EventController::class, 'attachCategory']);
        Route::get('/{id}/categories', [EventController::class, 'getCategories']);
    });

    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'getCategories']);
        Route::post('/', [CategoryController::class, 'createCatergories']);
        Route::get('/{id}', [CategoryController::class, 'getCategoriesById']);
        Route::patch('/{id}', [CategoryController::class, 'updateCategories']);
        Route::delete('/{id}', [CategoryController::class, 'destroyCategories']);
        Route::post('/{id}/events', [CategoryController::class, 'attachEvent']);
        Route::get('/{id}/events', [CategoryController::class, 'getEvents']);
    });

    Route::prefix('type-tickets')->group(function () {
        Route::get('/', [TypeTicketsController::class, 'getTypeTickets']);
        Route::post('/', [TypeTicketsController::class, 'createTypeTickets']);
        Route::get('/{id}', [TypeTicketsController::class, 'getTypeTicketsById']);
        // Route::get('/{id}/tickets', [TypeTicketsController::class, 'showTypeTicketDetails']);
        Route::patch('/{id}', [TypeTicketsController::class, 'updateTypeTickets']);
        Route::delete('/{id}', [TypeTicketsController::class, 'destroyTypeTickets']);
    });

    Route::prefix('tickets')->group(function () {
        Route::get('/', [TicketsController::class, 'getTickets']);
        Route::post('/', [TicketsController::class, 'createTickets']);
        Route::get('/{id}', [TicketsController::class, 'getTicketsById']);
        Route::get('/{id}/tickets', [UserController::class, 'getTicketsByUser'])->middleware('auth:sanctum');
        // Route::get('/{id}/orders', [TicketsController::class, 'getOrders']);
        Route::patch('/{id}', [TicketsController::class, 'updateTickets']);
        Route::delete('/{id}', [TicketsController::class, 'destroyTickets']);
    });

    Route::prefix('orders')->group(function () {
        Route::get('/', [OrdersController::class, 'getOrders']);
        Route::post('/', [OrdersController::class, 'createOrders']);
        Route::get('/{id}', [OrdersController::class, 'getOrdersById']);
        Route::patch('/{id}', [OrdersController::class, 'updateOrders']);
        Route::delete('/{id}', [OrdersController::class, 'destroyOrders']);
    });
});


