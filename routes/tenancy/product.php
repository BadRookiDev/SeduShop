<?php

use App\Http\Controllers\Product\ProductSearchController;
use App\Facades\AdvertisingProductHub;
use Illuminate\Support\Facades\Route;

Route::get('/producten/quick-search', [ProductSearchController::class, 'quickSearchProduct']);

Route::get('/producten/{productId}', function ($productId) {
    $product = AdvertisingProductHub::getProduct($productId);

    $viewPath = 'tenancy.industry.advertising.product.show.' . $product->vendor . '.standard';

    return view($viewPath, compact('product'));
})->name('product.show');
