<?php

namespace App\Http\Controllers\Product;

use App\Http\Requests\Product\ProductQuickSearchRequest;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductSearchController
{
    public function quickSearchProduct(ProductQuickSearchRequest $request)
    {
        $search = Str::lower($request->get('q'));
        //todo: get banned products for tenant

        //todo: product categories query apart

        $productNames = Product::query()
            ->where('industry', 'advertising') //in the future we get this from the current tenant (industry field needed for performance)
            ->where('name', 'ILIKE', "%{$search}%")
            ->get();

        $productNames = $productNames
            ->sortByDesc(function (Product $product) use ($search) {
                return Str::startsWith(Str::lower($product->name), $search) ? 1 : 0;
            })
            ->sortBy('name')
            ->values();

        $quickSearchResults = $productNames
            ->take(12)
            ->map(function (Product $product) {
                return $product->toQuickSearchResult();
            });

        return response()->json([
            'matchCount' => $productNames->count(),
            'results' => $quickSearchResults,
            //product categories here
        ]);
    }



}
