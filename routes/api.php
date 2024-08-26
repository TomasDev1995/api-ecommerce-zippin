<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Invoice\InvoiceController;
use App\Http\Controllers\Api\V1\Order\OrderController;
use App\Http\Controllers\Api\V1\Product\ProductController;
use App\Http\Controllers\Api\V1\User\Admin\AdminUserController;
use App\Http\Controllers\Api\V1\User\Customer\CustomerController;
use Illuminate\Support\Facades\Route;

/**
 * @OA\Info(
 *     title="API Documentation",
 *     version="1.0.0",
 *     description="API documentation for your application",
 *     @OA\Contact(
 *         email="support@example.com"
 *     )
 * )
 */

// Prefix para la versión de la API
Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'customerRegister'])->name('customer.register');
    Route::post('/login', [AuthController::class, 'customerLogin'])->name('customer.login');
    Route::middleware(['auth:sanctum', 'role:customer'])->group(function () {
        Route::post('/logout', [AuthController::class, 'customerLogout'])->name('customer.logout');

        // Gestión de Usuarios (solo para el cliente)
        Route::get('/users/me', [CustomerController::class, 'show'])->name('users.show');
        // Gestión de Productos (solo para el cliente)
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
        // Gestión de Órdenes (solo para el cliente)
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show'); 
        Route::post('/orders', [OrderController::class, 'create'])->name('orders.store');
        Route::put('orders/{id}', [OrderController::class, 'update'])->name('orders.update');
    });
    Route::prefix('admin')->group(function () {
        Route::post('/login', [AuthController::class, 'adminLogin'])->name('admin.login');
        Route::post('/register', [AuthController::class, 'adminRegister'])->name('admin.register');
        // Middleware para autenticación de admin
        Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
            Route::post('/logout', [AuthController::class, 'adminLogout'])->name('admin.logout');

            // Gestión de Usuarios
            Route::apiResource('users', AdminUserController::class);
            // Gestión de Productos
            Route::apiResource('products', ProductController::class);
            // Gestión de Órdenes
            Route::apiResource('orders', OrderController::class);
            // Rutas para cambiar el estado de las órdenes
            Route::post('orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
            // Gestión de Facturas
            Route::apiResource('invoices', InvoiceController::class);
        });
    });
});