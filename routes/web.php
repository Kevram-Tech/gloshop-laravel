<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrivacyController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Admin\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/privacy', [PrivacyController::class, 'index'])->name('privacy');

// Admin Authentication
Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
    
    // Protected Admin Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        
        Route::resource('products', ProductController::class)->names('admin.products');
        Route::resource('categories', CategoryController::class)->names('admin.categories');
        Route::resource('orders', OrderController::class)->names('admin.orders')->only(['index', 'show', 'update']);
        Route::resource('users', UserController::class)->names('admin.users')->only(['index', 'show']);
        Route::get('/statistics', [StatisticsController::class, 'index'])->name('admin.statistics.index');
        Route::get('/settings', function () {
            return view('admin.settings');
        })->name('admin.settings.index');
    });
});
