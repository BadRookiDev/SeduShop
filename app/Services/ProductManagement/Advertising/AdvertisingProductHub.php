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

    public function getProduct($productId, $avoidApiCalls = false): Product
    {
        $product = Product::query()->findOrFail($productId);

        if ($avoidApiCalls) {
            return $product;
        }

        $productManager = $this->advertisingProductManagers[$product->vendor];

        return $productManager->fetchAndUpdateProduct($product);
    }

    public function getProductManager($vendor): AdvertisingProductManager
    {
        return $this->advertisingProductManagers[$vendor];
    }


}
