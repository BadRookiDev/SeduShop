<?php

namespace App\Http\Controllers\Product;

use App\Facades\AdvertisingProductHub;
use App\Http\Requests\Product\RetrieveProductPriceRequest;
use App\Services\ProductManagement\ProductPriceExternalCalculatable;
use Illuminate\Support\Facades\Response;


class TenantProductController
{
    public function show($productId)
    {
        $product = AdvertisingProductHub::getProduct($productId);
        $productData = $product->getProductManager()->preprocessProduct($product);

        $viewPath = 'tenancy.industry.advertising.product.show.' . $product->vendor . '.classic';

        return view($viewPath, compact('product', 'productData'));
    }

    public function details($productId)
    {
        $product = AdvertisingProductHub::getProduct($productId, true);
        $details = $product->getProductManager()->filterDetails($product);

        return Response::json($details);
    }

    public function getPrice(int $productId, RetrieveProductPriceRequest $request){
        /** @var ProductPriceExternalCalculatable $productManager */
        $productManager = AdvertisingProductHub::getProductManager($request->get('vendor'));

        $data = $request->all();
        unset($data['vendor']);

        return Response::json($productManager->calculateExternalPrice($data, $productId));
    }
}
