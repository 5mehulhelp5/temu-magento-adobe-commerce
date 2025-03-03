<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\AffectedProduct;

class Product
{
    private \M2E\Temu\Model\Product $product;
    private ?\M2E\Temu\Model\Product\VariantSku $variantSku;

    public function __construct(
        \M2E\Temu\Model\Product $product,
        ?\M2E\Temu\Model\Product\VariantSku $variantSku
    ) {
        $this->product = $product;
        $this->variantSku = $variantSku;
    }

    public function getProduct(): \M2E\Temu\Model\Product
    {
        return $this->product;
    }

    public function getVariant(): ?\M2E\Temu\Model\Product\VariantSku
    {
        return $this->variantSku;
    }

    public function isAffectedVariant(): bool
    {
        return $this->variantSku !== null;
    }
}
