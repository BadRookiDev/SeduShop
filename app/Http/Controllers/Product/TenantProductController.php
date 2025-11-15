<?php

namespace App\Http\Controllers\Product;

use App\Facades\AdvertisingProductHub;
use Illuminate\Support\Facades\Response;


class TenantProductController
{
    public function show($productId)
    {
        $product = AdvertisingProductHub::getProduct($productId);
        $productData = $product->getProductManager()->preprocessProduct($product);

        $viewPath = 'tenancy.industry.advertising.product.show.' . $product->vendor . '.standard';

        return view($viewPath, compact('product', 'productData'));
    }

    public function details($productId)
    {
        $product = AdvertisingProductHub::getProduct($productId, true);
        $details = $product->getProductManager()->filterDetails($product);

        return Response::json($details);
    }
}
