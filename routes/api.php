<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Invoice\InvoiceController;
use App\Http\Controllers\Api\V1\Order\OrderController;
use App\Http\Controllers\Api\V1\Product\ProductController;
use App\Http\Controllers\Api\V1\User\Admin\AdminUserController;
use App\Http\Controllers\Api\V1\User\Customer\CustomerUserController;
use Illuminate\Support\Facades\Route;

// Prefix para la versión de la API
Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'customerRegister'])->name('customer.register');
    Route::post('/login', [AuthController::class, 'customerLogin'])->name('customer.login');
    Route::middleware(['auth:sanctum', 'role:customer'])->group(function () {
        // Gestión de Usuarios (solo para clientes)
        Route::get('/users/me', [CustomerUserController::class, 'show'])->name('users.show');
        Route::put('/users/me', [CustomerUserController::class, 'update'])->name('users.update'); 
        // Gestión de Productos
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

        // Gestión de Órdenes
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show'); 
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update'); 
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    });
    Route::prefix('admin')->group(function () {
        Route::post('/login', [AuthController::class, 'adminLogin'])->name('admin.login');
        Route::post('/register', [AuthController::class, 'adminRegister'])->name('admin.register');
        // Middleware para autenticación de admin
        Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
            // Gestión de Usuarios
            Route::apiResource('users', AdminUserController::class);
            
            // Rutas para gestionar roles y permisos
            Route::get('users/{user}/roles', [AdminUserController::class, 'roles'])->name('users.roles');
            Route::post('users/{user}/roles', [AdminUserController::class, 'assignRole'])->name('users.assignRole');
            Route::delete('users/{user}/roles/{role}', [AdminUserController::class, 'removeRole'])->name('users.removeRole');
            
            // Gestión de Productos
            Route::apiResource('products', ProductController::class);
            
            // Rutas para gestionar categorías y atributos
            Route::get('products/categories', [ProductController::class, 'categories'])->name('products.categories');
            Route::post('products/categories', [ProductController::class, 'addCategory'])->name('products.addCategory');
            Route::delete('products/categories/{category}', [ProductController::class, 'removeCategory'])->name('products.removeCategory');
            Route::get('products/attributes', [ProductController::class, 'attributes'])->name('products.attributes');
            Route::post('products/attributes', [ProductController::class, 'addAttribute'])->name('products.addAttribute');
            Route::delete('products/attributes/{attribute}', [ProductController::class, 'removeAttribute'])->name('products.removeAttribute');
        
            // Gestión de Órdenes
            Route::apiResource('orders', OrderController::class);
            
            // Rutas para cambiar el estado de las órdenes
            Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
            
            // Rutas para generar reportes
            Route::get('reports/sales', [OrderController::class, 'salesReport'])->name('reports.sales');
            Route::get('reports/orders', [OrderController::class, 'ordersReport'])->name('reports.orders');
        
            // Gestión de Facturas
            Route::apiResource('invoices', InvoiceController::class);
            
            // Rutas para generar y emitir facturas
            Route::post('invoices/{invoice}/issue', [InvoiceController::class, 'issue'])->name('invoices.issue');
        });
    });
});