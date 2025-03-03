<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Product\VariantSku;

class VariantSkuCollection
{
    /** @var \M2E\Temu\Model\Channel\Product\VariantSku[] */
    private array $variants = [];

    public function empty(): bool
    {
        return empty($this->variants);
    }

    public function has(string $skuId): bool
    {
        return isset($this->variants[$skuId]);
    }

    public function add(\M2E\Temu\Model\Channel\Product\VariantSku $variantSku): void
    {
        $this->variants[$variantSku->getSkuId()] = $variantSku;
    }

    public function get(string $skuId): \M2E\Temu\Model\Channel\Product\VariantSku
    {
        return $this->variants[$skuId];
    }

    public function remove(string $skuId): void
    {
        unset($this->variants[$skuId]);
    }

    /**
     * @return \M2E\Temu\Model\Channel\Product\VariantSku[]
     */
    public function getAll(): array
    {
        return array_values($this->variants);
    }

    public function findProductSkuBySkuId(string $skuId): ?\M2E\Temu\Model\Channel\Product\VariantSku
    {
        return $this->variants[$skuId] ?? null;
    }
}
