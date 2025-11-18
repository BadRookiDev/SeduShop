<?php

namespace App\Models;

use App\Facades\AdvertisingProductHub;
use App\Services\ProductManagement\Advertising\AdvertisingProductManager;
use Illuminate\Database\Eloquent\Model;

//todo: product_locale_override table (multiple columns are overridable per locale: name, categories and default_slug, product_data_override) [this is central db]
//todo: product_tenant_locale_override table (multiple columns are overridable per tenant, name, categories and default_slug, product_data_override)

//todo: product_tags table (with locale_field)

class Product extends Model
{
    protected $connection = 'pgsql';


    private static $imageJsonKeys = [
        'probo' => 'images.*.url',
    ];

    /**
     * Cast JSON columns to arrays so decoded values are available as PHP arrays.
     */
    protected $casts = [
        'catalog_index_data'    => 'array',
        'vendor_product_data'   => 'array',
        'product_data_override' => 'array',
    ];

    public function toQuickSearchResult(): array
    {
        $imageUrl = $this->getPrimaryImageUrlAttribute();

        return [
            'id'   => $this->id,
            'name' => $this->name,
            'default_slug' => $this->default_slug,
            'imageUrl' => $imageUrl
        ];
    }

    public function getProductManager(): AdvertisingProductManager
    {
        return AdvertisingProductHub::getProductManager($this->vendor);
    }

    public function getPrimaryImageUrlAttribute()
    {
        //todo: add primary_image_url to product table (and override tables), and populate it during first fetch.

        $imageUrlJsonKey = self::$imageJsonKeys[$this->vendor];

        $imageUrl = data_get($this->product_data_override, $imageUrlJsonKey)
            ?? data_get($this->vendor_product_data, $imageUrlJsonKey);

        if (is_array($imageUrl)) {
            $imageUrl = $imageUrl[0] ?? "https://picsum.photos/300/300"; //todo: change placeholder image
        }
        return $imageUrl ?? "https://picsum.photos/300/300";
    }
}
