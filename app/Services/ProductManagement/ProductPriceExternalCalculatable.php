<?php

namespace App\Services\ProductManagement;

interface ProductPriceExternalCalculatable
{
    public function calculateExternalPrice(array $productData, int $productId): array;

    public function getCalculationRequestRules(): array;

}
