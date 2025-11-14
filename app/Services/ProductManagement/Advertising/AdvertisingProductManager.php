<?php

namespace App\Services\ProductManagement\Advertising;

use App\Models\Product;

interface AdvertisingProductManager
{

    public function indexFullCatalog(): void;

    public function fetchAndUpdateProduct(Product $product): Product;


}
