<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController as ApiAuthController;
use App\Http\Controllers\API\MenuController as ApiMenuController;
use App\Http\Controllers\API\IngredientController as ApiIngredientController;
use App\Http\Controllers\API\OrderController as ApiOrderController;

// Customer mobile API controllers
use App\Http\Controllers\Api\Customer\CustomerAuthController;
use App\Http\Controllers\Api\Customer\CustomerMenuController;
use App\Http\Controllers\Api\Customer\CustomerOrderController;
use App\Http\Controllers\Api\Customer\PaymentSettingController;

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

// Public auth routes (legacy API)
Route::post('register', [ApiAuthController::class, 'register']);
Route::post('login', [ApiAuthController::class, 'login']);

// -------------------------
// Customer mobile API (new)
// -------------------------
Route::prefix('customer')->group(function () {
    Route::post('register', [CustomerAuthController::class, 'register']);
    Route::post('login', [CustomerAuthController::class, 'login']);

    // Public QR customer endpoints. Flutter customers do not need Sanctum login.
    Route::get('menus', [CustomerMenuController::class, 'index']);
    Route::get('payment-setting', [PaymentSettingController::class, 'show']);
    Route::get('qris', [CustomerOrderController::class, 'qris']);
    Route::post('orders', [CustomerOrderController::class, 'store']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('orders', [CustomerOrderController::class, 'index']);
        Route::get('orders/{order}', [CustomerOrderController::class, 'show']);
        Route::post('logout', [CustomerAuthController::class, 'logout']);
    });
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('logout', [ApiAuthController::class, 'logout']);

    // Menu CRUD
    Route::apiResource('menus', ApiMenuController::class);

    // Ingredient CRUD
    Route::apiResource('ingredients', ApiIngredientController::class);

    // Orders
    Route::post('orders', [ApiOrderController::class, 'store']);
    Route::get('orders', [ApiOrderController::class, 'index']);
    Route::get('orders/{order}', [ApiOrderController::class, 'show']);
    Route::patch('orders/{order}/status', [ApiOrderController::class, 'updateStatus']);
});
