<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\VariantSku;

class OnlineData
{
    private int $variantId;
    private int $qty;
    private float $price;
    private ?string $sku;
    private int $status;

    public function __construct(
        int $variantId,
        int $qty,
        float $price,
        ?string $sku,
        int $status
    ) {
        $this->variantId = $variantId;
        $this->qty = $qty;
        $this->price = $price;
        $this->sku = $sku;
        $this->status = $status;
    }

    public function getVariantId(): int
    {
        return $this->variantId;
    }

    public function getQty(): int
    {
        return $this->qty;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function getStatus(): int
    {
        return $this->status;
    }
}
