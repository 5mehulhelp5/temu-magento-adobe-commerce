<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\DataProvider\Variants;

class Item
{
    private string $skuId;
    private string $sku;
    private string $identifier;
    private int $qty;
    private float $price;
    private string $currency;
    private array $images;
    private array $variationAttributes;
    private array $packageWeight;
    private array $packageDimensions;

    public function setSkuId(string $skuId): void
    {
        $this->skuId = $skuId;
    }

    public function getSkuId(): string
    {
        return $this->skuId;
    }

    public function setSku(string $sku): void
    {
        $this->sku = $sku;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setQty(int $qty)
    {
        $this->qty = $qty;
    }

    public function getQty(): int
    {
        return $this->qty;
    }

    public function setVariationAttributes(array $variationAttributes)
    {
        $this->variationAttributes = $variationAttributes;
    }

    public function getVariationAttributes(): array
    {
        return $this->variationAttributes;
    }

    public function getPackageWeight(): array
    {
        return $this->packageWeight;
    }

    public function setPackageWeight(array $packageWeight): void
    {
        $this->packageWeight = $packageWeight;
    }

    public function setPackageDimensions(array $packageDimensions): void
    {
        $this->packageDimensions = $packageDimensions;
    }

    public function getPackageDimensions(): array
    {
        return $this->packageDimensions;
    }

    public function setImages(array $images): void
    {
        $this->images = $images;
    }

    public function getImages(): array
    {
        return $this->images;
    }
}
