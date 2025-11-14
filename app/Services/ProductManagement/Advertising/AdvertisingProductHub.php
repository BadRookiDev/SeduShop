<?php

namespace App\Services\ProductManagement\Advertising;

//todo: Every product model should use auditing!!!
use App\Models\Product;

class AdvertisingProductHub
{

    /**
     * Map of vendor name => AdvertisingProductManager instance
     *
     * @var array<string, AdvertisingProductManager>
     */
    private array $advertisingProductManagers;

    public function __construct()
    {
        $this->advertisingProductManagers = [
            'probo' => new ProboProductManager(),
        ];
    }

    public function indexAllProducts($vendor): void
    {
        $this->advertisingProductManagers[$vendor]->indexFullCatalog();
    }

    public function getProduct($productId): Product
    {
        $product = Product::query()->findOrFail($productId);

        $productManager = $this->advertisingProductManagers[$product->vendor];

        $product = $productManager->fetchAndUpdateProduct($product);

        return $product;
    }


}
