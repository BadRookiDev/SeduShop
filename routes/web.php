<?php

use Illuminate\Support\Facades\Route;

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {

        Route::get('/', function () {
            return view('welcome');
        });

        Route::get('/create-tenant/{domain}', function ($domain) {
            $tenant = \App\Models\Tenancy\Tenant::create([
                'id' => 'tenant_' . \Illuminate\Support\Str::random(8),
                'tenant_name' => 'Tenant ' . \Illuminate\Support\Str::random(8),
                'tenant_industry' => 'advertising',
                'tenancy_db_name' => 'tenant1',
                'tenancy_db_driver' => 'pgsql',
            ]);
            $tenant->domains()->create([
                'domain' => $domain
            ]);
        });

        Route::get('/probo', function () {
            AdvertisingProductHub::indexAllProducts('probo');
        });

        Route::get('/probo/{productId}', function ($productId) {
            $product = AdvertisingProductHub::getProduct($productId);
            dd($product, $product->vendor_product_data);
        });

    });
}
