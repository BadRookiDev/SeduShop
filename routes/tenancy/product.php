<?php

use App\Http\Controllers\Product\ProductSearchController;
use App\Facades\AdvertisingProductHub;
use App\Http\Controllers\Product\TenantProductController;
use Illuminate\Support\Facades\Route;

Route::get('/producten/quick-search', [ProductSearchController::class, 'quickSearchProduct']);

Route::get('/producten/{productId}', [TenantProductController::class, 'show'])->name('product.show');
Route::get('/producten/{productId}/details', [TenantProductController::class, 'details'])->name('product.show.details');
