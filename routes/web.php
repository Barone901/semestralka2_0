<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;

use Illuminate\Support\Facades\Route;

// SHOP routes (verejná časť)
require __DIR__.'/shop.php';

// AUTH routes (Breeze)
require __DIR__.'/auth.php';

// App routes (po prihlásení)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/kategoria/{category:slug}', [CategoryController::class, 'show'])
    ->name('category.show');

Route::get('/produkt/{product}', [ProductController::class, 'show'])
    ->name('product.show');
