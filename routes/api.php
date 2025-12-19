<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\FavoritesController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\AdminController;
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

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{slug}', [CategoryController::class, 'show']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::put('/password', [AuthController::class, 'changePassword']);
    });

    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/', [CartController::class, 'store']);
        Route::put('/{cart}', [CartController::class, 'update']);
        Route::delete('/{cart}', [CartController::class, 'destroy']);
    });

    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store']);
        Route::get('/{order}', [OrderController::class, 'show']);
    });

    Route::prefix('favorites')->group(function () {
        Route::get('/', [FavoritesController::class, 'index']);
        Route::post('/', [FavoritesController::class, 'store']);
        Route::delete('/{id}', [FavoritesController::class, 'destroy']);
        Route::post('/check', [FavoritesController::class, 'check']);
    });

    Route::prefix('addresses')->group(function () {
        Route::get('/', [AddressController::class, 'index']);
        Route::post('/', [AddressController::class, 'store']);
        Route::put('/{address}', [AddressController::class, 'update']);
        Route::delete('/{address}', [AddressController::class, 'destroy']);
        Route::post('/{address}/set-default', [AddressController::class, 'setDefault']);
    });

    Route::prefix('payment-methods')->group(function () {
        Route::get('/', [PaymentMethodController::class, 'index']);
        Route::post('/', [PaymentMethodController::class, 'store']);
        Route::put('/{paymentMethod}', [PaymentMethodController::class, 'update']);
        Route::delete('/{paymentMethod}', [PaymentMethodController::class, 'destroy']);
        Route::post('/{paymentMethod}/set-default', [PaymentMethodController::class, 'setDefault']);
    });
});

// Admin routes
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AdminController::class, 'logout']);
        Route::get('/dashboard/stats', [AdminController::class, 'getDashboardStats']);

        Route::prefix('orders')->group(function () {
            Route::get('/', [AdminController::class, 'getOrders']);
            Route::get('/{id}', [AdminController::class, 'getOrder']);
            Route::put('/{id}/status', [AdminController::class, 'updateOrderStatus']);
        });

        Route::prefix('products')->group(function () {
            Route::get('/', [AdminController::class, 'getProducts']);
            Route::post('/', [AdminController::class, 'createProduct']);
            Route::get('/{id}', [AdminController::class, 'getProduct']);
            Route::put('/{id}', [AdminController::class, 'updateProduct']);
            Route::delete('/{id}', [AdminController::class, 'deleteProduct']);
        });

        Route::prefix('categories')->group(function () {
            Route::get('/', [AdminController::class, 'getCategories']);
            Route::post('/', [AdminController::class, 'createCategory']);
            Route::put('/{id}', [AdminController::class, 'updateCategory']);
            Route::delete('/{id}', [AdminController::class, 'deleteCategory']);
        });

        Route::prefix('users')->group(function () {
            Route::get('/', [AdminController::class, 'getUsers']);
            Route::get('/{id}', [AdminController::class, 'getUser']);
        });

        Route::prefix('statistics')->group(function () {
            Route::get('/sales-by-period', [AdminController::class, 'getSalesByPeriod']);
            Route::get('/top-selling-products', [AdminController::class, 'getTopSellingProducts']);
            Route::get('/stock', [AdminController::class, 'getStockStatistics']);
        });
    });
});
