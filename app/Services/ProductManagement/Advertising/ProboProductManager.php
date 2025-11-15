<?php

namespace App\Services\ProductManagement\Advertising;

use App\Models\Product;
use App\Services\ApiHelper;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProboProductManager implements AdvertisingProductManager
{
    private string $BASE_URL = 'https://api.proboprints.com';

    public function fetchAndUpdateProduct(Product $product): Product
    {
        $aspect = "fetch probo product $product->vendor_product_id";
        $productUpdatedAt = Carbon::parse($product->catalog_index_data['updated_at'] ?? null);

        if ($product->last_fetch_at && $productUpdatedAt->lt($product->last_fetch_at)) {
            return $product;
        }

        $response = Http::withHeaders(['Authorization' => 'Basic '.config('sedu.api.probo.token')])
            ->retry(4, ApiHelper::makeRetryCallback(100, $aspect))
            ->get("$this->BASE_URL/products/product/$product->vendor_product_id");

        if ($response->failed()) {
            //todo: this function is summoned by a request, we need to log trace id's, tenants.

            Log::error("Failed to fetch product $product->vendor_product_id from Probo API",
                ['status' => $response->status(), 'body' => $response->body(), 'aspect' => $aspect]);

            return $product;
        }

        //todo: ask probo wtf tiering is.
        $product->vendor_product_data = $response->json();
        $product->last_fetch_at = Carbon::now();
        $product->save();

        return $product;
    }

    public function indexFullCatalog(): void
    {
        $page = 1;
        $pages = 1;

        $allData = [];

        // we fetch all probo products, in possibly multiple pages, and merge it in to one array
        while ($page <= $pages) {
            $response = Http::withHeaders(['Authorization' => 'Basic '.config('sedu.api.probo.token')])
                ->retry(5, ApiHelper::makeRetryCallback(1000, "indexing probo products"))
                ->withQueryParameters(['language' => 'nl', 'per_page' => '300', 'page' => $page])
                ->get("$this->BASE_URL/products");

            if ($response->failed()) {
                Log::error('Failed to index products page from Probo API',
                    ['status' => $response->status(), 'body' => $response->body(), 'aspect' => 'indexing probo products', 'page' => $page]);
                break;
            }

            $allData = array_merge($allData, $response->json('data', []));

            $pages = $response->json('meta')['pages'] ?? 1;
            $page += 1;
        }

        //we transform each product of the api to the format of the database products
        $upsertData = collect($allData)->map(function ($item) {
            return [
                'name' => $item['translations']['nl']['title'] ?? Str::replace('-', ' ', $item['code']),
                'industry' => 'advertising',
                'vendor' => 'probo',
                'default_slug' => $item['code'],
                'vendor_product_id' => $item['code'],
                'catalog_index_data' => json_encode($item)
            ];
        })->keyBy('vendor_product_id')->toArray();

        //we fetch all existing products from the db to see which ones need to be filled with id for upsert
        $existingProducts = Product::query()->where('vendor', 'probo')
            ->select('vendor_product_id', 'id')
            ->get();

        //NOTE: the ampersand is needed to modify the upsertData by reference instead of by value
        $existingProducts->each(function ($existingProduct) use (&$upsertData) {
            if (isset($upsertData[$existingProduct->vendor_product_id])) {
                $upsertData[$existingProduct->vendor_product_id]['id'] = $existingProduct->id;
            }
        });

        //we do a bulk upsert to insert new products and update existing ones
        DB::transaction(function () use ($upsertData) {
            $updateKeys = ['name','industry','vendor','default_slug','vendor_product_id','catalog_index_data','last_fetch_at'];
            Product::upsert(array_values($upsertData), uniqueBy: ['id'], update: $updateKeys);
        });
    }

    public function preprocessProduct(Product $product): array
    {
        // keep only top level options and their direct children; strip deeper descendants
        $data = $product->vendor_product_data;

        $filteredOptions = collect($data['options'])->filter(function ($option) {
            return $option['code'] !== 'accessories-cross-sell';
        })->map(function (mixed $top, int $key) {
            $newChildren = [];

            foreach ($top['children'] ?? [] as $child) {
                $child['children'] = [];
                $newChildren[] = $child;
            }
            $top['children'] = $newChildren;

            return $top;
        })->toArray();

        $accessoriesParent = Arr::first($data['options'], function (array $value, int $key) {
            return $value['code'] === 'accessories-cross-sell';
        });

        $accessories = collect($accessoriesParent['children'] ?? [])->filter(function ($option) {
            return $option['type_code'] === 'cross_sell_pc';
        });

        return [
            'options' => $filteredOptions,
            'accessories' => $accessories->toArray(),
        ];
    }

    public function filterDetails(Product $product): array
    {
        // return ONLY the stripped second level option data, keyed by path parentCode.firstLevelChildCode
        $mapping = [];

        foreach ($product->vendor_product_data['options'] ?? [] as $top) {
            foreach ($top['children'] ?? [] as $child) {
                if (isset($child['children']) && is_array($child['children']) && count($child['children']) > 0) {
                    $mapping[ $top['code'] . '__' . $child['code']] = $child['children'];
                }
            }
        }

        return $mapping;
    }
}
