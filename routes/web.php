<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/sale', [ProductController::class, 'sale'])->name('products.sale');
    Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
});

require __DIR__.'/auth.php';
