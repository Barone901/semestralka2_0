<?php

use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\ProductApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {

    Route::prefix('products')->controller(ProductApiController::class)->group(function () {
        Route::get('/search', 'search');           // GET /api/products/search
        Route::get('/{product:slug}', 'show');     // GET /api/products/{slug}
    });

    Route::prefix('cart')->controller(CartController::class)->group(function () {
        Route::get('/', 'index');                  // GET /api/cart
        Route::post('/add', 'add');                // POST /api/cart/add
        Route::post('/update', 'update');          // POST /api/cart/update
        Route::post('/remove', 'remove');          // POST /api/cart/remove
        Route::post('/clear', 'clear');            // POST /api/cart/clear
    });

});
